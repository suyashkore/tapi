<?php

namespace App\Feature\Tenant\Controllers;

use App\Feature\Tenant\Requests\TenantKycStoreRequest;
use App\Feature\Tenant\Requests\TenantKycUpdateRequest;
use App\Feature\Shared\Requests\UploadImgOrFileRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Tenant\Services\TenantKycService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class TenantKycController
 *
 * Controller class to handle HTTP requests related to TenantKyc entity.
 *
 * @package App\Feature\Tenant\Controllers
 */
class TenantKycController extends Controller
{
    /**
     * The service instance for handling business logic for the TenantKyc entity.
     *
     * @var TenantKycService
     */
    protected $tenantKycService;

    /**
     * TenantKycController constructor.
     *
     * @param TenantKycService $tenantKycService
     */
    public function __construct(TenantKycService $tenantKycService)
    {
        $this->tenantKycService = $tenantKycService;
    }

    /**
     * Create a new TenantKyc: C
     *
     * @param TenantKycStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TenantKycStoreRequest $request)
    {
        Log::debug('TenantKyc store method called in TenantKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new TenantKyc with validated data
            $tenantKyc = $this->tenantKycService->createTenantKyc($validatedData, $userContext);
            $response = response()->json($tenantKyc, 201);
            Log::info('TenantKyc store method response from TenantKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create TenantKyc in TenantKycController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single TenantKyc by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("TenantKyc show method called in TenantKycController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch TenantKyc by ID
        $tenantKyc = $this->tenantKycService->getTenantKycById($id, $userContext);

        if ($tenantKyc) {
            $response = response()->json($tenantKyc);
            Log::info('TenantKyc show method response from TenantKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'TenantKyc not found'], 404);
            Log::error('Failed to retrieve TenantKyc in TenantKycController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of TenantKycs with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("TenantKyc index method called in TenantKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch TenantKycs based on filters, sorting, and pagination
            $tenantKycs = $this->tenantKycService->getAllTenantKycs($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($tenantKycs);
            // Extract pagination details
            $paginationDetails = [
                'total' => $tenantKycs->total(),
                'per_page' => $tenantKycs->perPage(),
                'current_page' => $tenantKycs->currentPage(),
                'from' => $tenantKycs->firstItem(),
                'to' => $tenantKycs->lastItem(),
            ];
            Log::info('TenantKyc index method response from TenantKycController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in TenantKycController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing TenantKyc: U
     *
     * @param TenantKycUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TenantKycUpdateRequest $request, $id)
    {
        Log::debug("TenantKyc update method called in TenantKycController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update TenantKyc with validated data
            $tenantKyc = $this->tenantKycService->updateTenantKyc($id, $validatedData, $userContext);
            if (!$tenantKyc) {
                $error_response = response()->json(['message' => 'TenantKyc not found or update not possible'], 404);
                Log::error('Failed to update TenantKyc in TenantKycController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($tenantKyc);
            Log::info('TenantKyc update method response from TenantKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update TenantKyc in TenantKycController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
    * Upload an image or file for a TenantKyc: U
    *
    * @param UploadImgOrFileRequest $request
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function uploadImgOrFile(UploadImgOrFileRequest $request, $id)
    {
        Log::debug("Uploading a file for TenantKyc with ID: $id in TenantKycController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload file and get the URL
            $fileUrl = $this->tenantKycService->uploadImgOrFileSrvc($id, $validatedData['file'], $validatedData['urlfield_name'], $userContext);
            $response = response()->json([$validatedData['urlfield_name'] => $fileUrl], 200);
            Log::info('TenantKyc uploadImgOrFile method response from TenantKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload file in TenantKycController@uploadImgOrFile: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a TenantKyc (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating TenantKyc with ID: $id in TenantKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate TenantKyc by ID
        $tenantKyc = $this->tenantKycService->deactivateTenantKyc($id, $userContext);

        if ($tenantKyc) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'TenantKyc deactivated successfully'], 200);
            Log::info('TenantKyc deactivate method response from TenantKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'TenantKyc not found or already deactivated'], 404);
            Log::error('Failed to deactivate TenantKyc in TenantKycController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a TenantKyc permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete TenantKyc with ID: $id in TenantKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->tenantKycService->deleteTenantKyc($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'TenantKyc deleted successfully'], 200);
            Log::info('TenantKyc destroy method response from TenantKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'TenantKyc not found'], 404);
            Log::error('Failed to delete TenantKyc in TenantKycController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing TenantKycs.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in TenantKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->tenantKycService->generateXlsxTemplate($userContext);
            Log::info('TenantKyc xlsxTemplate method response from TenantKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in TenantKycController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import TenantKycs from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing TenantKycs from xlsx in TenantKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import TenantKycs from the provided Excel file
            $result = $this->tenantKycService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('TenantKyc importXlsx method response from TenantKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in TenantKycController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export TenantKycs to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting TenantKycs to xlsx in TenantKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export TenantKycs to an Excel file
            $filePath = $this->tenantKycService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('TenantKyc exportXlsx method response from TenantKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in TenantKycController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
