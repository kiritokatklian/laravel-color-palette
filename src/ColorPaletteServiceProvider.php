<?php

namespace Kiritokatklian\LaravelColorPalette;

use Illuminate\Support\ServiceProvider;

/**
 * Class ColorPaletteClient
 *
 * @package LaravelColorPalette
 */
class ColorPaletteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('ColorPalette', function ($app) {
            return new ColorPaletteClient();
        });
    }
}
