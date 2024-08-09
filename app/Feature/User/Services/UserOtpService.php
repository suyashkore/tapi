<?php

namespace App\Feature\User\Services;

use App\Feature\User\Models\User;
use App\Feature\User\Models\UserOtp;
use App\Feature\User\Repositories\UserOtpRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Class UserOtpService
 *
 * Service class to handle business logic for the UserOtp entity.
 *
 * @package App\Feature\User\Services
 */
class UserOtpService
{
    /**
     * The repository instance for interacting with the UserOtp model.
     *
     * @var UserOtpRepository
     */
    protected $userOtpRepository;

    /**
     * UserOtpService constructor.
     *
     * @param UserOtpRepository $userOtpRepository
     */
    public function __construct(UserOtpRepository $userOtpRepository)
    {
        $this->userOtpRepository = $userOtpRepository;
    }

    /**
     * Create and store OTP for a user.
     *
     * @param int $tenantId
     * @param string $loginId
     * @return void
     */
    public function createOtp(int $tenantId, string $loginId): void
    {
        Log::info("Creating OTP for user login ID: $loginId in tenant ID: $tenantId");

        // Find the user by tenant_id and login_id
        $user = User::where('tenant_id', $tenantId)
            ->where('login_id', $loginId)
            ->firstOrFail();

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpHash = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(5); // OTP expires in 5 minutes

        // Use the repository to create or update OTP record
        $this->userOtpRepository->updateOrCreate(
            [
                'user_id' => $user->id, // Set the user_id
                'tenant_id' => $tenantId,
                'login_id' => $loginId,
            ],
            [
                'otp_hash' => $otpHash,
                'expires_at' => $expiresAt,
            ]
        );

        // Send OTP to the user via the preferred method (e.g., SMS, Email)
        // This is just a placeholder, replace with actual implementation
        Log::info("OTP $otp generated and sent to user login ID: $loginId in tenant ID: $tenantId");
    }

    /**
     * Verify OTP for a user.
     *
     * @param int $tenantId
     * @param string $loginId
     * @param string $otp
     * @return bool
     */
    public function verifyOtp(int $tenantId, string $loginId, string $otp): bool
    {
        Log::info("Verifying OTP for login ID: $loginId in tenant ID: $tenantId");

        // Find the latest OTP record for the user
        $userOtp = $this->userOtpRepository->findLatestValidOtp($tenantId, $loginId);

        if (!$userOtp) {
            Log::error("No valid OTP found for login ID: $loginId in tenant ID: $tenantId");
            return false;
        }

        // Verify the OTP
        if (Hash::check($otp, $userOtp->otp_hash)) {
            Log::info("OTP verification successful for login ID: $loginId in tenant ID: $tenantId");
            return true;
        }

        Log::error("OTP verification failed for login ID: $loginId in tenant ID: $tenantId");
        return false;
    }
}
