<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        \App\Http\Middleware\ForceJsonResponse::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'api' => [
            //          \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            // \App\Http\Middleware\ForceJsonResponse::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];

    protected $routeMiddleware = [
        // other middleware...

        //we don't have to explicitly define below. It's inbuilt.
        //'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'setUserContext' => \App\Http\Middleware\SetUserContext::class,
        'checkPrivileges' => \App\Http\Middleware\CheckPrivileges::class,
        // add more middleware aliases as needed
    ];

}
