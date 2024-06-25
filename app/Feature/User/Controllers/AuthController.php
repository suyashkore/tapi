<?php

// <project_root>/app/Feature/User/Controllers/AuthController.php

namespace App\Feature\User\Controllers;

use App\Feature\User\Requests\AuthByLoginIdRequest;
use App\Feature\User\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function authByLoginId(AuthByLoginIdRequest $request): JsonResponse
    {
        Log::debug('AuthController -> authByLoginId method called', ['url' => $request->url()]);

        $validated = $request->validated();

        return $this->authService->attemptAuthByLoginId($validated);
    }

    // Define similar methods for other login types
}
