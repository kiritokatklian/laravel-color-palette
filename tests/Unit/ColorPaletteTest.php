<?php

namespace Kiritokatklian\LaravelColorPalette\Tests\Unit;

use Kiritokatklian\LaravelColorPalette\Facade\ColorPaletteFacade;
use Kiritokatklian\LaravelColorPalette\Tests\TestCase;

final class ColorPaletteTest extends TestCase
{
    /**
     * The color palette expected from strawberry.jpg
     *
     * @var array|string[]
     */
    private array $expectedStrawberryColorPalette = [
        '#d63e37',
        '#92231b',
        '#191813',
        '#f8c9cd',
        '#7dab53',
        '#446826',
        '#5d110c',
        '#f794a5',
        '#90799b',
        '#314956',
    ];

    /**
     * The RGB expected from strawberry.jpg
     *
     * @var array|string[]
     */
    private array $expectedStrawberryColorRGB = [
        220,
        85,
        80,
    ];

    /**
     * Test can generate color palette from image.
     *
     * @return void
     * @test
     */
    public function test_can_generate_color_palette_from_image(): void
    {
        // Arrange
        $imagePath = __DIR__ . '/../images/strawberry.jpeg';

        // Act
        $actualColorPalette = ColorPaletteFacade::getPalette($imagePath);

        // Assert
        $this->assertEquals($this->expectedStrawberryColorPalette, $actualColorPalette);
    }

    /**
     * Test can generate rgb from image.
     *
     * @return void
     * @test
     */
    public function test_can_generate_rgb_from_image(): void
    {
        // Arrange
        $imagePath = __DIR__ . '/../images/strawberry.jpeg';

        // Act
        $actualColors = ColorPaletteFacade::getColor($imagePath);
        $actualRGB = $actualColors->toRgb();

        // Assert
        $this->assertEquals($this->expectedStrawberryColorRGB, $actualRGB);
    }
}
