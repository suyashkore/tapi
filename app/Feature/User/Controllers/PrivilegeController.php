<?php

namespace App\Feature\User\Controllers;

use App\Feature\User\Requests\PrivilegeStoreRequest;
use App\Feature\User\Requests\PrivilegeUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\User\Services\PrivilegeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class PrivilegeController
 *
 * Controller class to handle HTTP requests related to Privilege entity.
 *
 * @package App\Feature\User\Controllers
 */
class PrivilegeController extends Controller
{
    /**
     * The service instance for handling business logic for the Privilege entity.
     *
     * @var PrivilegeService
     */
    protected $privilegeService;

    /**
     * PrivilegeController constructor.
     *
     * @param PrivilegeService $privilegeService
     */
    public function __construct(PrivilegeService $privilegeService)
    {
        $this->privilegeService = $privilegeService;
    }

    /**
     * Create a new Privilege: C
     *
     * @param PrivilegeStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrivilegeStoreRequest $request)
    {
        Log::debug('Privilege store method called in PrivilegeController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Privilege with validated data
            $privilege = $this->privilegeService->createPrivilege($validatedData, $userContext);
            $response = response()->json($privilege, 201);
            Log::info('Privilege store method response from PrivilegeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Privilege in PrivilegeController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Privilege by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Privilege show method called in PrivilegeController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Privilege by ID
        $privilege = $this->privilegeService->getPrivilegeById($id, $userContext);

        if ($privilege) {
            $response = response()->json($privilege);
            Log::info('Privilege show method response from PrivilegeController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Privilege not found'], 404);
            Log::error('Failed to retrieve Privilege in PrivilegeController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Privileges with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Privilege index method called in PrivilegeController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Privileges based on filters, sorting, and pagination
            $privileges = $this->privilegeService->getAllPrivileges($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($privileges);
            // Extract pagination details
            $paginationDetails = [
                'total' => $privileges->total(),
                'per_page' => $privileges->perPage(),
                'current_page' => $privileges->currentPage(),
                'from' => $privileges->firstItem(),
                'to' => $privileges->lastItem(),
            ];
            Log::info('Privilege index method response from PrivilegeController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in PrivilegeController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Privilege: U
     *
     * @param PrivilegeUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrivilegeUpdateRequest $request, $id)
    {
        Log::debug("Privilege update method called in PrivilegeController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Privilege with validated data
            $privilege = $this->privilegeService->updatePrivilege($id, $validatedData, $userContext);
            if (!$privilege) {
                $error_response = response()->json(['message' => 'Privilege not found or update not possible'], 404);
                Log::error('Failed to update Privilege in PrivilegeController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($privilege);
            Log::info('Privilege update method response from PrivilegeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Privilege in PrivilegeController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a Privilege permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Privilege with ID: $id in PrivilegeController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->privilegeService->deletePrivilege($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Privilege deleted successfully'], 200);
            Log::info('Privilege destroy method response from PrivilegeController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Privilege not found'], 404);
            Log::error('Failed to delete Privilege in PrivilegeController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Privileges.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in PrivilegeController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->privilegeService->generateXlsxTemplate($userContext);
            Log::info('Privilege xlsxTemplate method response from PrivilegeController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in PrivilegeController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Privileges from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Privileges from xlsx in PrivilegeController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Privileges from the provided Excel file
            $result = $this->privilegeService->importFromXlsx($validatedData['file'], $userContext);

            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';

            // Set the response message and status
            $response = response()->json([
                'message' => $message,
                'data' => $result
            ], $status);

            Log::info('Privilege importXlsx method response from PrivilegeController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in PrivilegeController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Privileges to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Privileges to xlsx in PrivilegeController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Privileges to an Excel file
            $filePath = $this->privilegeService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Privilege exportXlsx method response from PrivilegeController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in PrivilegeController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
