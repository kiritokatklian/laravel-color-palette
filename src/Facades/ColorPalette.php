<?php

namespace Kiritokatklian\LaravelColorPalette\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ColorPaletteFacade
 *
 * @package LaravelColorPalette
 */
class ColorPalette extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ColorPalette';
    }
}
