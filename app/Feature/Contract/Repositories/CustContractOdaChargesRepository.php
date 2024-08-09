<?php

namespace App\Feature\Contract\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\Contract\Models\CustContractOdaCharges;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class CustContractOdaChargesRepository
 *
 * Repository class to handle database interactions for the CustContractOdaCharges model.
 *
 * @package App\Feature\Contract\Repositories
 */
class CustContractOdaChargesRepository
{
    /**
     * Get the table name for the CustContractOdaCharges model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'cust_contract_oda_charges'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the CustContractOdaCharges model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new CustContractOdaCharges with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractOdaCharges
     */
    public function create(array $data, UserContext $userContext): CustContractOdaCharges
    {
        Log::debug('Creating a new CustContractOdaCharges in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return CustContractOdaCharges::create($data);
    }

    /**
     * Find a CustContractOdaCharges by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractOdaCharges|null
     */
    public function find(int $id, UserContext $userContext): ?CustContractOdaCharges
    {
        Log::debug('Finding a CustContractOdaCharges with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = CustContractOdaCharges::query();
            // ensure the CustContractOdaCharges belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return CustContractOdaCharges::find($id);
        }
    }

    /**
     * Build the query for fetching CustContractOdaCharges with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching custcontractodacharges from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = CustContractOdaCharges::query();

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
     * Fetch all CustContractOdaCharges with filters, sorting, and pagination: R
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
        Log::debug('Fetching all custcontractodacharges paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all CustContractOdaCharges with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all custcontractodacharges from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing CustContractOdaCharges with the given data: U
     *
     * @param CustContractOdaCharges $custContractOdaCharges
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractOdaCharges
     */
    public function update(CustContractOdaCharges $custContractOdaCharges, array $data, UserContext $userContext): CustContractOdaCharges
    {
        Log::debug('Updating a CustContractOdaCharges in the database', ['id' => $custContractOdaCharges->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the CustContractOdaCharges belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractOdaCharges->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractOdaCharges update attempt', ['tenantId' => $custContractOdaCharges->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractOdaCharges update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $custContractOdaCharges->update($data);
        return $custContractOdaCharges;
    }

    /**
     * Delete a CustContractOdaCharges: D
     *
     * @param CustContractOdaCharges $custContractOdaCharges
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(CustContractOdaCharges $custContractOdaCharges, UserContext $userContext): bool
    {
        Log::debug('Deleting a CustContractOdaCharges from the database', ['id' => $custContractOdaCharges->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the CustContractOdaCharges belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractOdaCharges->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractOdaCharges delete attempt', ['tenantId' => $custContractOdaCharges->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractOdaCharges delete attempt');
            }
        }
        return $custContractOdaCharges->delete();
    }
}
