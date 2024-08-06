<?php

namespace App\Feature\User\Controllers;

use App\Feature\User\Requests\AdminResetPasswordRequest;
use App\Feature\User\Requests\ChangePasswordRequest;
use App\Feature\User\Requests\UserStoreRequest;
use App\Feature\User\Requests\UserUpdateRequest;
use App\Feature\Shared\Requests\UploadImgOrFileRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\User\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

/**
 * Class UserController
 *
 * Controller class to handle HTTP requests related to User entity.
 *
 * @package App\Feature\User\Controllers
 */
class UserController extends Controller
{
    /**
     * The service instance for handling business logic for the User entity.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a new User: C
     *
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        Log::debug('User store method called in UserController');

        // Validate request data
        $validatedData = $request->validated();

        // Hash the password
        if (isset($validatedData['password'])) {
            $validatedData['password_hash'] = Hash::make($validatedData['password']);
            unset($validatedData['password']);
        }

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new User with validated data
            $user = $this->userService->createUser($validatedData, $userContext);
            $response = response()->json($user, 201);
            Log::info('User store method response from UserController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create User in UserController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single User by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("User show method called in UserController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch User by ID
        $user = $this->userService->getUserById($id, $userContext);

        if ($user) {
            // Load the role and privileges
            $user->load('role.privileges');

            // Make the role relationship hidden
            $user->makeHidden('role');

            // Prepare the response data
            $responseData = $user->toArray();

            // Insert role_name and privileges just after role_id
            $responseData = array_merge(
                array_slice($responseData, 0, array_search('role_id', array_keys($responseData)) + 1),
                [
                    'role_name' => $user->role ? $user->role->name : null,
                    'privileges' => $user->privileges()->toArray()
                ],
                array_slice($responseData, array_search('role_id', array_keys($responseData)) + 1)
            );

            $response = response()->json($responseData);
            Log::info('User show method response from UserController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'User not found'], 404);
            Log::error('Failed to retrieve User in UserController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Users with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("User index method called in UserController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Users based on filters, sorting, and pagination
            $users = $this->userService->getAllUsers($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($users);
            // Extract pagination details
            $paginationDetails = [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ];
            Log::info('User index method response from UserController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in UserController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing User: U
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        Log::debug("User update method called in UserController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Hash the password
        if (isset($validatedData['password'])) {
            $validatedData['password_hash'] = Hash::make($validatedData['password']);
            unset($validatedData['password']);
        }

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update User with validated data
            $user = $this->userService->updateUser($id, $validatedData, $userContext);
            if (!$user) {
                $error_response = response()->json(['message' => 'User not found or update not possible'], 404);
                Log::error('Failed to update User in UserController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($user);
            Log::info('User update method response from UserController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update User in UserController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
    * Upload an image or file for a User: U
    *
    * @param UploadImgOrFileRequest $request
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function uploadImgOrFile(UploadImgOrFileRequest $request, $id)
    {
        Log::debug("Uploading a file for User with ID: $id in UserController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload file and get the URL
            $fileUrl = $this->userService->uploadImgOrFileSrvc($id, $validatedData['file'], $validatedData['urlfield_name'], $userContext);
            $response = response()->json([$validatedData['urlfield_name'] => $fileUrl], 200);
            Log::info('User uploadImgOrFile method response from UserController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload file in UserController@uploadImgOrFile: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a User (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating User with ID: $id in UserController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate User by ID
        $user = $this->userService->deactivateUser($id, $userContext);

        if ($user) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'User deactivated successfully'], 200);
            Log::info('User deactivate method response from UserController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'User not found or already deactivated'], 404);
            Log::error('Failed to deactivate User in UserController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a User permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete User with ID: $id in UserController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->userService->deleteUser($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'User deleted successfully'], 200);
            Log::info('User destroy method response from UserController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'User not found'], 404);
            Log::error('Failed to delete User in UserController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Users.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in UserController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->userService->generateXlsxTemplate($userContext);
            Log::info('User xlsxTemplate method response from UserController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in UserController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Users from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Users from xlsx in UserController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Users from the provided Excel file
            $result = $this->userService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('User importXlsx method response from UserController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in UserController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Users to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Users to xlsx in UserController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Users to an Excel file
            $filePath = $this->userService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('User exportXlsx method response from UserController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in UserController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

   /**
     * Admin reset password for a user.
     *
     * @param AdminResetPasswordRequest $request
     * @return JsonResponse
     */
    public function adminResetPassword(AdminResetPasswordRequest $request): JsonResponse
    {
        Log::info('Admin requested password reset in UserController for user login ID: ' . $request->login_id . ' tenant id: ' . $request->tenant_id);

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        $data = $request->validated();
        $this->userService->resetUserPassword($data['tenant_id'], $data['login_id'], $data['new_password'], $userContext);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    /**
     * Change password for a logged-in user.
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changeSelfPassword(ChangePasswordRequest $request): JsonResponse
    {
        Log::info('User requested password change for user ID: ' . $request->user()->id);

        try {
            // Extract user context from request
            $userContext = $request->attributes->get('userContext');

            $data = $request->validated();
            $this->userService->changeUserPassword($data['old_password'], $data['new_password'], $userContext);

            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

}
