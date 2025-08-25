<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
     |----------------------------------------------------------------------
     | Authentication Guards
     |----------------------------------------------------------------------
     |
     | Keep the standard "web" session guard. Add an "api" guard that uses
     | Sanctum so "auth:sanctum" works cleanly on API routes.
     |
     */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
     |----------------------------------------------------------------------
     | User Providers
     |----------------------------------------------------------------------
     |
     | Point the users provider to your Usuario model.
     |
     */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Usuario::class,
        ],
        // If you ever use the query builder instead of Eloquent:
        // 'users' => ['driver' => 'database', 'table' => 'usuario'],
    ],

    /*
     |----------------------------------------------------------------------
     | Password Resetting
     |----------------------------------------------------------------------
     */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
