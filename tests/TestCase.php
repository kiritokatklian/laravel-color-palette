<?php

namespace Kiritokatklian\LaravelColorPalette\Tests;

use Kiritokatklian\LaravelColorPalette\ColorPaletteServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ColorPaletteServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }
}
