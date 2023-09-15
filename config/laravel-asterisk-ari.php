<?php

return [
    'asterisk_ari' => [
        'user' => 'asterisk',
        'password' => 'asterisk',
        'host' => '127.0.0.1',
        'port' => 8088,
    ],

    'stasis_app' => [
        'class' => 'Replace-With-Stasis-App-Class',
        'name' => 'MyLaravelStasisApp',
    ],

    // WARNING: ensure you set this set to false in production environment
    'enable_periodic_test_event' => false,

    // Put the client into debug mode and log debug messages
    'enable_ari_debug_mode' => false,
];
