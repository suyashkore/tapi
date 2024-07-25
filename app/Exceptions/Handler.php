<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_hash',
        'password_confirmation',
        'otp',
        'otp_hash',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle JWT authentication exceptions
        if ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } elseif ($exception instanceof TokenExpiredException) {
            return response()->json(['error' => 'Token has expired'], 401);
        } elseif ($exception instanceof JWTException) {
            return response()->json(['error' => 'Token is not provided'], 401);
        } elseif ($exception instanceof UnauthorizedHttpException) {
            // Handle the UnauthorizedHttpException which often wraps other exceptions
            $previousException = $exception->getPrevious();

            if ($previousException instanceof TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            } elseif ($previousException instanceof TokenInvalidException) {
                return response()->json(['error' => 'Token is invalid'], 401);
            } elseif ($previousException instanceof JWTException) {
                return response()->json(['error' => 'Token is not provided'], 401);
            }

            return response()->json(['error' => 'Unauthorized - missing or malformed token'], 401);
        }

        // Handle authorization exceptions
        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => 'You do not have permission to perform this action'], 403);
        }

        // Default exception handling
        return parent::render($request, $exception);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }
}
