<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\CustContractSlabRateStoreRequest;
use App\Feature\Contract\Requests\CustContractSlabRateUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\CustContractSlabRateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustContractSlabRateController
 *
 * Controller class to handle HTTP requests related to CustContractSlabRate entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class CustContractSlabRateController extends Controller
{
    /**
     * The service instance for handling business logic for the CustContractSlabRate entity.
     *
     * @var CustContractSlabRateService
     */
    protected $custContractSlabRateService;

    /**
     * CustContractSlabRateController constructor.
     *
     * @param CustContractSlabRateService $custContractSlabRateService
     */
    public function __construct(CustContractSlabRateService $custContractSlabRateService)
    {
        $this->custContractSlabRateService = $custContractSlabRateService;
    }

    /**
     * Create a new CustContractSlabRate: C
     *
     * @param CustContractSlabRateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustContractSlabRateStoreRequest $request)
    {
        Log::debug('CustContractSlabRate store method called in CustContractSlabRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CustContractSlabRate with validated data
            $custContractSlabRate = $this->custContractSlabRateService->createCustContractSlabRate($validatedData, $userContext);
            $response = response()->json($custContractSlabRate, 201);
            Log::info('CustContractSlabRate store method response from CustContractSlabRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CustContractSlabRate in CustContractSlabRateController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CustContractSlabRate by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CustContractSlabRate show method called in CustContractSlabRateController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CustContractSlabRate by ID
        $custContractSlabRate = $this->custContractSlabRateService->getCustContractSlabRateById($id, $userContext);

        if ($custContractSlabRate) {
            $response = response()->json($custContractSlabRate);
            Log::info('CustContractSlabRate show method response from CustContractSlabRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractSlabRate not found'], 404);
            Log::error('Failed to retrieve CustContractSlabRate in CustContractSlabRateController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CustContractSlabRates with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CustContractSlabRate index method called in CustContractSlabRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CustContractSlabRates based on filters, sorting, and pagination
            $custContractSlabRates = $this->custContractSlabRateService->getAllCustContractSlabRates($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($custContractSlabRates);
            // Extract pagination details
            $paginationDetails = [
                'total' => $custContractSlabRates->total(),
                'per_page' => $custContractSlabRates->perPage(),
                'current_page' => $custContractSlabRates->currentPage(),
                'from' => $custContractSlabRates->firstItem(),
                'to' => $custContractSlabRates->lastItem(),
            ];
            Log::info('CustContractSlabRate index method response from CustContractSlabRateController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustContractSlabRateController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CustContractSlabRate: U
     *
     * @param CustContractSlabRateUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustContractSlabRateUpdateRequest $request, $id)
    {
        Log::debug("CustContractSlabRate update method called in CustContractSlabRateController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CustContractSlabRate with validated data
            $custContractSlabRate = $this->custContractSlabRateService->updateCustContractSlabRate($id, $validatedData, $userContext);
            if (!$custContractSlabRate) {
                $error_response = response()->json(['message' => 'CustContractSlabRate not found or update not possible'], 404);
                Log::error('Failed to update CustContractSlabRate in CustContractSlabRateController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($custContractSlabRate);
            Log::info('CustContractSlabRate update method response from CustContractSlabRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CustContractSlabRate in CustContractSlabRateController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a CustContractSlabRate permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CustContractSlabRate with ID: $id in CustContractSlabRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->custContractSlabRateService->deleteCustContractSlabRate($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CustContractSlabRate deleted successfully'], 200);
            Log::info('CustContractSlabRate destroy method response from CustContractSlabRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractSlabRate not found'], 404);
            Log::error('Failed to delete CustContractSlabRate in CustContractSlabRateController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CustContractSlabRates.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustContractSlabRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->custContractSlabRateService->generateXlsxTemplate($userContext);
            Log::info('CustContractSlabRate xlsxTemplate method response from CustContractSlabRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustContractSlabRateController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CustContractSlabRates from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CustContractSlabRates from xlsx in CustContractSlabRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CustContractSlabRates from the provided Excel file
            $result = $this->custContractSlabRateService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CustContractSlabRate importXlsx method response from CustContractSlabRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustContractSlabRateController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CustContractSlabRates to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CustContractSlabRates to xlsx in CustContractSlabRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CustContractSlabRates to an Excel file
            $filePath = $this->custContractSlabRateService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CustContractSlabRate exportXlsx method response from CustContractSlabRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustContractSlabRateController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
