<?php

namespace App\Feature\Office\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\Office\Models\CpKyc;
use App\Feature\Office\Repositories\CpKycRepository;
use App\Feature\Office\Requests\CpKycStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CpKycService
 *
 * Service class to handle business logic for the CpKyc entity.
 *
 * @package App\Feature\Office\Services
 */
class CpKycService
{
    /**
     * The repository instance for interacting with the CpKyc model.
     *
     * @var CpKycRepository
     */
    protected $cpKycRepository;

    /**
     * CpKycService constructor.
     *
     * @param CpKycRepository $cpKycRepository
     */
    public function __construct(CpKycRepository $cpKycRepository)
    {
        $this->cpKycRepository = $cpKycRepository;
    }

    /**
     * Create a new CpKyc with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CpKyc
     */
    public function createCpKyc(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CpKyc in CpKycService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->cpKycRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CpKyc by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CpKyc|null
     */
    public function getCpKycById(int $id, UserContext $userContext): ?CpKyc
    {
        Log::info('Fetching CpKyc by ID in CpKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->cpKycRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CpKycs based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCpKycs(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all cpkycs with filters in CpKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->cpKycRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CpKyc with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CpKyc|null
     */
    public function updateCpKyc(int $id, array $data, UserContext $userContext): ?CpKyc
    {
        Log::info('Updating CpKyc in CpKycService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $cpKyc = $this->cpKycRepository->find($id, $userContext);
        if ($cpKyc) {
            return $this->cpKycRepository->update($cpKyc, $data, $userContext);
        }
        return null;
    }

    //TODO: Remove below method if not required.
    /**
    * Upload an image or file for the CpKyc and update the URL in the database: U
    *
    * @param int $id
    * @param \Illuminate\Http\UploadedFile $file
    * @param string $urlFieldName
    * @param UserContext $userContext
    * @return string|null
    * @throws Exception
    */
    public function uploadImgOrFileSrvc(int $id, $file, string $urlFieldName, UserContext $userContext): ?string
    {
        Log::info('Uploading file for CpKyc in CpKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $cpKyc = $this->cpKycRepository->find($id, $userContext);

        $storage_dir = 'public/files/tms/tenant_' . ($userContext->tenantId ?? 0) . '/cpkycs/cpkyc'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$cpKyc) {
            throw new Exception('CpKyc not found');
        }

        // Determine the file extension
        $extension = strtolower($file->getClientOriginalExtension());

        // generate a unique file name but keep the same extension
        $fileName = $filename_prefix . '_orig_' . $id . '.' . $extension;

        // Store the file
        $path = $file->storeAs($storage_dir, $fileName);

        if (!$path) {
            throw new Exception('Failed to upload file');
        }

        // New file name
        $newFileName = $filename_prefix . '_' . $id . '.' . $extension;

        // Save the file and get the URL
        $fileUrl = ImgOrFileUploadHelper::saveImgOrFile($storage_dir, $fileName, $newFileName);

        // Update the URL in the database
        $cpKyc = $this->cpKycRepository->update($cpKyc, [$urlFieldName => $fileUrl], $userContext);

        return $cpKyc->$urlFieldName;
    }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a CpKyc by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CpKyc|null
     */
    public function deactivateCpKyc(int $id, UserContext $userContext): ?CpKyc
    {
        Log::info('Deactivating CpKyc in CpKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $cpKyc = $this->cpKycRepository->find($id, $userContext);
        if ($cpKyc) {
            return $this->cpKycRepository->update($cpKyc, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a CpKyc by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCpKyc(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CpKyc in CpKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $cpKyc = $this->cpKycRepository->find($id, $userContext);
        if ($cpKyc) {
            return $this->cpKycRepository->delete($cpKyc, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CpKyc import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CpKycs in CpKycService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the cpkycs table
        $columns = Schema::getColumnListing('cp_kyc');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'cpkyc_template.xlsx';
        $templatePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the template using Maatwebsite Excel
            Excel::store(new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray {
                protected $headers;

                public function __construct(array $headers)
                {
                    $this->headers = $headers;
                }

                public function array(): array
                {
                    return [$this->headers];
                }
            }, $temp_dir . '/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('XLSX template created successfully at ' . $templatePath);

            // Check if the file was created
            if (!File::exists($templatePath)) {
                throw new \Exception("Failed to create the xlsx template file at $templatePath");
            }

            return $templatePath;

        } catch (Exception $e) {
            Log::error('Error generating XLSX template: ' . $e->getMessage());
            throw $e;
        }
    }

/**
 * Import CpKycs from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CpKycs from xlsx in CpKycService', [
        'userContext' => [
            'userId' => $userContext->userId,
            'tenantId' => $userContext->tenantId,
            'loginId' => $userContext->loginId
        ],
        'file' => $file
    ]);

    $importResult = [
        'success' => true,
        'message' => 'Import completed successfully',
        'imported_count' => 0,
        'errors' => []
    ];

    try {
        // Check if the file exists and is readable
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception('The file does not exist or is not readable.');
        }

        $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray
        {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        Log::info('Excel data read successfully', ['data' => $data]);

        if (empty($data) || !isset($data[0])) {
            throw new Exception('The uploaded file is empty or invalid.');
        }

        $cpKycs = $data[0];
        $headers = array_shift($cpKycs); // Remove the first row (headers)

        foreach ($cpKycs as $index => $cpKycData) {
            try {
                // Combine the headers with the data
                $cpKycData = array_combine($headers, $cpKycData);

                // Convert all data to strings
                foreach ($cpKycData as $key => $value) {
                    $cpKycData[$key] = (string)$value;
                }

                // Extract tenant_id from userContext if not present in data
                if (!isset($cpKycData['tenant_id']) || $cpKycData['tenant_id'] === null) {
                    $cpKycData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the data using the appropriate StoreRequest
                $request = new CpKycStoreRequest();
                // Manually set the data and user context on the request
                $request->merge($cpKycData);
                $request->setUserResolver(function () use ($userContext) {
                    return $userContext;
                });

                // Get validation rules
                $rules = $request->rules();

                // Validate the data
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    // Collect validation errors
                    $errors = $validator->errors()->all();
                    Log::error('Validation failed for CpKyc at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CpKyc at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import cpKyc at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import cpKyc at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CpKycs import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CpKycs imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CpKycs: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}



    /**
     * Export CpKycs to an Excel file based on the given filters.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function exportToXlsx(array $filters, string $sortBy, string $sortOrder, UserContext $userContext): string
    {
        Log::info('Exporting CpKycs to xlsx in CpKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch cpkycs data for export
        $cpKycs = $this->cpKycRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert cpkycs to array
        $cpKycsArray = $cpKycs->toArray();

        // Retrieve the columns of the cpkycs table
        $columns = Schema::getColumnListing('cp_kyc');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'cpkycs_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $cpKycsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $cpKycs;

                public function __construct(array $headers, array $cpKycs)
                {
                    $this->headers = $headers;
                    $this->cpKycs = $cpKycs;
                }

                public function collection()
                {
                    return collect($this->cpKycs);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CpKycs exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CpKycs to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
