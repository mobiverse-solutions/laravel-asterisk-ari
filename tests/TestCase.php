<?php

namespace Mobiverse\LaravelAsteriskAri\Tests;

use Mobiverse\LaravelAsteriskAri\LaravelAsteriskAriServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @package Mobiverse\LaravelAsteriskAri
 * Class TestCase
 */
class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelAsteriskAriServiceProvider::class,
        ];
    }
}
