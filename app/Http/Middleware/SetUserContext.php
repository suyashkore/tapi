<?php

namespace App\Http\Middleware;

use Closure;
use App\Feature\Shared\Services\UserContext;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class SetUserContext
{
    public function handle($request, Closure $next)
    {
        // Check if token is present and user is authenticated
        if ($token = JWTAuth::getToken() && $user = JWTAuth::parseToken()->authenticate()) {
            // Retrieve the JWT payload
            $payload = JWTAuth::getPayload();

            // Initialize and set UserContext in the request
            $request->userContext = new UserContext($payload);

            // Set UserContext in the request attributes
            $request->attributes->add(['userContext' => $request->userContext]);

             // Log the user object
             Log::debug('Authenticated UserContext retrieved from JWT and set as attribute in the request:', ['userContext' => $request->userContext]);
        }

        return $next($request);
    }
}
