<?php

namespace Kiritokatklian\LaravelColorPalette\Tests;

use Illuminate\Foundation\AliasLoader;
use Kiritokatklian\LaravelColorPalette\ColorPaletteServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $loader = AliasLoader::getInstance();
        $loader->alias('ColorPalette', 'LaravelColorPalette\\Facades\\ColorPalette');
    }

    protected function getPackageProviders($app): array
    {
        $serviceProviders = [
            ColorPaletteServiceProvider::class,
        ];

        return $serviceProviders;
    }

    protected function getEnvironmentSetUp($app)
    {
    }
}
