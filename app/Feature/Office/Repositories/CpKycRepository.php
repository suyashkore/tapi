<?php

namespace App\Feature\Office\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\Office\Models\CpKyc;
use App\Feature\Shared\Services\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class CpKycRepository
 *
 * Repository class to handle database interactions for the CpKyc model.
 *
 * @package App\Feature\Office\Repositories
 */
class CpKycRepository
{
    /**
     * Get the table name for the CpKyc model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'cp_kyc'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the CpKyc model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new CpKyc with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CpKyc
     */
    public function create(array $data, UserContext $userContext): CpKyc
    {
        Log::debug('Creating a new CpKyc in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return CpKyc::create($data);
    }

    /**
     * Find a CpKyc by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CpKyc|null
     */
    public function find(int $id, UserContext $userContext): ?CpKyc
    {
        Log::debug('Finding a CpKyc with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = CpKyc::query();
            // ensure the CpKyc belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return CpKyc::find($id);
        }
    }

    /**
     * Build the query for fetching CpKycs with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching cpkycs from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = CpKyc::query();

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

        //TODO: Review if the model has field by name 'active' and below code is required
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
     * Fetch all CpKycs with filters, sorting, and pagination: R
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
        Log::debug('Fetching all cpkycs paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all CpKycs with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all cpkycs from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing CpKyc with the given data: U
     *
     * @param CpKyc $cpKyc
     * @param array $data
     * @param UserContext $userContext
     * @return CpKyc
     */
    public function update(CpKyc $cpKyc, array $data, UserContext $userContext): CpKyc
    {
        Log::debug('Updating a CpKyc in the database', ['id' => $cpKyc->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the CpKyc belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($cpKyc->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CpKyc update attempt', ['tenantId' => $cpKyc->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CpKyc update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $cpKyc->update($data);
        return $cpKyc;
    }

    /**
     * Delete a CpKyc: D
     *
     * @param CpKyc $cpKyc
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(CpKyc $cpKyc, UserContext $userContext): bool
    {
        Log::debug('Deleting a CpKyc from the database', ['id' => $cpKyc->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the CpKyc belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($cpKyc->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CpKyc delete attempt', ['tenantId' => $cpKyc->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CpKyc delete attempt');
            }
        }
        return $cpKyc->delete();
    }
}
