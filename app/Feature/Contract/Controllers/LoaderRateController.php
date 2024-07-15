<?php

namespace App\Feature\Contract\Controllers;

use App\Feature\Contract\Requests\LoaderRateStoreRequest;
use App\Feature\Contract\Requests\LoaderRateUpdateRequest;
use App\Feature\Shared\Requests\UploadImageRequest;
use App\Feature\Shared\Requests\ImportXlsxRequest;
use App\Feature\Contract\Services\LoaderRateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Feature\Contract\Models\LoaderRate;

/**
 * Class LoaderRateController
 *
 * Controller class to handle HTTP requests related to LoaderRate entity.
 *
 * @package App\Feature\Contract\Controllers
 */
class LoaderRateController extends Controller
{
    /**
     * The service instance for handling business logic for the LoaderRate entity.
     *
     * @var LoaderRateService
     */
    protected $loaderRateService;

    /**
     * LoaderRateController constructor.
     *
     * @param LoaderRateService $loaderRateService
     */
    public function __construct(LoaderRateService $loaderRateService)
    {
        $this->loaderRateService = $loaderRateService;
    }

    /**
     * Create a new LoaderRate: C
     *
     * @param LoaderRateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoaderRateStoreRequest $request)
    {
        Log::debug('LoaderRate store method called in LoaderRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Create a new LoaderRate with validated data
            $loaderRate = $this->loaderRateService->createLoaderRate($validatedData, $userContext);
            $response = response()->json($loaderRate, 201);
            Log::info('LoaderRate store method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create LoaderRate in LoaderRateController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Creation failed'], 500);
        }
    }

    /**
     * Retrieve a single LoaderRate by its ID: R
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        Log::debug("LoaderRate show method called in LoaderRateController for ID: $id");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Fetch LoaderRate by ID
        $loaderRate = $this->loaderRateService->getLoaderRateById($id, $userContext);

        if ($loaderRate) {
            $response = response()->json($loaderRate);
            Log::info('LoaderRate show method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'LoaderRate not found'], 404);
            Log::error('Failed to retrieve LoaderRate in LoaderRateController@show: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Retrieve a list of LoaderRates with optional filters, sorting, and pagination : R
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::debug("LoaderRate index method called in LoaderRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');


        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        try {
            // Fetch LoaderRates based on filters, sorting, and pagination
            $loaderRates = $this->loaderRateService->getAllLoaderRates($filters, $sortBy, $sortOrder, $perPage, $userContext);
            $response = response()->json($loaderRates);
            // Extract pagination details
            $paginationDetails = [
                'total' => $loaderRates->total(),
                'per_page' => $loaderRates->perPage(),
                'current_page' => $loaderRates->currentPage(),
                'from' => $loaderRates->firstItem(),
                'to' => $loaderRates->lastItem(),
            ];
            Log::info('LoaderRate index method response from LoaderRateController: ', $paginationDetails);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in LoaderRateController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching data'], 500);
        }
    }

    /**
     * Update an existing LoaderRate: U
     *
     * @param LoaderRateUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LoaderRateUpdateRequest $request, $id)
    {
        Log::debug("LoaderRate update method called in LoaderRateController for ID: $id");

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Update LoaderRate with validated data
            $loaderRate = $this->loaderRateService->updateLoaderRate($id, $validatedData, $userContext);
            if (!$loaderRate) {
                $error_response = response()->json(['message' => 'LoaderRate not found or update not possible'], 404);
                Log::error('Failed to update LoaderRate in LoaderRateController@update:', $error_response->getData(true));
                return $error_response;
            }
            $response = response()->json($loaderRate);
            Log::info('LoaderRate update method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to update LoaderRate in LoaderRateController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Deactivate a LoaderRate (soft delete): U
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id, Request $request)
    {
        Log::debug("Deactivating LoaderRate with ID: $id in LoaderRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Deactivate LoaderRate by ID
        $loaderRate = $this->loaderRateService->deactivateLoaderRate($id, $userContext);

        if ($loaderRate) {
            $response = response()->json(['id' => $id, 'active' => false, 'message' => 'LoaderRate deactivated successfully'], 200);
            Log::info('LoaderRate deactivate method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'LoaderRate not found or already deactivated'], 404);
            Log::error('Failed to deactivate LoaderRate in LoaderRateController@deactivate: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Delete a LoaderRate permanently: D
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        Log::debug("Attempting to delete LoaderRate with ID: $id in LoaderRateController");

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        if ($this->loaderRateService->deleteLoaderRate($id, $userContext)) {
            $response = response()->json(['id' => $id, 'deleted' => true, 'message' => 'LoaderRate deleted successfully'], 200);
            Log::info('LoaderRate destroy method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } else {
            $error_response = response()->json(['message' => 'LoaderRate not found'], 404);
            Log::error('Failed to delete LoaderRate in LoaderRateController@destroy: ', $error_response->getData(true));
            return $error_response;
        }
    }

    /**
     * Generate and download an Excel template for importing LoaderRates.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function xlsxTemplate(Request $request)
    {
        Log::debug('Downloading xlsx template in LoaderRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Generate and get the path of the template
            $filePath = $this->loaderRateService->generateXlsxTemplate($userContext);
            Log::info('LoaderRate xlsxTemplate method response from LoaderRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to download xlsx template in LoaderRateController@xlsxFormat: ' . $e->getMessage());
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    /**
     * Import LoaderRates from an Excel file.
     *
     * @param ImportXlsxRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importXlsx(ImportXlsxRequest $request)
    {
        Log::debug('Importing LoaderRates from xlsx in LoaderRateController');

        // Validate request data
        $validatedData = $request->validated();

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        try {
            // Import LoaderRates from the provided Excel file
            $result = $this->loaderRateService->importFromXlsx($validatedData['file'], $userContext);
            // Determine the response status based on the success flag
            $status = $result['success'] ? 200 : 400;
            $message = $result['success'] ? 'Import successful' : 'Import failed with errors';
            $response = response()->json(['message' => $message, 'data' => $result], $status);
            Log::info('LoaderRate importXlsx method response from LoaderRateController: ', $response->getData(true));
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to import xlsx in LoaderRateController@importXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed'], 500);
        }
    }

    /**
     * Export LoaderRates to an Excel file based on filters.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportXlsx(Request $request)
    {
        Log::debug('Exporting LoaderRates to xlsx in LoaderRateController');

        // Extract user context from request
        $userContext = $request->attributes->get('userContext');

        // Extract filters, sorting, and pagination parameters from request
        $filters = $request->only(['active', 'created_from', 'created_to', 'updated_from', 'updated_to']);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        try {
            // Export LoaderRates to an Excel file
            $filePath = $this->loaderRateService->exportToXlsx($filters, $sortBy, $sortOrder, $userContext);
            Log::info('LoaderRate exportXlsx method response from LoaderRateController: ', ['file_path' => $filePath]);
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export xlsx in LoaderRateController@exportXlsx: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

}
