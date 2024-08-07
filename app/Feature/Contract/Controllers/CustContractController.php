<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\CustContractStoreRequest;
use App\Feature\Contract\Requests\CustContractUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\CustContractService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustContractController
 *
 * Controller class to handle HTTP requests related to CustContract entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class CustContractController extends Controller
{
    /**
     * The service instance for handling business logic for the CustContract entity.
     *
     * @var CustContractService
     */
    protected $custContractService;

    /**
     * CustContractController constructor.
     *
     * @param CustContractService $custContractService
     */
    public function __construct(CustContractService $custContractService)
    {
        $this->custContractService = $custContractService;
    }

    /**
     * Create a new CustContract: C
     *
     * @param CustContractStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustContractStoreRequest $request)
    {
        Log::debug('CustContract store method called in CustContractController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CustContract with validated data
            $custContract = $this->custContractService->createCustContract($validatedData, $userContext);
            $response = response()->json($custContract, 201);
            Log::info('CustContract store method response from CustContractController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CustContract in CustContractController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CustContract by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CustContract show method called in CustContractController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CustContract by ID
        $custContract = $this->custContractService->getCustContractById($id, $userContext);

        if ($custContract) {
            $response = response()->json($custContract);
            Log::info('CustContract show method response from CustContractController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContract not found'], 404);
            Log::error('Failed to retrieve CustContract in CustContractController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CustContracts with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CustContract index method called in CustContractController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CustContracts based on filters, sorting, and pagination
            $custContracts = $this->custContractService->getAllCustContracts($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($custContracts);
            // Extract pagination details
            $paginationDetails = [
                'total' => $custContracts->total(),
                'per_page' => $custContracts->perPage(),
                'current_page' => $custContracts->currentPage(),
                'from' => $custContracts->firstItem(),
                'to' => $custContracts->lastItem(),
            ];
            Log::info('CustContract index method response from CustContractController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustContractController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CustContract: U
     *
     * @param CustContractUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustContractUpdateRequest $request, $id)
    {
        Log::debug("CustContract update method called in CustContractController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CustContract with validated data
            $custContract = $this->custContractService->updateCustContract($id, $validatedData, $userContext);
            if (!$custContract) {
                $error_response = response()->json(['message' => 'CustContract not found or update not possible'], 404);
                Log::error('Failed to update CustContract in CustContractController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($custContract);
            Log::info('CustContract update method response from CustContractController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CustContract in CustContractController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a CustContract (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating CustContract with ID: $id in CustContractController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate CustContract by ID
        $custContract = $this->custContractService->deactivateCustContract($id, $userContext);

        if ($custContract) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'CustContract deactivated successfully'], 200);
            Log::info('CustContract deactivate method response from CustContractController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContract not found or already deactivated'], 404);
            Log::error('Failed to deactivate CustContract in CustContractController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a CustContract permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CustContract with ID: $id in CustContractController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->custContractService->deleteCustContract($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CustContract deleted successfully'], 200);
            Log::info('CustContract destroy method response from CustContractController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContract not found'], 404);
            Log::error('Failed to delete CustContract in CustContractController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CustContracts.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustContractController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->custContractService->generateXlsxTemplate($userContext);
            Log::info('CustContract xlsxTemplate method response from CustContractController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustContractController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CustContracts from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CustContracts from xlsx in CustContractController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CustContracts from the provided Excel file
            $result = $this->custContractService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CustContract importXlsx method response from CustContractController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustContractController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CustContracts to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CustContracts to xlsx in CustContractController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CustContracts to an Excel file
            $filePath = $this->custContractService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CustContract exportXlsx method response from CustContractController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustContractController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
