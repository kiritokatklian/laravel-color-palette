<?php

namespace Kiritokatklian\LaravelColorPalette;

use Closure;
use ColorThief\ColorThief;
use ColorThief\Image\Adapter\ImagickAdapter;
use ColorThief\Image\ImageLoader;
use Imagick;

/**
 * Class ColorPaletteClient
 *
 * @package LaravelColorPalette
 */
class ColorPaletteClient
{
    /**
     * The D50 red, green and blue colorants of each recognized color space.
     *
     * @var float[][]
     */
    private const COLORANTS = [
        ColorSpace::DisplayP3->value => [0.5151, 0.2412, -0.0011, 0.2920, 0.6922, 0.0419, 0.1571, 0.0666, 0.7841],
        ColorSpace::AdobeRGB->value => [0.6097, 0.3111, 0.0195, 0.2053, 0.6257, 0.0609, 0.1492, 0.0632, 0.7446],
        ColorSpace::ProPhotoRGB->value => [0.7977, 0.2880, 0.0000, 0.1352, 0.7119, 0.0000, 0.0313, 0.0001, 0.8250],
        ColorSpace::Rec2020->value => [0.6735, 0.2790, -0.0019, 0.1657, 0.6753, 0.0300, 0.1251, 0.0456, 0.7968],
    ];

    /**
     * The largest colorant difference that still matches a color space.
     *
     * @var float
     */
    private const COLORANT_TOLERANCE = 0.005;

    /**
     * Use the median cut algorithm to cluster similar colors.
     *
     * @bug Function does not always return the requested amount of colors. It can be +/- 2.
     *
     * @param mixed      $sourceImage   Path/URL to the image, GD resource, Imagick instance, or image as binary string
     * @param int        $quality       1 is the highest quality. There is a trade-off between quality and speed.
     *                                  The bigger the number, the faster the palette generation but the greater the
     *                                  likelihood that colors will be missed.
     * @param ?int[] $area              [x,y,w,h] It allows you to specify a rectangular area in the image in order to get
     *                                  colors only for this area. It needs to be an associative array with the
     *                                  following keys:
     *                                  $area['x']: The x-coordinate of the top left corner of the area. Default to 0.
     *                                  $area['y']: The y-coordinate of the top left corner of the area. Default to 0.
     *                                  $area['w']: The width of the area. Default to image width minus x-coordinate.
     *                                  $area['h']: The height of the area. Default to image height minus y-coordinate.
     *
     * @return Color
     */
    public function getColor(mixed $sourceImage, int $quality = 10, ?array $area = null): Color
    {
        return $this->sample($sourceImage, function ($image, ColorSpace $colorSpace) use ($quality, $area) {
            return new Color(ColorThief::getColor($image, $quality, $area), colorSpace: $colorSpace);
        });
    }

    /**
     * Use the median cut algorithm to cluster similar colors.
     *
     * @bug Function does not always return the requested amount of colors. It can be +/- 2.
     *
     * @param mixed     $sourceImage Path/URL to the image, GD resource, Imagick instance, or image as binary string
     * @param int       $colorCount  It determines the size of the palette; the number of colors returned.
     * @param int       $quality     1 is the highest quality.
     * @param ?int[]    $area        [x,y,w,h]
     *
     * @return \ColorThief\Color[]|int[]|string[]|null|Color[]
     */
    public function getPalette(mixed $sourceImage, int $colorCount = 10, int $quality = 10, ?array $area = null): ?array
    {
        return $this->sample($sourceImage, function ($image, ColorSpace $colorSpace) use ($colorCount, $quality, $area) {
            $palette = ColorThief::getPalette($image, $colorCount, $quality, $area);

            if (!$palette) {
                return $palette;
            }

            return array_map(function ($value) use ($colorSpace) {
                return new Color($value, colorSpace: $colorSpace);
            }, $palette);
        });
    }

