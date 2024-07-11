<?php

namespace App\Feature\Contract\Repositories;

use App\Feature\Shared\Helpers\DateHelper;
use App\Feature\Contract\Models\LoaderRate;
use App\Feature\Shared\Services\UserContext;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Class LoaderRateRepository
 *
 * Repository class to handle database interactions for the LoaderRate model.
 *
 * @package App\Feature\LoaderRate\Repositories
 */
class LoaderRateRepository
{
    /**
     * Get the table name for the LoaderRate model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'loader_rates'; // Placeholder for table name
    }

    /**
     * Get the primary key name for the LoaderRate model.
     *
     * @return string
     */
    public static function pkName(): string
    {
        return 'id'; // Placeholder for primary key name
    }

    /**
     * Create a new LoaderRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return LoaderRate
     */
    public function create(array $data, UserContext $userContext): LoaderRate
    {
        Log::debug('Creating a new LoaderRate in the database', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Add tenant_id if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $data['tenant_id'] = $userContext->tenantId;
        }

        // Add created_by and updated_by fields
        $data['created_by'] = $userContext->userId;
        $data['updated_by'] = $userContext->userId;

        return LoaderRate::create($data);
    }

    /**
     * Find a LoaderRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return LoaderRate|null
     */
    public function find(int $id, UserContext $userContext): ?LoaderRate
    {
        Log::debug('Finding a LoaderRate with ID', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Add tenant_id clause if it exists in the user context and if there's a column by name tenant_id in the table
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            $query = LoaderRate::query();
            // ensure the LoaderRate belongs to the tenant_id in the user context
            $query->where('tenant_id', $userContext->tenantId);
            $query->where(self::pkName(), $id);
            return $query->first();
        } else {
            return LoaderRate::find($id);
        }
    }

    /**
     * Build the query for fetching LoaderRates with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildGetAllQuery(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Building query for fetching loaderrates from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = LoaderRate::query();

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
     * Fetch all LoaderRates with filters, sorting, and pagination: R
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
        Log::debug('Fetching all loaderrates paginated from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'perPage'=> $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the paginated result
        return $query->paginate($perPage);
    }

    /**
     * Fetch all LoaderRates with filters and sorting: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithoutPagination(array $filters, string $sortBy, string $sortOrder, UserContext $userContext)
    {
        Log::debug('Fetching all loaderrates from the database', ['filters' => $filters, 'sortBy'=> $sortBy, 'sortOrder'=> $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $query = $this->buildGetAllQuery($filters, $sortBy, $sortOrder, $userContext);

        // Return the result without pagination
        return $query->get();
    }

    /**
     * Update an existing LoaderRate with the given data: U
     *
     * @param LoaderRate $loaderRate
     * @param array $data
     * @param UserContext $userContext
     * @return LoaderRate
     */
    public function update(LoaderRate $loaderRate, array $data, UserContext $userContext): LoaderRate
    {
        Log::debug('Updating a LoaderRate in the database', ['id' => $loaderRate->id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        // Check if the LoaderRate belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($loaderRate->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized LoaderRate update attempt', ['tenantId' => $loaderRate->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized LoaderRate update attempt');
            }
        }
        $data['updated_by'] = $userContext->userId;
        $loaderRate->update($data);
        return $loaderRate;
    }

    /**
     * Delete a LoaderRate: D
     *
     * @param LoaderRate $loaderRate
     * @param UserContext $userContext
     * @return bool
     */
    public function delete(LoaderRate $loaderRate, UserContext $userContext): bool
    {
        Log::debug('Deleting a LoaderRate from the database', ['id' => $loaderRate->id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
         // Check if the LoaderRate belongs to the tenant_id in the user context
        if ($userContext->tenantId !== null && Schema::hasColumn(self::tableName(), 'tenant_id')) {
            if ($loaderRate->tenant_id !== $userContext->tenantId) {
                Log::warning('Unauthorized LoaderRate delete attempt', ['tenantId' => $loaderRate->tenant_id, 'userContext->tenantId' => $userContext->tenantId]);
                throw new \Exception('Unauthorized LoaderRate delete attempt');
            }
        }
        return $loaderRate->delete();
    }
}
