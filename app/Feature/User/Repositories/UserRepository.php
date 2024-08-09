<?php

namespace App\Feature\User\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\User\Models\User;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class UserRepository
 *
 * Repository class to handle database interactions for the User model.
 *
 * @package App\Feature\User\Repositories
 */
class UserRepository
{
    /**
     * Get the table name for the User model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'users'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the User model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new User with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return User
     */
    public function create(array $data, UserContext $userContext): User
    {
        Log::debug('Creating a new User in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return User::create($data);
    }

    /**
     * Find a User by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return User|null
     */
    public function find(int $id, UserContext $userContext): ?User
    {
        Log::debug('Finding a User with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = User::query();
            // ensure the User belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return User::find($id);
        }
    }

    /**
     * Find a user by their login ID and tenant ID, ensuring the user is active.
     *
     * This method retrieves a user record based on the given login ID and tenant ID.
     * It also ensures that the user is currently active.
     *
     * @param string $loginId The login ID of the user.
     * @param int|null $tenantId The ID of the tenant. If null, users without a tenant will be considered.
     * @return User|null The user record if found, or null if not found.
     */
    public function findByLoginIdAndTenant($loginId, $tenantId)
    {
        return User::where(function ($query) use ($tenantId) {
                    // Apply tenant ID filtering
                    if (is_null($tenantId)) {
                        $query->whereNull('tenant_id'); // Match users without a tenant
                    } else {
                        $query->where('tenant_id', $tenantId); // Match users with the specified tenant ID
                    }
                })
                ->where('active', true) // Ensure the user is active
                ->where('login_id', $loginId) // Match the login ID
                ->first(); // Retrieve the first matching record
    }

    /**
     * Find a user by their mobile number and tenant ID, ensuring the user is active.
     *
     * This method retrieves a user record based on the given mobile number and tenant ID.
     * It also ensures that the user is currently active.
     *
     * @param string $mobile The mobile number of the user.
     * @param int|null $tenantId The ID of the tenant. If null, users without a tenant will be considered.
     * @return User|null The user record if found, or null if not found.
     */
    public function findByMobileAndTenant($mobile, $tenantId)
    {
        return User::where(function ($query) use ($tenantId) {
                    // Apply tenant ID filtering
                    if (is_null($tenantId)) {
                        $query->whereNull('tenant_id'); // Match users without a tenant
                    } else {
                        $query->where('tenant_id', $tenantId); // Match users with the specified tenant ID
                    }
                })
                ->where('active', true) // Ensure the user is active
                ->where('mobile', $mobile) // Match the mobile number
                ->first(); // Retrieve the first matching record
    }

    /**
     * Find a user by their email address and tenant ID, ensuring the user is active.
     *
     * This method retrieves a user record based on the given email address and tenant ID.
     * It also ensures that the user is currently active.
     *
     * @param string $email The email address of the user.
     * @param int|null $tenantId The ID of the tenant. If null, users without a tenant will be considered.
     * @return User|null The user record if found, or null if not found.
     */
    public function findByEmailAndTenant($email, $tenantId)
    {
        return User::where(function ($query) use ($tenantId) {
                    // Apply tenant ID filtering
                    if (is_null($tenantId)) {
                        $query->whereNull('tenant_id'); // Match users without a tenant
                    } else {
                        $query->where('tenant_id', $tenantId); // Match users with the specified tenant ID
                    }
                })
                ->where('active', true) // Ensure the user is active
                ->where('email', $email) // Match the email address
                ->first(); // Retrieve the first matching record
    }

    /**
     * Build the query for fetching Users with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching users from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = User::query();

        // Apply tenant_id filter from user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query->where('tenant_id', $userContext->tenantId);
        }

        // Apply created_at range filters
        if (isset($filters['created_from']) && !empty($filters['created_from'])) {
            $query->where('created_at', '>=', DateHelper::setStartTime($filters['created_from']));
        }
        if (isset($filters['created_to']) && !empty($filters['created_to'])) {
            $query->where('created_at', '<=', DateHelper::setEndTime($filters['created_to']));
        }

        // Apply updated_at range filters
        if (isset($filters['updated_from']) && !empty($filters['updated_from'])) {
            $query->where('updated_at', '>=', DateHelper::setStartTime($filters['updated_from']));
        }
        if (isset($filters['updated_to']) && !empty($filters['updated_to'])) {
            $query->where('updated_at', '<=', DateHelper::setEndTime($filters['updated_to']));
        }

        // Apply active filter
        if (!isset($filters['active'])) {
            $query->where('active', true); // Default active filter
        } else {
            switch ($filters['active']) {
                case 'true':
                    $query->where('active', true);
                    break;
                case 'false':
                    $query->where('active', false);
                    break;
                case 'both':
                    // No need to add a where clause for 'both'
                    break;
                default:
                    $query->where('active', true); // Default to true for invalid values
                    break;
            }
        }

        // Apply other filters
        foreach ($filters as $key => $value) {
            if (!empty($value) && !in_array($key, ['created_from', 'created_to', 'updated_from', 'updated_to', 'active'])) {
                if (is_string($value)) {
                    $query->where($key, 'like', "%$value%");
                } elseif (is_int($value) || is_float($value) || is_double($value)) {
                    $query->where($key, $value);
                } elseif (is_bool($value)) {
                    $query->where($key, $value);
                } elseif ($value instanceof \DateTime) {
                    $query->whereDate($key, $value->format('Y-m-d'));
                }
            }
        }

        // Apply sorting
        $query->orderBy($sortBy ?: 'updated_at', $sortOrder ?: 'desc');

        return $query;
    }

    /**
     * Fetch all Users with filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext): LengthAwarePaginator
    {
        Log::debug('Fetching all users paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all Users with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all users from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing User with the given data: U
     *
     * @param User $user
     * @param array $data
     * @param UserContext $userContext
     * @return User
     */
    public function update(User $user, array $data, UserContext $userContext): User
    {
        Log::debug('Updating a User in the database', ['id' => $user->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the User belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($user->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized User update attempt', ['tenantId' => $user->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized User update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $user->update($data);
        return $user;
    }

    /**
     * Delete a User: D
     *
     * @param User $user
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(User $user, UserContext $userContext): bool
    {
        Log::debug('Deleting a User from the database', ['id' => $user->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the User belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($user->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized User delete attempt', ['tenantId' => $user->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized User delete attempt');
            }
        }
        return $user->delete();
    }

    /**
     * Update the password of a user.
     *
     * @param int $tenantId
     * @param string $loginId
     * @param string $hashedPassword
     * @param UserContext $userContext
     * @return void
     */
    public function updateUserPassword(int $tenantId, string $loginId, string $hashedPassword, UserContext $userContext): void
    {
        Log::debug("Updating password in UserRepository for user login ID: $loginId in tenant ID: $tenantId", ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the User belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($tenantId !== $userContext->tenantId) {
                Log::error('Unauthorized password reset attempt in UserRepository', ['tenantId' => $tenantId, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized password reset attempt in UserRepository');
            }
        }
        User::where('tenant_id', $tenantId)
            ->where('login_id', $loginId)
            ->update(['password_hash' => $hashedPassword]);
    }
}
