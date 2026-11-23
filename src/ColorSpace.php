<?php

namespace Kiritokatklian\LaravelColorPalette;

/**
 * Enum ColorSpace
 *
 * @package LaravelColorPalette
 */
enum ColorSpace: int
{
    /**
     * The sRGB color space.
     */
    case SRGB = 0;

    /**
     * The Display P3 color space.
     */
    case DisplayP3 = 1;

    /**
     * The Adobe RGB (1998) color space.
     */
    case AdobeRGB = 2;

    /**
     * The ProPhoto RGB color space.
     */
    case ProPhotoRGB = 3;

    /**
     * The Rec. 2020 color space.
     */
    case Rec2020 = 4;
}
