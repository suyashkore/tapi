<?php

namespace App\Feature\Office\Controllers;

use App\Feature\Office\Requests\OfficeStoreRequest;
use App\Feature\Office\Requests\OfficeUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Office\Services\OfficeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class OfficeController
 *
 * Controller class to handle HTTP requests related to Office entity.
 *
 * @package App\Feature\Office\Controllers
 */
class OfficeController extends Controller
{
    /**
     * The service instance for handling business logic for the Office entity.
     *
     * @var OfficeService
     */
    protected $officeService;

    /**
     * OfficeController constructor.
     *
     * @param OfficeService $officeService
     */
    public function __construct(OfficeService $officeService)
    {
        $this->officeService = $officeService;
    }

    /**
     * Create a new Office: C
     *
     * @param OfficeStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OfficeStoreRequest $request)
    {
        Log::debug('Office store method called in OfficeController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Office with validated data
            $office = $this->officeService->createOffice($validatedData, $userContext);
            $response = response()->json($office, 201);
            Log::info('Office store method response from OfficeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Office in OfficeController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Office by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Office show method called in OfficeController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Office by ID
        $office = $this->officeService->getOfficeById($id, $userContext);

        if ($office) {
            $response = response()->json($office);
            Log::info('Office show method response from OfficeController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Office not found'], 404);
            Log::error('Failed to retrieve Office in OfficeController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Offices with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Office index method called in OfficeController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Offices based on filters, sorting, and pagination
            $offices = $this->officeService->getAllOffices($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($offices);
            // Extract pagination details
            $paginationDetails = [
                'total' => $offices->total(),
                'per_page' => $offices->perPage(),
                'current_page' => $offices->currentPage(),
                'from' => $offices->firstItem(),
                'to' => $offices->lastItem(),
            ];
            Log::info('Office index method response from OfficeController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in OfficeController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Office: U
     *
     * @param OfficeUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OfficeUpdateRequest $request, $id)
    {
        Log::debug("Office update method called in OfficeController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Office with validated data
            $office = $this->officeService->updateOffice($id, $validatedData, $userContext);
            if (!$office) {
                $error_response = response()->json(['message' => 'Office not found or update not possible'], 404);
                Log::error('Failed to update Office in OfficeController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json(['office'=>$office,'id' => $id, 'updated' => true, 'message' => 'Office updated successfully'], 200);
            Log::info('Office update method response from OfficeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Office in OfficeController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a Office (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Office with ID: $id in OfficeController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Office by ID
        $office = $this->officeService->deactivateOffice($id, $userContext);

        if ($office) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Office deactivated successfully'], 200);
            Log::info('Office deactivate method response from OfficeController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Office not found or already deactivated'], 404);
            Log::error('Failed to deactivate Office in OfficeController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Office permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Office with ID: $id in OfficeController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->officeService->deleteOffice($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Office deleted successfully'], 200);
            Log::info('Office destroy method response from OfficeController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Office not found'], 404);
            Log::error('Failed to delete Office in OfficeController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Offices.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in OfficeController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->officeService->generateXlsxTemplate($userContext);
            Log::info('Office xlsxTemplate method response from OfficeController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in OfficeController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Offices from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Offices from xlsx in OfficeController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Offices from the provided Excel file
            $result = $this->officeService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Office importXlsx method response from OfficeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in OfficeController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Offices to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Offices to xlsx in OfficeController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Offices to an Excel file
            $filePath = $this->officeService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Office exportXlsx method response from OfficeController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in OfficeController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
