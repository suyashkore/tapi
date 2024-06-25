<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'tapi'),
    'env' => env('APP_ENV', 'local'), // production
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Asia/Kolkata', // Set your desired timezone
    'locale' => 'en_IN',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_IN',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'auth' => [
        'otp_expiry' => 10, // minutes
        'otp_resend_threshold' => 1, // minute
        'max_failed_login_attempts' => 5,
        'max_failed_otp_attempts' => 5,
        'otp_login_block_duration' => 60, //minutes
    ],

    /*
     * Application Service Providers...
     */
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        // Add Maatwebsite Excel ServiceProvider
        Maatwebsite\Excel\ExcelServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Image' => Intervention\Image\Facades\Image::class,
        // Add Excel alias
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ])->toArray(),
];
