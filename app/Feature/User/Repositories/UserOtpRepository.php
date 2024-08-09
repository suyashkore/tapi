<?php

namespace App\Feature\User\Repositories;

use App\Feature\User\Models\UserOtp;
use Carbon\Carbon;
/**
 * Class UserOtpRepository
 *
 * Repository class to handle database interactions for the UserOtp model.
 *
 * @package App\Feature\User\Repositories
 */
class UserOtpRepository
{
    /**
     * Find the latest valid OTP for a given tenant_id and login_id.
     *
     * @param int $tenantId
     * @param string $loginId
     * @return UserOtp|null
     */
    public function findLatestValidOtp(int $tenantId, string $loginId): ?UserOtp
    {
        return UserOtp::where('tenant_id', $tenantId)
            ->where('login_id', $loginId)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * Create or update an OTP record for a user.
     *
     * @param array $attributes
     * @param array $values
     * @return UserOtp
     */
    public function updateOrCreate(array $attributes, array $values): UserOtp
    {
        return UserOtp::updateOrCreate($attributes, $values);
    }
}
