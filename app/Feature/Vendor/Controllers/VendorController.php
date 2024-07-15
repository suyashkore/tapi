<?php

namespace App\Feature\Vendor\Controllers;

use App\Feature\Vendor\Requests\VendorStoreRequest;
use App\Feature\Vendor\Requests\VendorUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Vendor\Services\VendorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class VendorController
 *
 * Controller class to handle HTTP requests related to Vendor entity.
 *
 * @package App\Feature\Vendor\Controllers
 */
class VendorController extends Controller
{
    /**
     * The service instance for handling business logic for the Vendor entity.
     *
     * @var VendorService
     */
    protected $vendorService;

    /**
     * VendorController constructor.
     *
     * @param VendorService $vendorService
     */
    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * Create a new Vendor: C
     *
     * @param VendorStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VendorStoreRequest $request)
    {
        Log::debug('Vendor store method called in VendorController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Vendor with validated data
            $vendor = $this->vendorService->createVendor($validatedData, $userContext);
            $response = response()->json($vendor, 201);
            Log::info('Vendor store method response from VendorController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Vendor in VendorController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Vendor by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Vendor show method called in VendorController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Vendor by ID
        $vendor = $this->vendorService->getVendorById($id, $userContext);

        if ($vendor) {
            $response = response()->json($vendor);
            Log::info('Vendor show method response from VendorController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vendor not found'], 404);
            Log::error('Failed to retrieve Vendor in VendorController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Vendors with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Vendor index method called in VendorController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model Vendor
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Vendors based on filters, sorting, and pagination
            $vendors = $this->vendorService->getAllVendors($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($vendors);
            // Extract pagination details
            $paginationDetails = [
                'total' => $vendors->total(),
                'per_page' => $vendors->perPage(),
                'current_page' => $vendors->currentPage(),
                'from' => $vendors->firstItem(),
                'to' => $vendors->lastItem(),
            ];
            Log::info('Vendor index method response from VendorController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in VendorController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Vendor: U
     *
     * @param VendorUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VendorUpdateRequest $request, $id)
    {
        Log::debug("Vendor update method called in VendorController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Vendor with validated data
            $vendor = $this->vendorService->updateVendor($id, $validatedData, $userContext);
            if (!$vendor) {
                $error_response = response()->json(['message' => 'Vendor not found or update not possible'], 404);
                Log::error('Failed to update Vendor in VendorController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($vendor);
            Log::info('Vendor update method response from VendorController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Vendor in VendorController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    //TODO: Remove below method if not required.
    /**
     * Upload an image for a Vendor: U
     *
     * @param UploadImageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(UploadImageRequest $request, $id)
    {
        Log::debug("Uploading an image for Vendor with ID: $id in VendorController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload image and get the URL
            $imageUrl = $this->vendorService->uploadImage($id, $validatedData['img'], $userContext);
            //TODO: Replace 'image_url' with the real field name
            $response = response()->json(['image_url' => $imageUrl], 200);
            Log::info('Vendor uploadImage method response from VendorController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload image in VendorController@uploadImage: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a Vendor (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Vendor with ID: $id in VendorController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Vendor by ID
        $vendor = $this->vendorService->deactivateVendor($id, $userContext);

        if ($vendor) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Vendor deactivated successfully'], 200);
            Log::info('Vendor deactivate method response from VendorController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vendor not found or already deactivated'], 404);
            Log::error('Failed to deactivate Vendor in VendorController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Vendor permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Vendor with ID: $id in VendorController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->vendorService->deleteVendor($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Vendor deleted successfully'], 200);
            Log::info('Vendor destroy method response from VendorController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vendor not found'], 404);
            Log::error('Failed to delete Vendor in VendorController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Vendors.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in VendorController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->vendorService->generateXlsxTemplate($userContext);
            Log::info('Vendor xlsxTemplate method response from VendorController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in VendorController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Vendors from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Vendors from xlsx in VendorController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Vendors from the provided Excel file
            $result = $this->vendorService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Vendor importXlsx method response from VendorController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in VendorController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Vendors to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Vendors to xlsx in VendorController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model Vendor
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Vendors to an Excel file
            $filePath = $this->vendorService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Vendor exportXlsx method response from VendorController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in VendorController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
