<?php

namespace App\Feature\Station\Controllers;

use App\Feature\Station\Requests\StationCoverageStoreRequest;
use App\Feature\Station\Requests\StationCoverageUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Station\Services\StationCoverageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class StationCoverageController
 *
 * Controller class to handle HTTP requests related to StationCoverage entity.
 *
 * @package App\Feature\Station\Controllers
 */
class StationCoverageController extends Controller
{
    /**
     * The service instance for handling business logic for the StationCoverage entity.
     *
     * @var StationCoverageService
     */
    protected $stationCoverageService;

    /**
     * StationCoverageController constructor.
     *
     * @param StationCoverageService $stationCoverageService
     */
    public function __construct(StationCoverageService $stationCoverageService)
    {
        $this->stationCoverageService = $stationCoverageService;
    }

    /**
     * Create a new StationCoverage: C
     *
     * @param StationCoverageStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StationCoverageStoreRequest $request)
    {
        Log::debug('StationCoverage store method called in StationCoverageController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new StationCoverage with validated data
            $stationCoverage = $this->stationCoverageService->createStationCoverage($validatedData, $userContext);
            $response = response()->json($stationCoverage, 201);
            Log::info('StationCoverage store method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create StationCoverage in StationCoverageController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single StationCoverage by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("StationCoverage show method called in StationCoverageController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch StationCoverage by ID
        $stationCoverage = $this->stationCoverageService->getStationCoverageById($id, $userContext);

        if ($stationCoverage) {
            $response = response()->json($stationCoverage);
            Log::info('StationCoverage show method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'StationCoverage not found'], 404);
            Log::error('Failed to retrieve StationCoverage in StationCoverageController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of StationCoverages with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("StationCoverage index method called in StationCoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch StationCoverages based on filters, sorting, and pagination
            $stationCoverages = $this->stationCoverageService->getAllStationCoverages($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($stationCoverages);
            // Extract pagination details
            $paginationDetails = [
                'total' => $stationCoverages->total(),
                'per_page' => $stationCoverages->perPage(),
                'current_page' => $stationCoverages->currentPage(),
                'from' => $stationCoverages->firstItem(),
                'to' => $stationCoverages->lastItem(),
            ];
            Log::info('StationCoverage index method response from StationCoverageController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in StationCoverageController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing StationCoverage: U
     *
     * @param StationCoverageUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StationCoverageUpdateRequest $request, $id)
    {
        Log::debug("StationCoverage update method called in StationCoverageController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update StationCoverage with validated data
            $stationCoverage = $this->stationCoverageService->updateStationCoverage($id, $validatedData, $userContext);
            if (!$stationCoverage) {
                $error_response = response()->json(['message' => 'StationCoverage not found or update not possible'], 404);
                Log::error('Failed to update StationCoverage in StationCoverageController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($stationCoverage);
            Log::info('StationCoverage update method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update StationCoverage in StationCoverageController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a StationCoverage (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating StationCoverage with ID: $id in StationCoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate StationCoverage by ID
        $stationCoverage = $this->stationCoverageService->deactivateStationCoverage($id, $userContext);

        if ($stationCoverage) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'StationCoverage deactivated successfully'], 200);
            Log::info('StationCoverage deactivate method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'StationCoverage not found or already deactivated'], 404);
            Log::error('Failed to deactivate StationCoverage in StationCoverageController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a StationCoverage permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete StationCoverage with ID: $id in StationCoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->stationCoverageService->deleteStationCoverage($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'StationCoverage deleted successfully'], 200);
            Log::info('StationCoverage destroy method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'StationCoverage not found'], 404);
            Log::error('Failed to delete StationCoverage in StationCoverageController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing StationCoverages.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in StationCoverageController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->stationCoverageService->generateXlsxTemplate($userContext);
            Log::info('StationCoverage xlsxTemplate method response from StationCoverageController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in StationCoverageController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import StationCoverages from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing StationCoverages from xlsx in StationCoverageController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import StationCoverages from the provided Excel file
            $result = $this->stationCoverageService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('StationCoverage importXlsx method response from StationCoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in StationCoverageController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export StationCoverages to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting StationCoverages to xlsx in StationCoverageController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export StationCoverages to an Excel file
            $filePath = $this->stationCoverageService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('StationCoverage exportXlsx method response from StationCoverageController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in StationCoverageController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
