<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\CustContractExcessWeightRateStoreRequest;
use App\Feature\Contract\Requests\CustContractExcessWeightRateUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\CustContractExcessWeightRateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustContractExcessWeightRateController
 *
 * Controller class to handle HTTP requests related to CustContractExcessWeightRate entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class CustContractExcessWeightRateController extends Controller
{
    /**
     * The service instance for handling business logic for the CustContractExcessWeightRate entity.
     *
     * @var CustContractExcessWeightRateService
     */
    protected $custContractExcessWeightRateService;

    /**
     * CustContractExcessWeightRateController constructor.
     *
     * @param CustContractExcessWeightRateService $custContractExcessWeightRateService
     */
    public function __construct(CustContractExcessWeightRateService $custContractExcessWeightRateService)
    {
        $this->custContractExcessWeightRateService = $custContractExcessWeightRateService;
    }

    /**
     * Create a new CustContractExcessWeightRate: C
     *
     * @param CustContractExcessWeightRateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustContractExcessWeightRateStoreRequest $request)
    {
        Log::debug('CustContractExcessWeightRate store method called in CustContractExcessWeightRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CustContractExcessWeightRate with validated data
            $custContractExcessWeightRate = $this->custContractExcessWeightRateService->createCustContractExcessWeightRate($validatedData, $userContext);
            $response = response()->json($custContractExcessWeightRate, 201);
            Log::info('CustContractExcessWeightRate store method response from CustContractExcessWeightRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CustContractExcessWeightRate in CustContractExcessWeightRateController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CustContractExcessWeightRate by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CustContractExcessWeightRate show method called in CustContractExcessWeightRateController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CustContractExcessWeightRate by ID
        $custContractExcessWeightRate = $this->custContractExcessWeightRateService->getCustContractExcessWeightRateById($id, $userContext);

        if ($custContractExcessWeightRate) {
            $response = response()->json($custContractExcessWeightRate);
            Log::info('CustContractExcessWeightRate show method response from CustContractExcessWeightRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractExcessWeightRate not found'], 404);
            Log::error('Failed to retrieve CustContractExcessWeightRate in CustContractExcessWeightRateController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CustContractExcessWeightRates with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CustContractExcessWeightRate index method called in CustContractExcessWeightRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

       
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CustContractExcessWeightRates based on filters, sorting, and pagination
            $custContractExcessWeightRates = $this->custContractExcessWeightRateService->getAllCustContractExcessWeightRates($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($custContractExcessWeightRates);
            // Extract pagination details
            $paginationDetails = [
                'total' => $custContractExcessWeightRates->total(),
                'per_page' => $custContractExcessWeightRates->perPage(),
                'current_page' => $custContractExcessWeightRates->currentPage(),
                'from' => $custContractExcessWeightRates->firstItem(),
                'to' => $custContractExcessWeightRates->lastItem(),
            ];
            Log::info('CustContractExcessWeightRate index method response from CustContractExcessWeightRateController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustContractExcessWeightRateController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CustContractExcessWeightRate: U
     *
     * @param CustContractExcessWeightRateUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustContractExcessWeightRateUpdateRequest $request, $id)
    {
        Log::debug("CustContractExcessWeightRate update method called in CustContractExcessWeightRateController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CustContractExcessWeightRate with validated data
            $custContractExcessWeightRate = $this->custContractExcessWeightRateService->updateCustContractExcessWeightRate($id, $validatedData, $userContext);
            if (!$custContractExcessWeightRate) {
                $error_response = response()->json(['message' => 'CustContractExcessWeightRate not found or update not possible'], 404);
                Log::error('Failed to update CustContractExcessWeightRate in CustContractExcessWeightRateController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($custContractExcessWeightRate);
            Log::info('CustContractExcessWeightRate update method response from CustContractExcessWeightRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CustContractExcessWeightRate in CustContractExcessWeightRateController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a CustContractExcessWeightRate permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CustContractExcessWeightRate with ID: $id in CustContractExcessWeightRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->custContractExcessWeightRateService->deleteCustContractExcessWeightRate($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CustContractExcessWeightRate deleted successfully'], 200);
            Log::info('CustContractExcessWeightRate destroy method response from CustContractExcessWeightRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractExcessWeightRate not found'], 404);
            Log::error('Failed to delete CustContractExcessWeightRate in CustContractExcessWeightRateController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CustContractExcessWeightRates.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustContractExcessWeightRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->custContractExcessWeightRateService->generateXlsxTemplate($userContext);
            Log::info('CustContractExcessWeightRate xlsxTemplate method response from CustContractExcessWeightRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustContractExcessWeightRateController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CustContractExcessWeightRates from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CustContractExcessWeightRates from xlsx in CustContractExcessWeightRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CustContractExcessWeightRates from the provided Excel file
            $result = $this->custContractExcessWeightRateService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CustContractExcessWeightRate importXlsx method response from CustContractExcessWeightRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustContractExcessWeightRateController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CustContractExcessWeightRates to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CustContractExcessWeightRates to xlsx in CustContractExcessWeightRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CustContractExcessWeightRates to an Excel file
            $filePath = $this->custContractExcessWeightRateService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CustContractExcessWeightRate exportXlsx method response from CustContractExcessWeightRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustContractExcessWeightRateController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
