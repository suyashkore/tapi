<?php

namespace App\Feature\Vendor\Controllers;

use App\Feature\Vendor\Requests\VendorKycStoreRequest;
use App\Feature\Vendor\Requests\VendorKycUpdateRequest;
use App\Feature\Shared\Requests\UploadImgOrFileRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Vendor\Services\VendorKycService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class VendorKycController
 *
 * Controller class to handle HTTP requests related to VendorKyc entity.
 *
 * @package App\Feature\Vendor\Controllers
 */
class VendorKycController extends Controller
{
    /**
     * The service instance for handling business logic for the VendorKyc entity.
     *
     * @var VendorKycService
     */
    protected $vendorKycService;

    /**
     * VendorKycController constructor.
     *
     * @param VendorKycService $vendorKycService
     */
    public function __construct(VendorKycService $vendorKycService)
    {
        $this->vendorKycService = $vendorKycService;
    }

    /**
     * Create a new VendorKyc: C
     *
     * @param VendorKycStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VendorKycStoreRequest $request)
    {
        Log::debug('VendorKyc store method called in VendorKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new VendorKyc with validated data
            $vendorKyc = $this->vendorKycService->createVendorKyc($validatedData, $userContext);
            $response = response()->json($vendorKyc, 201);
            Log::info('VendorKyc store method response from VendorKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create VendorKyc in VendorKycController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single VendorKyc by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("VendorKyc show method called in VendorKycController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch VendorKyc by ID
        $vendorKyc = $this->vendorKycService->getVendorKycById($id, $userContext);

        if ($vendorKyc) {
            $response = response()->json($vendorKyc);
            Log::info('VendorKyc show method response from VendorKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'VendorKyc not found'], 404);
            Log::error('Failed to retrieve VendorKyc in VendorKycController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of VendorKycs with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("VendorKyc index method called in VendorKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch VendorKycs based on filters, sorting, and pagination
            $vendorKycs = $this->vendorKycService->getAllVendorKycs($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($vendorKycs);
            // Extract pagination details
            $paginationDetails = [
                'total' => $vendorKycs->total(),
                'per_page' => $vendorKycs->perPage(),
                'current_page' => $vendorKycs->currentPage(),
                'from' => $vendorKycs->firstItem(),
                'to' => $vendorKycs->lastItem(),
            ];
            Log::info('VendorKyc index method response from VendorKycController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in VendorKycController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing VendorKyc: U
     *
     * @param VendorKycUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VendorKycUpdateRequest $request, $id)
    {
        Log::debug("VendorKyc update method called in VendorKycController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update VendorKyc with validated data
            $vendorKyc = $this->vendorKycService->updateVendorKyc($id, $validatedData, $userContext);
            if (!$vendorKyc) {
                $error_response = response()->json(['message' => 'VendorKyc not found or update not possible'], 404);
                Log::error('Failed to update VendorKyc in VendorKycController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($vendorKyc);
            Log::info('VendorKyc update method response from VendorKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update VendorKyc in VendorKycController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
    * Upload an image or file for a VendorKyc: U
    *
    * @param UploadImgOrFileRequest $request
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function uploadImgOrFile(UploadImgOrFileRequest $request, $id)
    {
        Log::debug("Uploading a file for VendorKyc with ID: $id in VendorKycController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload file and get the URL
            $fileUrl = $this->vendorKycService->uploadImgOrFileSrvc($id, $validatedData['file'], $validatedData['urlfield_name'], $userContext);
            $response = response()->json([$validatedData['urlfield_name'] => $fileUrl], 200);
            Log::info('VendorKyc uploadImgOrFile method response from VendorKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload file in VendorKycController@uploadImgOrFile: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a VendorKyc (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating VendorKyc with ID: $id in VendorKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate VendorKyc by ID
        $vendorKyc = $this->vendorKycService->deactivateVendorKyc($id, $userContext);

        if ($vendorKyc) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'VendorKyc deactivated successfully'], 200);
            Log::info('VendorKyc deactivate method response from VendorKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'VendorKyc not found or already deactivated'], 404);
            Log::error('Failed to deactivate VendorKyc in VendorKycController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a VendorKyc permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete VendorKyc with ID: $id in VendorKycController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->vendorKycService->deleteVendorKyc($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'VendorKyc deleted successfully'], 200);
            Log::info('VendorKyc destroy method response from VendorKycController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'VendorKyc not found'], 404);
            Log::error('Failed to delete VendorKyc in VendorKycController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing VendorKycs.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in VendorKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->vendorKycService->generateXlsxTemplate($userContext);
            Log::info('VendorKyc xlsxTemplate method response from VendorKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in VendorKycController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import VendorKycs from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing VendorKycs from xlsx in VendorKycController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import VendorKycs from the provided Excel file
            $result = $this->vendorKycService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('VendorKyc importXlsx method response from VendorKycController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in VendorKycController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export VendorKycs to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting VendorKycs to xlsx in VendorKycController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export VendorKycs to an Excel file
            $filePath = $this->vendorKycService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('VendorKyc exportXlsx method response from VendorKycController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in VendorKycController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}