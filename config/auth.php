<?php

use App\Models\Account;
use App\Models\UndpUser;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'undp_users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'accounts',
        ],
        'undp' => [
            'driver' => 'session',
            'provider' => 'undp_users',
        ],
    ],

    'providers' => [
        'accounts' => [
            'driver' => 'eloquent',
            'model' => Account::class,
        ],
        'undp_users' => [
            'driver' => 'eloquent',
            'model' => UndpUser::class,
        ],
    ],

    'passwords' => [
        'undp_users' => [
            'provider' => 'undp_users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