    /**
     * Calls the closure with the loaded image and the color space of its embedded color profile.
     *
     * @template TResult
     *
     * @param mixed                               $sourceImage
     * @param Closure(mixed, ColorSpace): TResult $callback
     *
     * @return TResult
     */
    protected function sample(mixed $sourceImage, Closure $callback): mixed
    {
        $loader = new ImageLoader();

        if (!ImagickAdapter::isAvailable() || $loader->isGdImage($sourceImage) || $loader->isGmagick($sourceImage)) {
            return $callback($sourceImage, ColorSpace::SRGB);
        }

        if ($sourceImage instanceof Imagick) {
            return $callback($sourceImage, $this->colorSpaceOf($sourceImage));
        }

        $image = $loader->setPreferredAdapter('Imagick')->load($sourceImage)->getResource();

        if (!$image instanceof Imagick) {
            return $callback($sourceImage, ColorSpace::SRGB);
        }

        try {
            return $callback($image, $this->colorSpaceOf($image));
        } finally {
            $image->clear();
        }
    }

    /**
     * Returns the color space of the image's embedded color profile.
     *
     * @param Imagick $image
     *
     * @return ColorSpace
     */
    protected function colorSpaceOf(Imagick $image): ColorSpace
    {
        $profile = $image->getImageProfiles('icc')['icc'] ?? null;
        $colorants = is_string($profile) ? $this->colorantsIn($profile) : null;

        if ($colorants === null) {
            return ColorSpace::SRGB;
        }

        foreach (self::COLORANTS as $colorSpace => $expectedColorants) {
            $differences = array_map(function (float $colorant, float $expectedColorant) {
                return abs($colorant - $expectedColorant);
            }, $colorants, $expectedColorants);

            if (max($differences) <= self::COLORANT_TOLERANCE) {
                return ColorSpace::from($colorSpace);
            }
        }

        return ColorSpace::SRGB;
    }

    /**
     * Returns the X, Y and Z values of the red, green and blue colorants in the ICC profile.
     *
     * @param string $profile
     *
     * @return ?array{float, float, float, float, float, float, float, float, float}
     */
    protected function colorantsIn(string $profile): ?array
    {
        if (strlen($profile) < 132) {
            return null;
        }

        $tagCount = $this->unsignedIntegerAt($profile, 128);
        $colorants = [];

        for ($index = 0; $index < $tagCount; $index++) {
            $entryOffset = 132 + $index * 12;

            if (strlen($profile) < $entryOffset + 12) {
                return null;
            }

            $signature = substr($profile, $entryOffset, 4);
            $offset = $this->unsignedIntegerAt($profile, $entryOffset + 4);

            if (!in_array($signature, ['rXYZ', 'gXYZ', 'bXYZ'], true)) {
                continue;
            }

            if (strlen($profile) < $offset + 20 || substr($profile, $offset, 4) !== 'XYZ ') {
                return null;
            }

            $colorants[$signature] = [
                $this->fixedPointNumberAt($profile, $offset + 8),
                $this->fixedPointNumberAt($profile, $offset + 12),
                $this->fixedPointNumberAt($profile, $offset + 16),
            ];
        }

        if (!isset($colorants['rXYZ'], $colorants['gXYZ'], $colorants['bXYZ'])) {
            return null;
        }

        return [...$colorants['rXYZ'], ...$colorants['gXYZ'], ...$colorants['bXYZ']];
    }

    /**
     * Returns the big-endian unsigned 32-bit integer at the offset of the ICC profile.
     *
     * @param string $profile
     * @param int    $offset
     *
     * @return int
     */
    protected function unsignedIntegerAt(string $profile, int $offset): int
    {
        return (ord($profile[$offset]) << 24) | (ord($profile[$offset + 1]) << 16) | (ord($profile[$offset + 2]) << 8) | ord($profile[$offset + 3]);
    }

    /**
     * Returns the signed 15.16 fixed-point number at the offset of the ICC profile.
     *
     * @param string $profile
     * @param int    $offset
     *
     * @return float
     */
    protected function fixedPointNumberAt(string $profile, int $offset): float
    {
        $value = $this->unsignedIntegerAt($profile, $offset);

        return ($value > 0x7FFFFFFF ? $value - 0x100000000 : $value) / 0x10000;
    }
}
