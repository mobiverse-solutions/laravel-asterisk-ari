<?php

namespace Mobiverse\LaravelAsteriskAri\Tests\Unit;

use Illuminate\Support\Facades\Config;

test('confirm environment is set to testing', function () {
    expect(config('app.env'))->toBe('testing');
});

test('console command', function () {
    Config::set('laravel-asterisk-ari.stasis_app.class', '\Mobiverse\LaravelAsteriskAri\Tests\Unit\TestStasisApp');
    Config::set('laravel-asterisk-ari.stasis_app.name', 'LaravelStasisApp');
    Config::set('laravel-asterisk-ari.asterisk_ari.user', 'laravel');
    Config::set('laravel-asterisk-ari.asterisk_ari.password', '20cc80e7341c89c143a86321cdcf9255');
    Config::set('laravel-asterisk-ari.asterisk_ari.host', '10.8.0.50');
    Config::set('laravel-asterisk-ari.asterisk_ari.port', '8088');
    Config::set('laravel-asterisk-ari.enable_ari_debug_mode', true);
    Config::set('laravel-asterisk-ari.enable_periodic_test_event', true);
    $this->artisan('stasis-app:start')->assertExitCode(0);
});