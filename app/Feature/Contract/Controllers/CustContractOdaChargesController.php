<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\CustContractOdaChargesStoreRequest;
use App\Feature\Contract\Requests\CustContractOdaChargesUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\CustContractOdaChargesService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustContractOdaChargesController
 *
 * Controller class to handle HTTP requests related to CustContractOdaCharges entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class CustContractOdaChargesController extends Controller
{
    /**
     * The service instance for handling business logic for the CustContractOdaCharges entity.
     *
     * @var CustContractOdaChargesService
     */
    protected $custContractOdaChargesService;

    /**
     * CustContractOdaChargesController constructor.
     *
     * @param CustContractOdaChargesService $custContractOdaChargesService
     */
    public function __construct(CustContractOdaChargesService $custContractOdaChargesService)
    {
        $this->custContractOdaChargesService = $custContractOdaChargesService;
    }

    /**
     * Create a new CustContractOdaCharges: C
     *
     * @param CustContractOdaChargesStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustContractOdaChargesStoreRequest $request)
    {
        Log::debug('CustContractOdaCharges store method called in CustContractOdaChargesController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CustContractOdaCharges with validated data
            $custContractOdaCharges = $this->custContractOdaChargesService->createCustContractOdaCharges($validatedData, $userContext);
            $response = response()->json($custContractOdaCharges, 201);
            Log::info('CustContractOdaCharges store method response from CustContractOdaChargesController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CustContractOdaCharges in CustContractOdaChargesController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CustContractOdaCharges by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CustContractOdaCharges show method called in CustContractOdaChargesController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CustContractOdaCharges by ID
        $custContractOdaCharges = $this->custContractOdaChargesService->getCustContractOdaChargesById($id, $userContext);

        if ($custContractOdaCharges) {
            $response = response()->json($custContractOdaCharges);
            Log::info('CustContractOdaCharges show method response from CustContractOdaChargesController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractOdaCharges not found'], 404);
            Log::error('Failed to retrieve CustContractOdaCharges in CustContractOdaChargesController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CustContractOdaCharges with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CustContractOdaCharges index method called in CustContractOdaChargesController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CustContractOdaCharges based on filters, sorting, and pagination
            $custContractOdaCharges = $this->custContractOdaChargesService->getAllCustContractOdaCharges($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($custContractOdaCharges);
            // Extract pagination details
            $paginationDetails = [
                'total' => $custContractOdaCharges->total(),
                'per_page' => $custContractOdaCharges->perPage(),
                'current_page' => $custContractOdaCharges->currentPage(),
                'from' => $custContractOdaCharges->firstItem(),
                'to' => $custContractOdaCharges->lastItem(),
            ];
            Log::info('CustContractOdaCharges index method response from CustContractOdaChargesController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustContractOdaChargesController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CustContractOdaCharges: U
     *
     * @param CustContractOdaChargesUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustContractOdaChargesUpdateRequest $request, $id)
    {
        Log::debug("CustContractOdaCharges update method called in CustContractOdaChargesController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CustContractOdaCharges with validated data
            $custContractOdaCharges = $this->custContractOdaChargesService->updateCustContractOdaCharges($id, $validatedData, $userContext);
            if (!$custContractOdaCharges) {
                $error_response = response()->json(['message' => 'CustContractOdaCharges not found or update not possible'], 404);
                Log::error('Failed to update CustContractOdaCharges in CustContractOdaChargesController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($custContractOdaCharges);
            Log::info('CustContractOdaCharges update method response from CustContractOdaChargesController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CustContractOdaCharges in CustContractOdaChargesController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a CustContractOdaCharges permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CustContractOdaCharges with ID: $id in CustContractOdaChargesController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->custContractOdaChargesService->deleteCustContractOdaCharges($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CustContractOdaCharges deleted successfully'], 200);
            Log::info('CustContractOdaCharges destroy method response from CustContractOdaChargesController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractOdaCharges not found'], 404);
            Log::error('Failed to delete CustContractOdaCharges in CustContractOdaChargesController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CustContractOdaCharges.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustContractOdaChargesController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->custContractOdaChargesService->generateXlsxTemplate($userContext);
            Log::info('CustContractOdaCharges xlsxTemplate method response from CustContractOdaChargesController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustContractOdaChargesController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CustContractOdaCharges from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CustContractOdaCharges from xlsx in CustContractOdaChargesController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CustContractOdaCharges from the provided Excel file
            $result = $this->custContractOdaChargesService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CustContractOdaCharges importXlsx method response from CustContractOdaChargesController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustContractOdaChargesController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CustContractOdaCharges to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CustContractOdaCharges to xlsx in CustContractOdaChargesController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CustContractOdaCharges to an Excel file
            $filePath = $this->custContractOdaChargesService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CustContractOdaCharges exportXlsx method response from CustContractOdaChargesController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustContractOdaChargesController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
