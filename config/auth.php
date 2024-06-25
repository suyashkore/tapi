<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt', // Assuming you're using JWT
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Feature\User\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets', // Make sure this table exists if using password reset features
            'expire' => 60,
            'throttle' => 1000,
        ],
    ],

    'password_timeout' => 10800,
];
