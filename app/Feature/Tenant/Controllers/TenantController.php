<?php

namespace App\Feature\Tenant\Controllers;

use App\Feature\Tenant\Requests\TenantStoreRequest;
use App\Feature\Tenant\Requests\TenantUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Tenant\Services\TenantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class TenantController
 *
 * Controller class to handle HTTP requests related to Tenant entity.
 *
 * @package App\Feature\Tenant\Controllers
 */
class TenantController extends Controller
{
    /**
     * The service instance for handling business logic for the Tenant entity.
     *
     * @var TenantService
     */
    protected $tenantService;

    /**
     * TenantController constructor.
     *
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Create a new tenant: C
     *
     * @param TenantStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TenantStoreRequest $request)
    {
        Log::debug('Tenant store method called in TenantController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new tenant with validated data
            $tenant = $this->tenantService->createTenant($validatedData, $userContext);
            $response = response()->json($tenant, 201);
            Log::info('Tenant store method response from TenantController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Tenant in TenantController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single tenant by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Tenant show method called in TenantController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch tenant by ID
        $tenant = $this->tenantService->getTenantById($id, $userContext);

        if ($tenant) {
            $response = response()->json($tenant);
            Log::info('Tenant show method response from TenantController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Tenant not found'], 404);
            Log::error('Failed to retrieve Tenant in TenantController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of tenants with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Tenant index method called in TenantController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch tenants based on filters, sorting, and pagination
            $tenants = $this->tenantService->getAllTenants($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($tenants);
            // Extract pagination details
            $paginationDetails = [
                'total' => $tenants->total(),
                'per_page' => $tenants->perPage(),
                'current_page' => $tenants->currentPage(),
                'from' => $tenants->firstItem(),
                'to' => $tenants->lastItem(),
            ];
            Log::info('Tenant index method response from TenantController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in TenantController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing tenant: U
     *
     * @param TenantUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TenantUpdateRequest $request, $id)
    {
        Log::debug("Tenant update method called in TenantController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update tenant with validated data
            $tenant = $this->tenantService->updateTenant($id, $validatedData, $userContext);
            if (!$tenant) {
                $error_response = response()->json(['message' => 'Tenant not found or update not possible'], 404);
                Log::error('Failed to update Tenant in TenantController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($tenant);
            Log::info('Tenant update method response from TenantController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Tenant in TenantController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload a logo image for a tenant: U
     *
     * @param UploadImageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadLogo(UploadImageRequest $request, $id)
    {
        Log::debug("Uploading logo for Tenant with ID: $id in TenantController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload logo image and get the URL
            $logoUrl = $this->tenantService->uploadLogo($id, $validatedData['img'], $userContext);
            $response = response()->json(['logo_url' => $logoUrl], 200);
            Log::info('Tenant uploadLogo method response from TenantController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload logo in TenantController@uploadLogo: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a tenant (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Tenant with ID: $id in TenantController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate tenant by ID
        $tenant = $this->tenantService->deactivateTenant($id, $userContext);

        if ($tenant) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Tenant deactivated successfully'], 200);
            Log::info('Tenant deactivate method response from TenantController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Tenant not found or already deactivated'], 404);
            Log::error('Failed to deactivate Tenant in TenantController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a tenant permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Tenant with ID: $id in TenantController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->tenantService->deleteTenant($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Tenant deleted successfully'], 200);
            Log::info('Tenant destroy method response from TenantController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Tenant not found'], 404);
            Log::error('Failed to delete Tenant in TenantController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing tenants.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in TenantController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->tenantService->generateXlsxTemplate($userContext);
            Log::info('Tenant xlsxTemplate method response from TenantController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in TenantController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import tenants from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing tenants from xlsx in TenantController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import tenants from the provided Excel file
            $result = $this->tenantService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Tenant importXlsx method response from TenantController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in TenantController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export tenants to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting tenants to xlsx in TenantController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export tenants to an Excel file
            $filePath = $this->tenantService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Tenant exportXlsx method response from TenantController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in TenantController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
