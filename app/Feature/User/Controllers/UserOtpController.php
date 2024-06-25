<?php

namespace App\Feature\User\Controllers;

use App\Feature\User\Requests\UserOtpStoreRequest;
use App\Feature\User\Requests\UserOtpUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\User\Services\UserOtpService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class UserOtpController
 *
 * Controller class to handle HTTP requests related to UserOtp entity.
 *
 * @package App\Feature\User\Controllers
 */
class UserOtpController extends Controller
{
    /**
     * The service instance for handling business logic for the UserOtp entity.
     *
     * @var UserOtpService
     */
    protected $userOtpService;

    /**
     * UserOtpController constructor.
     *
     * @param UserOtpService $userOtpService
     */
    public function __construct(UserOtpService $userOtpService)
    {
        $this->userOtpService = $userOtpService;
    }

    /**
     * Create a new UserOtp: C
     *
     * @param UserOtpStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserOtpStoreRequest $request)
    {
        Log::debug('UserOtp store method called in UserOtpController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new UserOtp with validated data
            $userOtp = $this->userOtpService->createUserOtp($validatedData, $userContext);
            $response = response()->json($userOtp, 201);
            Log::info('UserOtp store method response from UserOtpController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create UserOtp in UserOtpController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single UserOtp by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("UserOtp show method called in UserOtpController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch UserOtp by ID
        $userOtp = $this->userOtpService->getUserOtpById($id, $userContext);

        if ($userOtp) {
            $response = response()->json($userOtp);
            Log::info('UserOtp show method response from UserOtpController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'UserOtp not found'], 404);
            Log::error('Failed to retrieve UserOtp in UserOtpController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of UserOtps with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("UserOtp index method called in UserOtpController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch UserOtps based on filters, sorting, and pagination
            $userOtps = $this->userOtpService->getAllUserOtps($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($userOtps);
            // Extract pagination details
            $paginationDetails = [
                'total' => $userOtps->total(),
                'per_page' => $userOtps->perPage(),
                'current_page' => $userOtps->currentPage(),
                'from' => $userOtps->firstItem(),
                'to' => $userOtps->lastItem(),
            ];
            Log::info('UserOtp index method response from UserOtpController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in UserOtpController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing UserOtp: U
     *
     * @param UserOtpUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserOtpUpdateRequest $request, $id)
    {
        Log::debug("UserOtp update method called in UserOtpController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update UserOtp with validated data
            $userOtp = $this->userOtpService->updateUserOtp($id, $validatedData, $userContext);
            if (!$userOtp) {
                $error_response = response()->json(['message' => 'UserOtp not found or update not possible'], 404);
                Log::error('Failed to update UserOtp in UserOtpController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($userOtp);
            Log::info('UserOtp update method response from UserOtpController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update UserOtp in UserOtpController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a UserOtp permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete UserOtp with ID: $id in UserOtpController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->userOtpService->deleteUserOtp($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'UserOtp deleted successfully'], 200);
            Log::info('UserOtp destroy method response from UserOtpController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'UserOtp not found'], 404);
            Log::error('Failed to delete UserOtp in UserOtpController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }
}
