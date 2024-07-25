<?php

namespace App\Feature\Station\Controllers;

use App\Feature\Station\Requests\GeoHierarchyStoreRequest;
use App\Feature\Station\Requests\GeoHierarchyUpdateRequest;
use App\Feature\Station\Services\GeoHierarchyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class GeoHierarchyController
 *
 * Controller class to handle HTTP requests related to GeoHierarchy entity.
 *
 * @package App\Feature\Station\Controllers
 */
class GeoHierarchyController extends Controller
{
    /**
     * The service instance for handling business logic for the GeoHierarchy entity.
     *
     * @var GeoHierarchyService
     */
    protected $geoHierarchyService;

    /**
     * GeoHierarchyController constructor.
     *
     * @param GeoHierarchyService $geoHierarchyService
     */
    public function __construct(GeoHierarchyService $geoHierarchyService)
    {
        $this->geoHierarchyService = $geoHierarchyService;
    }

    /**
     * Create a new GeoHierarchy: C
     *
     * @param GeoHierarchyStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GeoHierarchyStoreRequest $request)
    {
        Log::debug('GeoHierarchy store method called in GeoHierarchyController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new GeoHierarchy with validated data
            $geoHierarchy = $this->geoHierarchyService->createGeoHierarchy($validatedData, $userContext);
            $response = response()->json($geoHierarchy, 201);
            Log::info('GeoHierarchy store method response from GeoHierarchyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create GeoHierarchy in GeoHierarchyController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single GeoHierarchy by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("GeoHierarchy show method called in GeoHierarchyController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch GeoHierarchy by ID
        $geoHierarchy = $this->geoHierarchyService->getGeoHierarchyById($id, $userContext);

        if ($geoHierarchy) {
            $response = response()->json($geoHierarchy);
            Log::info('GeoHierarchy show method response from GeoHierarchyController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'GeoHierarchy not found'], 404);
            Log::error('Failed to retrieve GeoHierarchy in GeoHierarchyController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of GeoHierarchys with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("GeoHierarchy index method called in GeoHierarchyController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only([ 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch GeoHierarchys based on filters, sorting, and pagination
            $geoHierarchys = $this->geoHierarchyService->getAllGeoHierarchys($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($geoHierarchys);
            // Extract pagination details
            $paginationDetails = [
                'total' => $geoHierarchys->total(),
                'per_page' => $geoHierarchys->perPage(),
                'current_page' => $geoHierarchys->currentPage(),
                'from' => $geoHierarchys->firstItem(),
                'to' => $geoHierarchys->lastItem(),
            ];
            Log::info('GeoHierarchy index method response from GeoHierarchyController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in GeoHierarchyController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing GeoHierarchy: U
     *
     * @param GeoHierarchyUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GeoHierarchyUpdateRequest $request, $id)
    {
        Log::debug("GeoHierarchy update method called in GeoHierarchyController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update GeoHierarchy with validated data
            $geoHierarchy = $this->geoHierarchyService->updateGeoHierarchy($id, $validatedData, $userContext);
            if (!$geoHierarchy) {
                $error_response = response()->json(['message' => 'GeoHierarchy not found or update not possible'], 404);
                Log::error('Failed to update GeoHierarchy in GeoHierarchyController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($geoHierarchy);
            Log::info('GeoHierarchy update method response from GeoHierarchyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update GeoHierarchy in GeoHierarchyController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

}
