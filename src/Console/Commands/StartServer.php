<?php

namespace Mobiverse\LaravelAsteriskAri\Console\Commands;

use Illuminate\Console\Command;
use OpiyOrg\AriClient\Client\Rest\Resource\{Channels as AriChannelsRestResourceClient,
    Events as AriEventsResourceRestClient};
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mobiverse\LaravelAsteriskAri\Exceptions\StasisAppClassMissingException;
use OpiyOrg\AriClient\Client\Rest\Settings as AriRestClientSettings;
use OpiyOrg\AriClient\Client\WebSocket\{Factory as AriWebSocketClientFactory,
    Settings as AriWebSocketClientSettings};
use Throwable;

/**
 * @package Mobiverse\LaravelAsteriskAri
 * Class StartServer
 */
class StartServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stasis-app:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the stasis app';

    private string $stasisClass;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->stasisClass = config('laravel-asterisk-ari.stasis_app.class', 'StasisApp');

        if (!class_exists($this->stasisClass)) {
            Log::error('Class ' . $this->stasisClass . ' does not exist');
            throw new StasisAppClassMissingException('Class ' . $this->stasisClass . ' does not exist');
        }

        $stasis_app_name = config('laravel-asterisk-ari.stasis_app.name');

        $ariRestClientSettings = new AriRestClientSettings(
            config('laravel-asterisk-ari.asterisk_ari.user'),
            config('laravel-asterisk-ari.asterisk_ari.password'),
            config('laravel-asterisk-ari.asterisk_ari.host'),
            config('laravel-asterisk-ari.asterisk_ari.port'),
            $stasis_app_name,
        );

        $stasisApp = new $this->stasisClass(
            new AriChannelsRestResourceClient($ariRestClientSettings)
        );

        $ariWebSocketClientSettings = new AriWebSocketClientSettings(
            $ariRestClientSettings->getUser(),
            $ariRestClientSettings->getPassword(),
            $ariRestClientSettings->getHost(),
            $ariRestClientSettings->getPort(),
            $ariRestClientSettings->getAppName(),
        );

        $ariWebSocketClientSettings->setIsInDebugMode(config('laravel-asterisk-ari.enable_ari_debug_mode'));

        $ariWebSocketClientSettings->setErrorHandler(
            static function (string $context, Throwable $throwable) {
                Log::error(
                    "\n\nThis is the error handler, triggered in context '{$context}'. "
                    . "Throwable message: '{$throwable->getMessage()}'\n\n"
                );
            }
        );

        $ariWebSocketClientSettings->setLoggerInterface(Log::getLogger());

        $ariWebSocketClient = AriWebSocketClientFactory::create(
            $ariWebSocketClientSettings,
            $stasisApp
        );

        $ariEventsRestClient = new AriEventsResourceRestClient($ariRestClientSettings);

        if (config('laravel-asterisk-ari.enable_periodic_test_event')) {
            // Trigger an example ChannelUserevent every few seconds to see terminal output
            $ariWebSocketClient->getLoop()->addPeriodicTimer(
                3,
                static function () use ($ariEventsRestClient, $stasis_app_name) {
                    $ariEventsRestClient->userEvent(
                        'ThisEventIsTriggeredByYourself',
                        $stasis_app_name
                    );
                }
            );
        }

        // After finishing configuration, start the web socket client.
        $ariWebSocketClient->start();
    }
}
