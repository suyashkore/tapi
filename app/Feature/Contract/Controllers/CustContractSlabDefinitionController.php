<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\CustContractSlabDefinitionStoreRequest;
use App\Feature\Contract\Requests\CustContractSlabDefinitionUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\CustContractSlabDefinitionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustContractSlabDefinitionController
 *
 * Controller class to handle HTTP requests related to CustContractSlabDefinition entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class CustContractSlabDefinitionController extends Controller
{
    /**
     * The service instance for handling business logic for the CustContractSlabDefinition entity.
     *
     * @var CustContractSlabDefinitionService
     */
    protected $custContractSlabDefinitionService;

    /**
     * CustContractSlabDefinitionController constructor.
     *
     * @param CustContractSlabDefinitionService $custContractSlabDefinitionService
     */
    public function __construct(CustContractSlabDefinitionService $custContractSlabDefinitionService)
    {
        $this->custContractSlabDefinitionService = $custContractSlabDefinitionService;
    }

    /**
     * Create a new CustContractSlabDefinition: C
     *
     * @param CustContractSlabDefinitionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustContractSlabDefinitionStoreRequest $request)
    {
        Log::debug('CustContractSlabDefinition store method called in CustContractSlabDefinitionController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CustContractSlabDefinition with validated data
            $custContractSlabDefinition = $this->custContractSlabDefinitionService->createCustContractSlabDefinition($validatedData, $userContext);
            $response = response()->json($custContractSlabDefinition, 201);
            Log::info('CustContractSlabDefinition store method response from CustContractSlabDefinitionController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CustContractSlabDefinition in CustContractSlabDefinitionController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CustContractSlabDefinition by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CustContractSlabDefinition show method called in CustContractSlabDefinitionController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CustContractSlabDefinition by ID
        $custContractSlabDefinition = $this->custContractSlabDefinitionService->getCustContractSlabDefinitionById($id, $userContext);

        if ($custContractSlabDefinition) {
            $response = response()->json($custContractSlabDefinition);
            Log::info('CustContractSlabDefinition show method response from CustContractSlabDefinitionController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractSlabDefinition not found'], 404);
            Log::error('Failed to retrieve CustContractSlabDefinition in CustContractSlabDefinitionController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CustContractSlabDefinitions with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CustContractSlabDefinition index method called in CustContractSlabDefinitionController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CustContractSlabDefinitions based on filters, sorting, and pagination
            $custContractSlabDefinitions = $this->custContractSlabDefinitionService->getAllCustContractSlabDefinitions($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($custContractSlabDefinitions);
            // Extract pagination details
            $paginationDetails = [
                'total' => $custContractSlabDefinitions->total(),
                'per_page' => $custContractSlabDefinitions->perPage(),
                'current_page' => $custContractSlabDefinitions->currentPage(),
                'from' => $custContractSlabDefinitions->firstItem(),
                'to' => $custContractSlabDefinitions->lastItem(),
            ];
            Log::info('CustContractSlabDefinition index method response from CustContractSlabDefinitionController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustContractSlabDefinitionController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CustContractSlabDefinition: U
     *
     * @param CustContractSlabDefinitionUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustContractSlabDefinitionUpdateRequest $request, $id)
    {
        Log::debug("CustContractSlabDefinition update method called in CustContractSlabDefinitionController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CustContractSlabDefinition with validated data
            $custContractSlabDefinition = $this->custContractSlabDefinitionService->updateCustContractSlabDefinition($id, $validatedData, $userContext);
            if (!$custContractSlabDefinition) {
                $error_response = response()->json(['message' => 'CustContractSlabDefinition not found or update not possible'], 404);
                Log::error('Failed to update CustContractSlabDefinition in CustContractSlabDefinitionController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($custContractSlabDefinition);
            Log::info('CustContractSlabDefinition update method response from CustContractSlabDefinitionController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CustContractSlabDefinition in CustContractSlabDefinitionController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a CustContractSlabDefinition permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CustContractSlabDefinition with ID: $id in CustContractSlabDefinitionController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->custContractSlabDefinitionService->deleteCustContractSlabDefinition($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CustContractSlabDefinition deleted successfully'], 200);
            Log::info('CustContractSlabDefinition destroy method response from CustContractSlabDefinitionController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CustContractSlabDefinition not found'], 404);
            Log::error('Failed to delete CustContractSlabDefinition in CustContractSlabDefinitionController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CustContractSlabDefinitions.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustContractSlabDefinitionController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->custContractSlabDefinitionService->generateXlsxTemplate($userContext);
            Log::info('CustContractSlabDefinition xlsxTemplate method response from CustContractSlabDefinitionController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustContractSlabDefinitionController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CustContractSlabDefinitions from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CustContractSlabDefinitions from xlsx in CustContractSlabDefinitionController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CustContractSlabDefinitions from the provided Excel file
            $result = $this->custContractSlabDefinitionService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CustContractSlabDefinition importXlsx method response from CustContractSlabDefinitionController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustContractSlabDefinitionController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CustContractSlabDefinitions to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CustContractSlabDefinitions to xlsx in CustContractSlabDefinitionController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CustContractSlabDefinitions to an Excel file
            $filePath = $this->custContractSlabDefinitionService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CustContractSlabDefinition exportXlsx method response from CustContractSlabDefinitionController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustContractSlabDefinitionController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
