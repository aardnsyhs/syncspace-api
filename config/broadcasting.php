<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "reverb", "ably", "pusher", "log", "null"
    |
    | For local development, "reverb" is recommended (free, self-hosted).
    | For production, "ably" or "pusher" are managed cloud options.
    | Set BROADCAST_CONNECTION in your .env to switch between them.
    |
    */
  'default' => env('BROADCAST_CONNECTION', 'reverb'),

  'connections' => [

    'reverb' => [
      'driver' => 'reverb',
      'key' => env('REVERB_APP_KEY'),
      'secret' => env('REVERB_APP_SECRET'),
      'app_id' => env('REVERB_APP_ID'),
      'options' => [
        'host' => env('REVERB_HOST'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
        'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
      ],
      'client_options' => [

      ],
    ],

    'ably' => [
      'driver' => 'ably',
      'key' => env('ABLY_KEY'),
    ],

    'log' => [
      'driver' => 'log',
    ],

    'null' => [
      'driver' => 'null',
    ],

  ],

];
