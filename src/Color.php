<?php

namespace Kiritokatklian\LaravelColorPalette;

use Exception;

/**
 * Class Color
 *
 * @package LaravelColorPalette
 */
class Color
{
    /**
     * The red value.
     *
     * @var int $r
     */
    public int $r;

    /**
     * The green value.
     *
     * @var int $g
     */
    public int $g;

    /**
     * The blue value.
     *
     * @var int $b
     */
    public int $b;

    /**
     * The alpha value.
     *
     * @var int $a
     */
    public int $a;

    /**
     * Construct new Color.
     *
     * @param \ColorThief\Color|int|int[]|string|null   $color
     * @param bool                                      $short
     */
    public function __construct(\ColorThief\Color|array|int|string|null $color = 0x000000, bool $short = false)
    {
        if (is_numeric($color) || is_string($color)) {
            if (is_string($color)) {
                $color = preg_replace('/[^\dA-Fa-f]/', '', $color);
                $color = hexdec($color);
            }

            if ($short) {
                $this->r = (($color >> 0x8) & 0xF) * 0x11;
                $this->g = (($color >> 0x4) & 0xF) * 0x11;
                $this->b = ($color & 0xF) * 0x11;
                $this->a = (($color >> 0xC) & 0xF) * 0x11;
            } else {
                $this->r = ($color >> 0x10) & 0xFF;
                $this->g = ($color >> 0x8) & 0xFF;
                $this->b = $color & 0xFF;
                $this->a = ($color >> 0x18) & 0xFF;
            }
        } elseif (is_array($color)) {
            [$this->r, $this->g, $this->b] = $color;
            $this->a = count($color) > 3 ? $color[3] : 0;
        } elseif ($color instanceof \ColorThief\Color) {
            $this->r = $color->getRed();
            $this->g = $color->getGreen();
            $this->b = $color->getBlue();
            $this->a = 0;
        }
    }

    /**
     * Some useful magic getters.
     *
     * @param string $property
     *
     * @return mixed
     * @throws Exception
     */
    public function __get(string $property)
    {
        $method = 'to' . ucfirst($property);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        switch ($property) {
            case 'red':   case 'getRed':
                return $this->r;
            case 'green': case 'getGreen':
                return $this->g;
            case 'blue':  case 'getBlue':
                return $this->b;
            case 'alpha': case 'getAlpha':
                return $this->a;
        }

        throw new Exception("Property $property does not exist");
    }

    /**
     * Magic method, alias for toHexString.
     *
     * @see  toHexString()
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toHexString();
    }

    /**
     * Returns the difference between this color and the provided color using simple pythagoras *without* sqrt.
     *
     * @param int|Color $color
     *
     * @return int
     */
    public function getDiff(int|Color $color): int
    {
        if (!$color instanceof Color) {
            $color = new Color($color);
        }

        return pow($this->r - $color->r, 2)
            + pow($this->g - $color->g, 2)
            + pow($this->b - $color->b, 2);
    }

    /**
     * Whether this color has an alpha value > 0.
     *
     * @see isTransparent for full transparency
     *
     * @return bool
     */
    public function hasAlpha(): bool
    {
        return (bool) $this->a;
    }

    /**
     * Detect Transparency using GD Returns true if the provided color has zero opacity.
     *
     * @return bool
     */
    public function isTransparent(): bool
    {
        return $this->a === 127;
    }

    /**
     * Returns an array containing int values for red, green, blue and alpha.
     *
     * @return int[]
     */
    public function toArray(): array
    {
        return [$this->r, $this->g, $this->b, $this->a];
    }

    /**
     * Returns an array containing int values for red, green and blue.
     *
     * @return int[]
     */
    public function toRgb(): array
    {
        return [$this->r, $this->g, $this->b];
    }

    /**
     * Returns an array containing int values for red, green and blue and a double for alpha.
     *
     * @return int[]
     */
    public function toRgba(): array
    {
        return [$this->r, $this->g, $this->b, 1 - $this->a / 0x100];
    }

    /**
     * Returns an int representing the color
     * defined by the red, green and blue values
     *
     * @return int
     */
    public function toInt(): int
    {
        return ($this->r << 0x10) | ($this->g << 0x8) | $this->b;
    }

    /**
     * Render 6-digit hexadecimal string representation like '#ABCDEF'.
     *
     * @param string $prefix defaults to '#'
     *
     * @return string
     */
    public function toHexString(string $prefix = '#'): string
    {
        return $prefix . str_pad(dechex($this->toInt()), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Render 3-integer decimal string representation like 'rgb(123,0,20)'.
     *
     * @param string $prefix defaults to 'rgb'
     *
     * @return string
     */
    public function toRgbString(string $prefix = 'rgb'): string
    {
        return $prefix  . '('
            . $this->r . ','
            . $this->g . ','
            . $this->b . ')';
    }

    /**
     * Render 3-integer decimal string representation like 'rgba(123,0,20,0.5)'.
     *
     * @param string $prefix         defaults to 'argb'
     * @param int    $alphaPrecision max alpha digits, default 2
     *
     * @return string
     */
    public function toRgbaString(string $prefix = 'rgba', int $alphaPrecision = 2): string
    {
        return $prefix  . '('
            . $this->r . ','
            . $this->g . ','
            . $this->b . ','
            . round(1 - $this->a / 0x100, $alphaPrecision) . ')';
    }
}
