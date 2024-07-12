<?php

namespace App\Feature\Office\Controllers;

use App\Feature\Office\Requests\CpKycStoreRequest;
use App\Feature\Office\Requests\CpKycUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Office\Services\CpKycService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CpKycController
 *
 * Controller class to handle HTTP requests related to CpKyc entity.
 *
 * @package App\Feature\Office\Controllers
 */
class CpKycController extends Controller
{
    /**
     * The service instance for handling business logic for the CpKyc entity.
     *
     * @var CpKycService
     */
    protected $cpKycService;

    /**
     * CpKycController constructor.
     *
     * @param CpKycService $cpKycService
     */
    public function __construct(CpKycService $cpKycService)
    {
        $this->cpKycService = $cpKycService;
    }

    /**
     * Create a new CpKyc: C
     *
     * @param CpKycStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CpKycStoreRequest $request)
    {
        Log::debug('CpKyc store method called in CpKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new CpKyc with validated data
            $cpKyc = $this->cpKycService->createCpKyc($validatedData, $userContext);
            $response = response()->json($cpKyc, 201);
            Log::info('CpKyc store method response from CpKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create CpKyc in CpKycController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single CpKyc by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("CpKyc show method called in CpKycController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch CpKyc by ID
        $cpKyc = $this->cpKycService->getCpKycById($id, $userContext);

        if ($cpKyc) {
            $response = response()->json($cpKyc);
            Log::info('CpKyc show method response from CpKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CpKyc not found'], 404);
            Log::error('Failed to retrieve CpKyc in CpKycController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of CpKycs with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("CpKyc index method called in CpKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model CpKyc
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch CpKycs based on filters, sorting, and pagination
            $cpKycs = $this->cpKycService->getAllCpKycs($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($cpKycs);
            // Extract pagination details
            $paginationDetails = [
                'total' => $cpKycs->total(),
                'per_page' => $cpKycs->perPage(),
                'current_page' => $cpKycs->currentPage(),
                'from' => $cpKycs->firstItem(),
                'to' => $cpKycs->lastItem(),
            ];
            Log::info('CpKyc index method response from CpKycController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CpKycController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing CpKyc: U
     *
     * @param CpKycUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CpKycUpdateRequest $request, $id)
    {
        Log::debug("CpKyc update method called in CpKycController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update CpKyc with validated data
            $cpKyc = $this->cpKycService->updateCpKyc($id, $validatedData, $userContext);
            if (!$cpKyc) {
                $error_response = response()->json(['message' => 'CpKyc not found or update not possible'], 404);
                Log::error('Failed to update CpKyc in CpKycController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($cpKyc);
            Log::info('CpKyc update method response from CpKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update CpKyc in CpKycController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a CpKyc (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating CpKyc with ID: $id in CpKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate CpKyc by ID
        $cpKyc = $this->cpKycService->deactivateCpKyc($id, $userContext);

        if ($cpKyc) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'CpKyc deactivated successfully'], 200);
            Log::info('CpKyc deactivate method response from CpKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CpKyc not found or already deactivated'], 404);
            Log::error('Failed to deactivate CpKyc in CpKycController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a CpKyc permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete CpKyc with ID: $id in CpKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->cpKycService->deleteCpKyc($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'CpKyc deleted successfully'], 200);
            Log::info('CpKyc destroy method response from CpKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'CpKyc not found'], 404);
            Log::error('Failed to delete CpKyc in CpKycController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing CpKycs.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CpKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->cpKycService->generateXlsxTemplate($userContext);
            Log::info('CpKyc xlsxTemplate method response from CpKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CpKycController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import CpKycs from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing CpKycs from xlsx in CpKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import CpKycs from the provided Excel file
            $result = $this->cpKycService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('CpKyc importXlsx method response from CpKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CpKycController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export CpKycs to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting CpKycs to xlsx in CpKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model CpKyc
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export CpKycs to an Excel file
            $filePath = $this->cpKycService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('CpKyc exportXlsx method response from CpKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CpKycController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
