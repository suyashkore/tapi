<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if 'Accept' header is set to 'application/json', if not, set it
        if ($request->header('Accept') !== 'application/json') {
            $request->headers->set('Accept', 'application/json');
            // Log the updated 'Accept' header value
            Log::debug('Accept key is set in Request header: ', ['Accept' => $request->header('Accept')]);
        }

        return $next($request);
    }
}
