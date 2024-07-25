<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Middleware to check if the user has the required privileges.
 */
class CheckPrivileges
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$privileges
     * @return mixed
     */
    public function handle($request, Closure $next, ...$privileges)
    {
        // Assuming user context is set in the request by previous middleware
        $userContext = $request->userContext ?? null;

        if (!$userContext || count(array_intersect($privileges, $userContext->privileges)) === 0) {
            // User does not have any of the required privileges
            return response()->json(['error' => 'CheckPrivileges resulted in insufficient privileges'], 403);
        }

        return $next($request);
    }
}
