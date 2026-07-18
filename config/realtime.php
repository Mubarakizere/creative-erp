<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Realtime Architecture Feature Flags
    |--------------------------------------------------------------------------
    |
    | These flags control the availability of realtime features globally.
    | Enable or disable features without changing the underlying code.
    |
    */

    'features' => [
        'enabled'               => env('REALTIME_ENABLED', true),
        'presence'              => env('REALTIME_PRESENCE_ENABLED', true),
        'announcements'         => env('REALTIME_ANNOUNCEMENTS_ENABLED', true),
        'toast'                 => env('REALTIME_TOAST_ENABLED', true),
        'dashboard'             => env('REALTIME_DASHBOARD_ENABLED', true),
        'broadcasting'          => env('REALTIME_BROADCASTING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Realtime Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Define the primary provider for realtime events.
    | Supported: "reverb", "pusher", "redis", "ably", "log", "null"
    |
    */

    'provider' => env('REALTIME_PROVIDER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Presence Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for tracking user presence across the application.
    |
    */

    'presence' => [
        // Number of minutes before a user is considered "offline" if no heartbeat is received.
        'offline_timeout' => 5,
        
        // Number of minutes before a user is considered "away" if no activity is detected.
        'away_timeout'    => 15,
    ],
];
