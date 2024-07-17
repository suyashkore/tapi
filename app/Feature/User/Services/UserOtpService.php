<?php

namespace App\Feature\User\Services;

use App\Feature\Shared\Helpers\ImageHelper;
use App\Feature\User\Models\UserOtp;
use App\Feature\User\Repositories\UserOtpRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

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
     * Create a new UserOtp with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return UserOtp
     */
    public function createUserOtp(array $data, UserContext $userContext)
    {
        Log::info('Creating a new UserOtp in UserOtpService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userOtpRepository->create($data, $userContext);
    }

    /**
     * Retrieve a UserOtp by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return UserOtp|null
     */
    public function getUserOtpById(int $id, UserContext $userContext): ?UserOtp
    {
        Log::info('Fetching UserOtp by ID in UserOtpService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userOtpRepository->find($id, $userContext);
    }

    /**
     * Retrieve all UserOtps based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllUserOtps(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all userotps with filters in UserOtpService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userOtpRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing UserOtp with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return UserOtp|null
     */
    public function updateUserOtp(int $id, array $data, UserContext $userContext): ?UserOtp
    {
        Log::info('Updating UserOtp in UserOtpService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $userOtp = $this->userOtpRepository->find($id, $userContext);
        if ($userOtp) {
            return $this->userOtpRepository->update($userOtp, $data, $userContext);
        }
        return null;
    }

    /**
     * Delete a UserOtp by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteUserOtp(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting UserOtp in UserOtpService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $userOtp = $this->userOtpRepository->find($id, $userContext);
        if ($userOtp) {
            return $this->userOtpRepository->delete($userOtp, $userContext);
        }
        return false;
    }
}
