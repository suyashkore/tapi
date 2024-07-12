<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\DriverRateStoreRequest;
use App\Feature\Contract\Requests\DriverRateUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\DriverRateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class DriverRateController
 *
 * Controller class to handle HTTP requests related to DriverRate entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class DriverRateController extends Controller
{
    /**
     * The service instance for handling business logic for the DriverRate entity.
     *
     * @var DriverRateService
     */
    protected $driverRateService;

    /**
     * DriverRateController constructor.
     *
     * @param DriverRateService $driverRateService
     */
    public function __construct(DriverRateService $driverRateService)
    {
        $this->driverRateService = $driverRateService;
    }

    /**
     * Create a new DriverRate: C
     *
     * @param DriverRateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DriverRateStoreRequest $request)
    {
        Log::debug('DriverRate store method called in DriverRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new DriverRate with validated data
            $driverRate = $this->driverRateService->createDriverRate($validatedData, $userContext);
            $response = response()->json($driverRate, 201);
            Log::info('DriverRate store method response from DriverRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create DriverRate in DriverRateController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single DriverRate by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("DriverRate show method called in DriverRateController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch DriverRate by ID
        $driverRate = $this->driverRateService->getDriverRateById($id, $userContext);

        if ($driverRate) {
            $response = response()->json($driverRate);
            Log::info('DriverRate show method response from DriverRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'DriverRate not found'], 404);
            Log::error('Failed to retrieve DriverRate in DriverRateController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of DriverRates with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("DriverRate index method called in DriverRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch DriverRates based on filters, sorting, and pagination
            $driverRates = $this->driverRateService->getAllDriverRates($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($driverRates);
            // Extract pagination details
            $paginationDetails = [
                'total' => $driverRates->total(),
                'per_page' => $driverRates->perPage(),
                'current_page' => $driverRates->currentPage(),
                'from' => $driverRates->firstItem(),
                'to' => $driverRates->lastItem(),
            ];
            Log::info('DriverRate index method response from DriverRateController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in DriverRateController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing DriverRate: U
     *
     * @param DriverRateUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DriverRateUpdateRequest $request, $id)
    {
        Log::debug("DriverRate update method called in DriverRateController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update DriverRate with validated data
            $driverRate = $this->driverRateService->updateDriverRate($id, $validatedData, $userContext);
            if (!$driverRate) {
                $error_response = response()->json(['message' => 'DriverRate not found or update not possible'], 404);
                Log::error('Failed to update DriverRate in DriverRateController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($driverRate);
            Log::info('DriverRate update method response from DriverRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update DriverRate in DriverRateController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a DriverRate (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating DriverRate with ID: $id in DriverRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate DriverRate by ID
        $driverRate = $this->driverRateService->deactivateDriverRate($id, $userContext);

        if ($driverRate) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'DriverRate deactivated successfully'], 200);
            Log::info('DriverRate deactivate method response from DriverRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'DriverRate not found or already deactivated'], 404);
            Log::error('Failed to deactivate DriverRate in DriverRateController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a DriverRate permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete DriverRate with ID: $id in DriverRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->driverRateService->deleteDriverRate($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'DriverRate deleted successfully'], 200);
            Log::info('DriverRate destroy method response from DriverRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'DriverRate not found'], 404);
            Log::error('Failed to delete DriverRate in DriverRateController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing DriverRates.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in DriverRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->driverRateService->generateXlsxTemplate($userContext);
            Log::info('DriverRate xlsxTemplate method response from DriverRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in DriverRateController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import DriverRates from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing DriverRates from xlsx in DriverRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import DriverRates from the provided Excel file
            $result = $this->driverRateService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('DriverRate importXlsx method response from DriverRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in DriverRateController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export DriverRates to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting DriverRates to xlsx in DriverRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export DriverRates to an Excel file
            $filePath = $this->driverRateService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('DriverRate exportXlsx method response from DriverRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in DriverRateController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
