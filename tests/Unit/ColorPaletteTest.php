<?php

namespace Kiritokatklian\LaravelColorPalette\Tests\Unit;

use Kiritokatklian\LaravelColorPalette\Color;
use Kiritokatklian\LaravelColorPalette\Facades\ColorPalette;
use Kiritokatklian\LaravelColorPalette\Tests\TestCase;

final class ColorPaletteTest extends TestCase
{
    /**
     * Test can construct Color from int.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_int(): void
    {
        // Arrange
        $color = 0x00FF0000; // Red
        $expectedRGB = [
            255,    // Red
            0,
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGB, $actualRGBA);
    }
    /**
     * Test can construct Color from short int.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_short_int(): void
    {
        // Arrange
        $color = 0x0F00; // Red
        $expectedRGB = [
            255,    // Red
            0,
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color, true);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGB, $actualRGBA);
    }

    /**
     * Test can construct Color from int array.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_int_array(): void
    {
        // Arrange
        $color = [
            0,
            255,    // Green
            0,
        ];
        $expectedRGB = [
            0,
            255,    // Green
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGB, $actualRGBA);
    }

    /**
     * Test can construct Color from \ColorThief\Color.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_color_thief_color(): void
    {
        // Arrange
        $color = new \ColorThief\Color(0, 0, 255);
        $expectedRGB = [
            0,
            0,
            255,    // Blue
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGB, $actualRGBA);
    }

    /**
     * Test can construct Color from HEX string.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_hex_string(): void
    {
        // Arrange
        $color = '#FF0000'; // Red
        $expectedRGBA = [
            255,    // Red
            0,
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGBA, $actualRGBA);
    }

    /**
     * Test can construct Color from short HEX string.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_short_hex_string(): void
    {
        // Arrange
        $color = '#00F0'; // Green
        $expectedRGBA = [
            0,
            255,    // Green
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color, true);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGBA, $actualRGBA);
    }

    /**
     * Test can construct Color from non-HEX string.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_non_hex_string(): void
    {
        // Arrange
        $color = '00FF00'; // Green
        $expectedRGBA = [
            0,
            255,    // Green
            0,
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGBA, $actualRGBA);
    }

    /**
     * Test can construct Color from any string.
     *
     * @return void
     * @test
     */
    public function test_can_construct_color_from_any_string(): void
    {
        // Arrange
        // Contains 'e' whose HEX is 14. Because for blue we don't shift the bits, blue's value becomes 14.
        $color = 'test';
        $expectedRGBA = [
            0,
            0,
            14,     // Blue
            1,
        ];

        // Act
        $actualColors = new Color($color);
        $actualRGBA = $actualColors->toRgba();

        // Assert
        $this->assertEquals($expectedRGBA, $actualRGBA);
    }

    /**
     * Test Color constructed from null errors.
     *
     * @return void
     * @test
     */
    public function test_color_constructed_from_null_errors(): void
    {
        $this->expectError();
        // Arrange
        $color = null;

        // Act
        $actualColors = new Color($color);

        // Assert
        $error = $actualColors->toRgba();
    }

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
        $expectedPalette = [
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

        // Act
        $actualColorPalette = ColorPalette::getPalette($imagePath);

        // Assert
        $this->assertEquals($expectedPalette, $actualColorPalette);
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
        $expectedRGB = [
            220,    // Red
            85,     // Green
            80,     // Blue
        ];

        // Act
        $actualColors = ColorPalette::getColor($imagePath);
        $actualRGB = $actualColors->toRgb();

        // Assert
        $this->assertEquals($expectedRGB, $actualRGB);
    }
}
