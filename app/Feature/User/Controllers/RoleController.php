<?php

namespace App\Feature\User\Controllers;

use App\Feature\User\Requests\RoleStoreRequest;
use App\Feature\User\Requests\RoleUpdateRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\User\Services\RoleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class RoleController
 *
 * Controller class to handle HTTP requests related to Role entity.
 *
 * @package App\Feature\User\Controllers
 */
class RoleController extends Controller
{
    /**
     * The service instance for handling business logic for the Role entity.
     *
     * @var RoleService
     */
    protected $roleService;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Create a new Role: C
     *
     * @param RoleStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleStoreRequest $request)
    {
        Log::debug('Role store method called in RoleController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Role with validated data
            $role = $this->roleService->createRole($validatedData, $userContext);

            // Handle privileges if provided
            if (isset($validatedData['privileges'])) {
                $this->roleService->assignPrivilegesToRole($role, $validatedData['privileges']);
            }
            // Ensure the role is not null
            if ($role) {
                // Load the privileges relationship
                $role->load('privileges:id,name');
                $response = response()->json($role, 201);
                Log::info('Role store method response from RoleController: ', $response->getData(true));
                return $response;
            } else {
                $error_response = response()->json(['message' => 'Failed to create role'], 500);
                Log::error('Role creation returned null in RoleController@store:', $error_response->getData(true));
                return $error_response;
            }
        } catch (\Exception $e) {
            Log::error('Failed to create Role in RoleController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }


    /**
     * Retrieve a single Role by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Role show method called in RoleController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Role by ID
        $role = $this->roleService->getRoleById($id, $userContext);

        if ($role) {
            // Load the privileges relationship
            $role->load('privileges:id,name');
            $response = response()->json($role);
            Log::info('Role show method response from RoleController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Role not found'], 404);
            Log::error('Failed to retrieve Role in RoleController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Roles with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Role index method called in RoleController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Roles based on filters, sorting, and pagination
            $roles = $this->roleService->getAllRoles($filters, $sortBy, $sortOrder, $perPage, $userContext);

            $response = response()->json($roles);
            // Extract pagination details
            $paginationDetails = [
                'total' => $roles->total(),
                'per_page' => $roles->perPage(),
                'current_page' => $roles->currentPage(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem(),
            ];
            Log::info('Role index method response from RoleController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in RoleController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Role: U
     *
     * @param RoleUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        Log::debug("Role update method called in RoleController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Role with validated data
            $role = $this->roleService->updateRole($id, $validatedData, $userContext);
            if (!$role) {
                $error_response = response()->json(['message' => 'Role not found or update not possible'], 404);
                Log::error('Failed to update Role in RoleController@update:', $error_response->getData(true));
                return $error_response;
            }

            // Handle privileges if provided
            if (isset($validatedData['privileges'])) {
                $this->roleService->assignPrivilegesToRole($role, $validatedData['privileges']);
            }

            // Ensure the role is not null
            if ($role) {
                // Load the privileges relationship
                $role->load('privileges:id,name');
                $response = response()->json($role);
                Log::info('Role update method response from RoleController: ', $response->getData(true));
                return $response;
            } else {
                $error_response = response()->json(['message' => 'Failed to update role'], 500);
                Log::error('Role updation returned null in RoleController@update:', $error_response->getData(true));
                return $error_response;
            }
        } catch (\Exception $e) {
            Log::error('Failed to update Role in RoleController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Delete a Role permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Role with ID: $id in RoleController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->roleService->deleteRole($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Role deleted successfully'], 200);
            Log::info('Role destroy method response from RoleController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Role not found'], 404);
            Log::error('Failed to delete Role in RoleController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Roles.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in RoleController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->roleService->generateXlsxTemplate($userContext);
            Log::info('Role xlsxTemplate method response from RoleController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in RoleController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Roles from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Roles from xlsx in RoleController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Roles from the provided Excel file
            $result = $this->roleService->importFromXlsx($validatedData['file'], $userContext);

            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';

            // Set the response message and status
            $response = response()->json([
                'message' => $message,
                'data' => $result
            ], $status);

            Log::info('Role importXlsx method response from RoleController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in RoleController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }


    /**
     * Export Roles to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Roles to xlsx in RoleController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Roles to an Excel file
            $filePath = $this->roleService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Role exportXlsx method response from RoleController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in RoleController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
