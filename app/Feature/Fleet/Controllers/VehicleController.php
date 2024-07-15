<?php

namespace App\Feature\Fleet\Controllers;

use App\Feature\Fleet\Requests\VehicleStoreRequest;
use App\Feature\Fleet\Requests\VehicleUpdateRequest;
use App\Feature\Shared\Requests\UploadImgOrFileRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Fleet\Services\VehicleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class VehicleController
 *
 * Controller class to handle HTTP requests related to Vehicle entity.
 *
 * @package App\Feature\Fleet\Controllers
 */
class VehicleController extends Controller
{
    /**
     * The service instance for handling business logic for the Vehicle entity.
     *
     * @var VehicleService
     */
    protected $vehicleService;

    /**
     * VehicleController constructor.
     *
     * @param VehicleService $vehicleService
     */
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Create a new Vehicle: C
     *
     * @param VehicleStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VehicleStoreRequest $request)
    {
        Log::debug('Vehicle store method called in VehicleController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Vehicle with validated data
            $vehicle = $this->vehicleService->createVehicle($validatedData, $userContext);
            $response = response()->json($vehicle, 201);
            Log::info('Vehicle store method response from VehicleController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Vehicle in VehicleController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Vehicle by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Vehicle show method called in VehicleController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Vehicle by ID
        $vehicle = $this->vehicleService->getVehicleById($id, $userContext);

        if ($vehicle) {
            $response = response()->json($vehicle);
            Log::info('Vehicle show method response from VehicleController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vehicle not found'], 404);
            Log::error('Failed to retrieve Vehicle in VehicleController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Vehicles with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Vehicle index method called in VehicleController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Vehicles based on filters, sorting, and pagination
            $vehicles = $this->vehicleService->getAllVehicles($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($vehicles);
            // Extract pagination details
            $paginationDetails = [
                'total' => $vehicles->total(),
                'per_page' => $vehicles->perPage(),
                'current_page' => $vehicles->currentPage(),
                'from' => $vehicles->firstItem(),
                'to' => $vehicles->lastItem(),
            ];
            Log::info('Vehicle index method response from VehicleController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in VehicleController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Vehicle: U
     *
     * @param VehicleUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VehicleUpdateRequest $request, $id)
    {
        Log::debug("Vehicle update method called in VehicleController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Vehicle with validated data
            $vehicle = $this->vehicleService->updateVehicle($id, $validatedData, $userContext);
            if (!$vehicle) {
                $error_response = response()->json(['message' => 'Vehicle not found or update not possible'], 404);
                Log::error('Failed to update Vehicle in VehicleController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($vehicle);
            Log::info('Vehicle update method response from VehicleController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Vehicle in VehicleController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
    * Upload an image or file for a Vehicle: U
    *
    * @param UploadImgOrFileRequest $request
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function uploadImgOrFile(UploadImgOrFileRequest $request, $id)
    {
        Log::debug("Uploading a file for Vehicle with ID: $id in VehicleController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload file and get the URL
            $fileUrl = $this->vehicleService->uploadImgOrFileSrvc($id, $validatedData['file'], $validatedData['urlfield_name'], $userContext);
            $response = response()->json([$validatedData['urlfield_name'] => $fileUrl], 200);
            Log::info('Vehicle uploadImgOrFile method response from VehicleController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload file in VehicleController@uploadImgOrFile: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a Vehicle (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Vehicle with ID: $id in VehicleController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Vehicle by ID
        $vehicle = $this->vehicleService->deactivateVehicle($id, $userContext);

        if ($vehicle) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Vehicle deactivated successfully'], 200);
            Log::info('Vehicle deactivate method response from VehicleController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vehicle not found or already deactivated'], 404);
            Log::error('Failed to deactivate Vehicle in VehicleController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Vehicle permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Vehicle with ID: $id in VehicleController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->vehicleService->deleteVehicle($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Vehicle deleted successfully'], 200);
            Log::info('Vehicle destroy method response from VehicleController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Vehicle not found'], 404);
            Log::error('Failed to delete Vehicle in VehicleController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Vehicles.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in VehicleController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->vehicleService->generateXlsxTemplate($userContext);
            Log::info('Vehicle xlsxTemplate method response from VehicleController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in VehicleController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Vehicles from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Vehicles from xlsx in VehicleController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Vehicles from the provided Excel file
            $result = $this->vehicleService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Vehicle importXlsx method response from VehicleController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in VehicleController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Vehicles to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Vehicles to xlsx in VehicleController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Vehicles to an Excel file
            $filePath = $this->vehicleService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Vehicle exportXlsx method response from VehicleController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in VehicleController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
