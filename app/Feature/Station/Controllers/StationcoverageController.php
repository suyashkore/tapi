<?php

namespace App\Feature\Station\Controllers;

use App\Feature\Station\Requests\StationcoverageStoreRequest;
use App\Feature\Station\Requests\StationcoverageUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Station\Services\StationcoverageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class StationcoverageController
 *
 * Controller class to handle HTTP requests related to Stationcoverage entity.
 *
 * @package App\Feature\Station\Controllers
 */
class StationcoverageController extends Controller
{
    /**
     * The service instance for handling business logic for the Stationcoverage entity.
     *
     * @var StationcoverageService
     */
    protected $stationcoverageService;

    /**
     * StationcoverageController constructor.
     *
     * @param StationcoverageService $stationcoverageService
     */
    public function __construct(StationcoverageService $stationcoverageService)
    {
        $this->stationcoverageService = $stationcoverageService;
    }

    /**
     * Create a new Stationcoverage: C
     *
     * @param StationcoverageStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StationcoverageStoreRequest $request)
    {
        Log::debug('Stationcoverage store method called in StationcoverageController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Stationcoverage with validated data
            $stationcoverage = $this->stationcoverageService->createStationcoverage($validatedData, $userContext);
            $response = response()->json($stationcoverage, 201);
            Log::info('Stationcoverage store method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Stationcoverage in StationcoverageController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Stationcoverage by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Stationcoverage show method called in StationcoverageController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Stationcoverage by ID
        $stationcoverage = $this->stationcoverageService->getStationcoverageById($id, $userContext);

        if ($stationcoverage) {
            $response = response()->json($stationcoverage);
            Log::info('Stationcoverage show method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Stationcoverage not found'], 404);
            Log::error('Failed to retrieve Stationcoverage in StationcoverageController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Stationcoverages with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Stationcoverage index method called in StationcoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Stationcoverages based on filters, sorting, and pagination
            $stationcoverages = $this->stationcoverageService->getAllStationcoverages($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($stationcoverages);
            // Extract pagination details
            $paginationDetails = [
                'total' => $stationcoverages->total(),
                'per_page' => $stationcoverages->perPage(),
                'current_page' => $stationcoverages->currentPage(),
                'from' => $stationcoverages->firstItem(),
                'to' => $stationcoverages->lastItem(),
            ];
            Log::info('Stationcoverage index method response from StationcoverageController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in StationcoverageController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Stationcoverage: U
     *
     * @param StationcoverageUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StationcoverageUpdateRequest $request, $id)
    {
        Log::debug("Stationcoverage update method called in StationcoverageController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Stationcoverage with validated data
            $stationcoverage = $this->stationcoverageService->updateStationcoverage($id, $validatedData, $userContext);
            if (!$stationcoverage) {
                $error_response = response()->json(['message' => 'Stationcoverage not found or update not possible'], 404);
                Log::error('Failed to update Stationcoverage in StationcoverageController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($stationcoverage);
            Log::info('Stationcoverage update method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Stationcoverage in StationcoverageController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

  

    /**
     * Deactivate a Stationcoverage (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Stationcoverage with ID: $id in StationcoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Stationcoverage by ID
        $stationcoverage = $this->stationcoverageService->deactivateStationcoverage($id, $userContext);

        if ($stationcoverage) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Stationcoverage deactivated successfully'], 200);
            Log::info('Stationcoverage deactivate method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Stationcoverage not found or already deactivated'], 404);
            Log::error('Failed to deactivate Stationcoverage in StationcoverageController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Stationcoverage permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Stationcoverage with ID: $id in StationcoverageController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->stationcoverageService->deleteStationcoverage($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Stationcoverage deleted successfully'], 200);
            Log::info('Stationcoverage destroy method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Stationcoverage not found'], 404);
            Log::error('Failed to delete Stationcoverage in StationcoverageController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Stationcoverages.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in StationcoverageController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->stationcoverageService->generateXlsxTemplate($userContext);
            Log::info('Stationcoverage xlsxTemplate method response from StationcoverageController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in StationcoverageController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Stationcoverages from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Stationcoverages from xlsx in StationcoverageController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Stationcoverages from the provided Excel file
            $result = $this->stationcoverageService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Stationcoverage importXlsx method response from StationcoverageController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in StationcoverageController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Stationcoverages to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Stationcoverages to xlsx in StationcoverageController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Stationcoverages to an Excel file
            $filePath = $this->stationcoverageService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Stationcoverage exportXlsx method response from StationcoverageController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in StationcoverageController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
