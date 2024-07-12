<?php

namespace App\Feature\Customer\Controllers;

use App\Feature\Customer\Requests\CustomerStoreRequest;
use App\Feature\Customer\Requests\CustomerUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Customer\Services\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CustomerController
 *
 * Controller class to handle HTTP requests related to Customer entity.
 *
 * @package App\Feature\Customer\Controllers
 */
class CustomerController extends Controller
{
    /**
     * The service instance for handling business logic for the Customer entity.
     *
     * @var CustomerService
     */
    protected $customerService;

    /**
     * CustomerController constructor.
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Create a new Customer: C
     *
     * @param CustomerStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustomerStoreRequest $request)
    {
        Log::debug('Customer store method called in CustomerController');
        Log::debug('Request data: ', $request->all());
        // Validate request data
        $validatedData = $request->validated();
        // dd($validatedData);
        Log::debug('Validated data: ', $validatedData);
        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Customer with validated data
            $customer = $this->customerService->createCustomer($validatedData, $userContext);
            $response = response()->json($customer, 201);
            Log::info('Customer store method response from CustomerController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Customer in CustomerController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Customer by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Customer show method called in CustomerController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Customer by ID
        $customer = $this->customerService->getCustomerById($id, $userContext);

        if ($customer) {
            $response = response()->json($customer);
            Log::info('Customer show method response from CustomerController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Customer not found'], 404);
            Log::error('Failed to retrieve Customer in CustomerController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Customers with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Customer index method called in CustomerController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model Customer
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Customers based on filters, sorting, and pagination
            $customers = $this->customerService->getAllCustomers($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($customers);
            // Extract pagination details
            $paginationDetails = [
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem(),
            ];
            Log::info('Customer index method response from CustomerController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CustomerController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Customer: U
     *
     * @param CustomerUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomerUpdateRequest $request, $id)
    {
        Log::debug("Customer update method called in CustomerController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Customer with validated data
            $customer = $this->customerService->updateCustomer($id, $validatedData, $userContext);
            if (!$customer) {
                $error_response = response()->json(['message' => 'Customer not found or update not possible'], 404);
                Log::error('Failed to update Customer in CustomerController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($customer);
            Log::info('Customer update method response from CustomerController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Customer in CustomerController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    //TODO: Remove below method if not required.
    /**
     * Upload an image for a Customer: U
     *
     * @param UploadImageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function uploadImage(UploadImageRequest $request, $id)
    // {
    //     Log::debug("Uploading an image for Customer with ID: $id in CustomerController");

    //     // Validate request data
    //     $validatedData = $request->validated();

    //     // Extract user context from request
    //     $userContext = $request->attributes->get('userContext');

    //     try {
    //         // Upload image and get the URL
    //         $imageUrl = $this->customerService->uploadImage($id, $validatedData['img'], $userContext);
    //         //TODO: Replace 'image_url' with the real field name
    //         $response = response()->json(['image_url' => $imageUrl], 200);
    //         Log::info('Customer uploadImage method response from CustomerController: ', $response->getData(true));
    //         return $response;
    //     } catch (\Exception $e) {
    //         Log::error('Failed to upload image in CustomerController@uploadImage: ' . $e->getMessage());
    //         return response()->json(['message' => 'Upload failed'], 500);
    //     }
    // }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a Customer (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Customer with ID: $id in CustomerController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Customer by ID
        $customer = $this->customerService->deactivateCustomer($id, $userContext);

        if ($customer) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Customer deactivated successfully'], 200);
            Log::info('Customer deactivate method response from CustomerController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Customer not found or already deactivated'], 404);
            Log::error('Failed to deactivate Customer in CustomerController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Customer permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Customer with ID: $id in CustomerController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->customerService->deleteCustomer($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Customer deleted successfully'], 200);
            Log::info('Customer destroy method response from CustomerController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Customer not found'], 404);
            Log::error('Failed to delete Customer in CustomerController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Customers.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CustomerController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->customerService->generateXlsxTemplate($userContext);
            Log::info('Customer xlsxTemplate method response from CustomerController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CustomerController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Customers from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Customers from xlsx in CustomerController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Customers from the provided Excel file
            $result = $this->customerService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Customer importXlsx method response from CustomerController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CustomerController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Customers to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Customers to xlsx in CustomerController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        //TODO: Check if 'active' is a field in model Customer
        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Customers to an Excel file
            $filePath = $this->customerService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Customer exportXlsx method response from CustomerController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CustomerController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
