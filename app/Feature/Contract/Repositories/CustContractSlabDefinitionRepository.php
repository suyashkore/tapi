<?php

namespace App\Feature\Contract\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\Contract\Models\CustContractSlabDefinition;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class CustContractSlabDefinitionRepository
 *
 * Repository class to handle database interactions for the CustContractSlabDefinition model.
 *
 * @package App\Feature\Contract\Repositories
 */
class CustContractSlabDefinitionRepository
{
    /**
     * Get the table name for the CustContractSlabDefinition model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'cust_contract_slab_definitions'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the CustContractSlabDefinition model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new CustContractSlabDefinition with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabDefinition
     */
    public function create(array $data, UserContext $userContext): CustContractSlabDefinition
    {
        Log::debug('Creating a new CustContractSlabDefinition in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return CustContractSlabDefinition::create($data);
    }

    /**
     * Find a CustContractSlabDefinition by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractSlabDefinition|null
     */
    public function find(int $id, UserContext $userContext): ?CustContractSlabDefinition
    {
        Log::debug('Finding a CustContractSlabDefinition with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = CustContractSlabDefinition::query();
            // ensure the CustContractSlabDefinition belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return CustContractSlabDefinition::find($id);
        }
    }

    /**
     * Build the query for fetching CustContractSlabDefinitions with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching custcontractslabdefinitions from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = CustContractSlabDefinition::query();

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
     * Fetch all CustContractSlabDefinitions with filters, sorting, and pagination: R
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
        Log::debug('Fetching all custcontractslabdefinitions paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all CustContractSlabDefinitions with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all custcontractslabdefinitions from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing CustContractSlabDefinition with the given data: U
     *
     * @param CustContractSlabDefinition $custContractSlabDefinition
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabDefinition
     */
    public function update(CustContractSlabDefinition $custContractSlabDefinition, array $data, UserContext $userContext): CustContractSlabDefinition
    {
        Log::debug('Updating a CustContractSlabDefinition in the database', ['id' => $custContractSlabDefinition->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the CustContractSlabDefinition belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractSlabDefinition->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractSlabDefinition update attempt', ['tenantId' => $custContractSlabDefinition->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractSlabDefinition update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $custContractSlabDefinition->update($data);
        return $custContractSlabDefinition;
    }

    /**
     * Delete a CustContractSlabDefinition: D
     *
     * @param CustContractSlabDefinition $custContractSlabDefinition
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(CustContractSlabDefinition $custContractSlabDefinition, UserContext $userContext): bool
    {
        Log::debug('Deleting a CustContractSlabDefinition from the database', ['id' => $custContractSlabDefinition->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the CustContractSlabDefinition belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($custContractSlabDefinition->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized CustContractSlabDefinition delete attempt', ['tenantId' => $custContractSlabDefinition->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized CustContractSlabDefinition delete attempt');
            }
        }
        return $custContractSlabDefinition->delete();
    }
}
