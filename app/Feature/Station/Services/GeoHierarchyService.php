<?php

namespace App\Feature\Station\Services;

use App\Feature\Station\Models\GeoHierarchy;
use App\Feature\Station\Repositories\GeoHierarchyRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

/**
 * Class GeoHierarchyService
 *
 * Service class to handle business logic for the GeoHierarchy entity.
 *
 * @package App\Feature\Station\Services
 */
class GeoHierarchyService
{
    /**
     * The repository instance for interacting with the GeoHierarchy model.
     *
     * @var GeoHierarchyRepository
     */
    protected $geoHierarchyRepository;

    /**
     * GeoHierarchyService constructor.
     *
     * @param GeoHierarchyRepository $geoHierarchyRepository
     */
    public function __construct(GeoHierarchyRepository $geoHierarchyRepository)
    {
        $this->geoHierarchyRepository = $geoHierarchyRepository;
    }

    /**
     * Create a new GeoHierarchy with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return GeoHierarchy
     */
    public function createGeoHierarchy(array $data, UserContext $userContext)
    {
        Log::info('Creating a new GeoHierarchy in GeoHierarchyService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->geoHierarchyRepository->create($data, $userContext);
    }

    /**
     * Retrieve a GeoHierarchy by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return GeoHierarchy|null
     */
    public function getGeoHierarchyById(int $id, UserContext $userContext): ?GeoHierarchy
    {
        Log::info('Fetching GeoHierarchy by ID in GeoHierarchyService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->geoHierarchyRepository->find($id, $userContext);
    }

    /**
     * Retrieve all GeoHierarchys based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllGeoHierarchys(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all geohierarchys with filters in GeoHierarchyService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->geoHierarchyRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing GeoHierarchy with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return GeoHierarchy|null
     */
    public function updateGeoHierarchy(int $id, array $data, UserContext $userContext): ?GeoHierarchy
    {
        Log::info('Updating GeoHierarchy in GeoHierarchyService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $geoHierarchy = $this->geoHierarchyRepository->find($id, $userContext);
        if ($geoHierarchy) {
            return $this->geoHierarchyRepository->update($geoHierarchy, $data, $userContext);
        }
        return null;
    }

}
