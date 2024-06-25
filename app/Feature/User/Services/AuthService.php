<?php

// <project_root>/app/Feature/User/Services/AuthService.php

namespace App\Feature\User\Services;

use App\Feature\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attemptAuthByLoginId($data)
    {
        Log::info('Attempting authentication by login ID in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id']]);

        // Check if tenant_id is set, if not set it to null
        $tenantId = $data['tenant_id'] ?? null;

        // Find the user based on login_id and tenant_id
        $user = $this->userRepository->findByLoginIdAndTenant($data['login_id'], $tenantId);

        if (! $user) {
            Log::warning('User not found or invalid tenant in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id']]);

            return response()->json(['error' => 'Auth Failed. Invalid credential'], 401);
        }

        // Check if the provided password matches the hashed password in the database
        if (Hash::check($data['password'], $user->password_hash)) {
            try {
                // Create a JWT token for the user
                $token = JWTAuth::fromUser($user);
                if (! $token) {
                    Log::error('Failed to create JWT token in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id']]);

                    return response()->json(['error' => 'Auth Failed.  JWT creation failed.'], 401);
                }

                Log::info('User authenticated successfully in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id']]);

                return response()->json(compact('token'));
            } catch (JWTException $e) {
                Log::error('JWT Exception in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id'], 'exception' => $e->getMessage()]);

                return response()->json(['error' => 'Auth Failed. Could not create JWT'], 500);
            }
        } else {
            Log::warning('Invalid password attempt in AuthService->attemptAuthByLoginId', ['tenant_id' => $data['tenant_id'] ?? null, 'login_id' => $data['login_id']]);

            return response()->json(['error' => 'Auth Failed. Invalid credentials.'], 401);
        }
    }

    // Define similar methods for other login types (by mobile, by email, etc.)

}
