<?php

namespace App\Feature\Contract\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\Contract\Models\CustContractSlabRate;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class CustContractSlabRateRepository
 *
 * Repository class to handle database interactions for the CustContractSlabRate model.
 *
 * @package App\Feature\Contract\Repositories
 */
class CustContractSlabRateRepository
{
    /**
     * Get the table name for the CustContractSlabRate model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'cust_contract_slab_rates'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the CustContractSlabRate model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new CustContractSlabRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabRate
     */
    public function create(array $data, UserContext $userContext): CustContractSlabRate
    {
        Log::debug('Creating a new CustContractSlabRate in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return CustContractSlabRate::create($data);
    }

    /**
     * Find a CustContractSlabRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractSlabRate|null
     */
    public function find(int $id, UserContext $userContext): ?CustContractSlabRate
    {
        Log::debug('Finding a CustContractSlabRate with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = CustContractSlabRate::query();
            // ensure the CustContractSlabRate belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return CustContractSlabRate::find($id);
        }
    }

    /**
     * Build the query for fetching CustContractSlabRates with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching custcontractslabrates from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = CustContractSlabRate::query();

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

        // Apply other filters
        foreach ($filters as $key => $value) {
            if (!empty($value) && !in_array($key, ['created_from', 'created_to', 'updated_from', 'updated_to'])) {
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
     * Fetch all CustContractSlabRates with filters, sorting, and pagination: R
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
        Log::debug('Fetching all custcontractslabrates paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all CustContractSlabRates with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all custcontractslabrates from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing CustContractSlabRate with the given data: U
     *
     * @param CustContractSlabRate $custContractSlabRate
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabRate
     */
    public function update(CustContractSlabRate $custContractSlabRate, array $data, UserContext $userContext): CustContractSlabRate
    {
        Log::debug('Updating a CustContractSlabRate in the database', ['id' => $custContractSlabRate->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the CustContractSlabRate belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractSlabRate->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractSlabRate update attempt', ['tenantId' => $custContractSlabRate->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractSlabRate update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $custContractSlabRate->update($data);
        return $custContractSlabRate;
    }

    /**
     * Delete a CustContractSlabRate: D
     *
     * @param CustContractSlabRate $custContractSlabRate
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(CustContractSlabRate $custContractSlabRate, UserContext $userContext): bool
    {
        Log::debug('Deleting a CustContractSlabRate from the database', ['id' => $custContractSlabRate->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the CustContractSlabRate belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractSlabRate->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractSlabRate delete attempt', ['tenantId' => $custContractSlabRate->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractSlabRate delete attempt');
            }
        }
        return $custContractSlabRate->delete();
    }
}
