<?php

namespace App\Feature\Company\Controllers;

use App\Feature\Company\Requests\CompanyStoreRequest;
use App\Feature\Company\Requests\CompanyUpdateRequest;
use App\Feature\Shared\Requests\UploadImgOrFileRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Company\Services\CompanyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CompanyController
 *
 * Controller class to handle HTTP requests related to Company entity.
 *
 * @package App\Feature\Company\Controllers
 */
class CompanyController extends Controller
{
    /**
     * The service instance for handling business logic for the Company entity.
     *
     * @var CompanyService
     */
    protected $companyService;

    /**
     * CompanyController constructor.
     *
     * @param CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Create a new Company: C
     *
     * @param CompanyStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CompanyStoreRequest $request)
    {
        Log::debug('Company store method called in CompanyController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new Company with validated data
            $company = $this->companyService->createCompany($validatedData, $userContext);
            $response = response()->json($company, 201);
            Log::info('Company store method response from CompanyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Company in CompanyController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single Company by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("Company show method called in CompanyController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch Company by ID
        $company = $this->companyService->getCompanyById($id, $userContext);

        if ($company) {
            $response = response()->json($company);
            Log::info('Company show method response from CompanyController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Company not found'], 404);
            Log::error('Failed to retrieve Company in CompanyController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of Companies with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("Company index method called in CompanyController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch Companies based on filters, sorting, and pagination
            $companies = $this->companyService->getAllCompanies($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($companies);
            // Extract pagination details
            $paginationDetails = [
                'total' => $companies->total(),
                'per_page' => $companies->perPage(),
                'current_page' => $companies->currentPage(),
                'from' => $companies->firstItem(),
                'to' => $companies->lastItem(),
            ];
            Log::info('Company index method response from CompanyController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in CompanyController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing Company: U
     *
     * @param CompanyUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompanyUpdateRequest $request, $id)
    {
        Log::debug("Company update method called in CompanyController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update Company with validated data
            $company = $this->companyService->updateCompany($id, $validatedData, $userContext);
            if (!$company) {
                $error_response = response()->json(['message' => 'Company not found or update not possible'], 404);
                Log::error('Failed to update Company in CompanyController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($company);
            Log::info('Company update method response from CompanyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update Company in CompanyController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
    * Upload an image or file for a Company: U
    *
    * @param UploadImgOrFileRequest $request
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function uploadImgOrFile(UploadImgOrFileRequest $request, $id)
    {
        Log::debug("Uploading a file for Company with ID: $id in CompanyController");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Upload file and get the URL
            $fileUrl = $this->companyService->uploadImgOrFileSrvc($id, $validatedData['file'], $validatedData['urlfield_name'], $userContext);
            $response = response()->json([$validatedData['urlfield_name'] => $fileUrl], 200);
            Log::info('Company uploadImgOrFile method response from CompanyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upload file in CompanyController@uploadImgOrFile: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    /**
     * Deactivate a Company (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating Company with ID: $id in CompanyController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate Company by ID
        $company = $this->companyService->deactivateCompany($id, $userContext);

        if ($company) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'Company deactivated successfully'], 200);
            Log::info('Company deactivate method response from CompanyController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Company not found or already deactivated'], 404);
            Log::error('Failed to deactivate Company in CompanyController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a Company permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete Company with ID: $id in CompanyController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->companyService->deleteCompany($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'Company deleted successfully'], 200);
            Log::info('Company destroy method response from CompanyController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'Company not found'], 404);
            Log::error('Failed to delete Company in CompanyController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing Companies.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in CompanyController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->companyService->generateXlsxTemplate($userContext);
            Log::info('Company xlsxTemplate method response from CompanyController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in CompanyController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import Companies from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing Companies from xlsx in CompanyController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import Companies from the provided Excel file
            $result = $this->companyService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('Company importXlsx method response from CompanyController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in CompanyController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export Companies to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting Companies to xlsx in CompanyController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export Companies to an Excel file
            $filePath = $this->companyService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('Company exportXlsx method response from CompanyController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in CompanyController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
