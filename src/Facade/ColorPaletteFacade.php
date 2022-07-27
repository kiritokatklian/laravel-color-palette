<?php

namespace Kiritokatklian\LaravelColorPalette\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class ColorPaletteFacade
 *
 * @package LaravelColorPalette
 */
class ColorPaletteFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ColorPaletteClient';
    }
}
