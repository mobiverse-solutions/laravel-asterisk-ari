# <div align="center">Laravel Asterisk ARI</div>
<div align="center">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![GitHub Issues](https://img.shields.io/github/issues/mobiverse-solutions/The-Documentation-Compendium.svg)](https://github.com/JetstreamAfrica/raven/issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/mobiverse-solutions/The-Documentation-Compendium.svg)](https://github.com/JetstreamAfrica/raven/pulls)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)

</div>

---

<p align="center"> Laravel Asterisk ARI Package
    <br> 
</p>

## 📝 Table of Contents

- [About](#about)
- [Getting Started](#getting_started)
- [Components](#components)
- [Usage](#usage)
- [Built Using](#built_using)
- [Authors](#authors)

## 🧐 About <a name = "about"></a>
Laravel Asterisk ARI is a Laravel package which is a wrapper around the asterisk-ari-php package by Lukas Stermann.

## 🏁 Getting Started <a name = "getting_started"></a>

### Prerequisites
Before setting up this project on your local machine, you need to meet the following requirements:

1. PHP v8.0
2. Composer v2.3.2

NB: versions may vary

## 🎈 Usage <a name="usage"></a>
This package is not available on Packagist. Hence, to use this package in your laravel project:
1. Add the following sections to your project's `composer.json` file:

    ```json
    {
      "require": {
        "gashey/laravel-asterisk-ari": "dev-master"
      }
    }
    ```
    ```json
    {
      "repositories": [
        { 
          "type": "git", 
          "url": "https://github.com/gashey/asterisk-ari-php.git" 
        },
        {
          "type": "git",
          "url": "https://github.com/gashey/laravel-asterisk-ari.git"
        }
      ]
    }
    ```

2. The above section points to  a private repository, hence you would need to provide composer with a personal access
   token from GitHub to successfully pull the contents of the repo.
   For local development, it is recommended that you create
   a gitignored `auth.json` file with the following content, in the root of your project:

    ```json
    {
      "github-oauth": {
        "github.com": "token"
      }
    }
    ```

3. Then, proceed to run the following command to resolve the dependency:
    ```bash
    composer update
    ```

4. For remote access, you can set up an environment secret with name `COMPOSER_AUTH`, which will contain the JSON formatted
   content of the `auth.json` file:
    ```bash
    COMPOSER_AUTH='{"github-oauth":{"github.com":"token"}}'
    ```

5. After successfully adding this package to your project, you will need to publish the config file where you can
   set up your credentials for Asterisk ARI access. The following command will allow you to do that:
    ```bash
    php artisan vendor:publish laravel-asterisk-ari-config
    ```

6. The config file `laravel-asterisk-ari.php`, will be published to your config directory `./config`. Customize
   it to suit your needs.

7. Create your event handlers. They must implement the IPamiEventMessageHandler interface. See an example below:
   ```php
    use Illuminate\Support\Facades\Log;
    use InvalidArgumentException;
    use OpiyOrg\AriClient\StasisApplicationInterface;
    use OpiyOrg\AriClient\Client\Rest\Resource\Channels;
    use OpiyOrg\AriClient\Exception\AsteriskRestInterfaceException;
    use OpiyOrg\AriClient\Model\Message\Event\{ChannelHangupRequest, ChannelUserevent, StasisEnd, StasisStart};

    final class ExampleStasisApp implements StasisApplicationInterface
    {
         private Channels $ariChannelsClient;

         /**
          * MyExampleStasisApp constructor.
          *
          * @param Channels $ariChannelsClient REST client for
          * the 'Channels' resource of the Asterisk REST Interface
          */
          public function __construct(Channels $ariChannelsClient)
          {
             $this->ariChannelsClient = $ariChannelsClient;
          }

          /**
           * 'StasisStart' is the first event that is triggered by Asterisk
           * when a channel enters your Stasis application.
           *
           * @param StasisStart $stasisStart The Asterisk StasisStart event
           *
           * @throws AsteriskRestInterfaceException in case the REST request fails.
           */
           public function onAriEventStasisStart(StasisStart $stasisStart): void
           {
              $channelId = $stasisStart->getChannel()->getId();
              $msg = sprintf(
                   "The channel '%s' has entered the StasisApp.\n",
                  $channelId
              );
              Log::debug($msg);

              /*
               * Now we make a call to the Asterisk REST Interface in order
               * to get the list of active channels on your Asterisk instance.
               */
              foreach ($this->ariChannelsClient->list() as $activeChannel) {
                $msg = sprintf(
                    "The channel (id: '%s' state: '%s') is active on your Asterisk server.\n",
                    $activeChannel->getId(),
                    $activeChannel->getState()
                );
                Log::debug($msg);
              }

              // Go back to continue call processing on the dialplan
              $this->ariChannelsClient->continueInDialPlan($channelId, []);
           }

        /**
        * A default event handler for channels that have been hung up.
        *
        * @param ChannelHangupRequest $channelHangupRequest The Asterisk event
        */
        public function onAriEventChannelHangupRequest(
            ChannelHangupRequest $channelHangupRequest
        ): void {
            $msg = sprintf(
                "This is the default hangup handler triggered by channel '%s' :-)\n",
                $channelHangupRequest->getChannel()->getId()
            );
            Log::debug($msg);
        }

        /**
        * Notification that a channel has left your Stasis application.
        * Do some clean ups in your database here for example.
        *
        * @param StasisEnd $stasisEnd The Asterisk StasisEnd event
        */
        public function onAriEventStasisEnd(StasisEnd $stasisEnd): void
        {
            $msg = sprintf(
                "The channel '%s' has left your example app.\n",
                $stasisEnd->getChannel()->getId()
            );
            Log::debug($msg);
        }
   }
    ```
   
8. Wire up your stasis app in the "stasis_app" section of the config. See an example below:
   ```php
   'stasis_app' => [
        'class' => 'App\\Library\\BillingStasisApp',
        'name' => 'BillingStasisApp',
    ],
   ```


## ⛏️ Built Using <a name = "built_using"></a>
- [PHP](https://www.php.net/) - Language
- [asterisk-ari-php](https://github.com/opiy-org/asterisk-ari-php) Library

## ✍️ Authors <a name = "authors"></a>
- [@gashey](https://github.com/gashey) - Initial work