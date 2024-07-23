<?php

namespace App\Feature\Tenant\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\Tenant\Models\TenantKyc;
use App\Feature\Tenant\Repositories\TenantKycRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class TenantKycService
 *
 * Service class to handle business logic for the TenantKyc entity.
 *
 * @package App\Feature\Tenant\Services
 */
class TenantKycService
{
    /**
     * The repository instance for interacting with the TenantKyc model.
     *
     * @var TenantKycRepository
     */
    protected $tenantKycRepository;

    /**
     * TenantKycService constructor.
     *
     * @param TenantKycRepository $tenantKycRepository
     */
    public function __construct(TenantKycRepository $tenantKycRepository)
    {
        $this->tenantKycRepository = $tenantKycRepository;
    }

    /**
     * Create a new TenantKyc with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return TenantKyc
     */
    public function createTenantKyc(array $data, UserContext $userContext)
    {
        Log::info('Creating a new TenantKyc in TenantKycService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->tenantKycRepository->create($data, $userContext);
    }

    /**
     * Retrieve a TenantKyc by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return TenantKyc|null
     */
    public function getTenantKycById(int $id, UserContext $userContext): ?TenantKyc
    {
        Log::info('Fetching TenantKyc by ID in TenantKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->tenantKycRepository->find($id, $userContext);
    }

    /**
     * Retrieve all TenantKycs based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTenantKycs(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all tenantkycs with filters in TenantKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->tenantKycRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing TenantKyc with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return TenantKyc|null
     */
    public function updateTenantKyc(int $id, array $data, UserContext $userContext): ?TenantKyc
    {
        Log::info('Updating TenantKyc in TenantKycService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $tenantKyc = $this->tenantKycRepository->find($id, $userContext);
        if ($tenantKyc) {
            return $this->tenantKycRepository->update($tenantKyc, $data, $userContext);
        }
        return null;
    }

    /**
    * Upload an image or file for the TenantKyc and update the URL in the database: U
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
        Log::info('Uploading file for TenantKyc in TenantKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $tenantKyc = $this->tenantKycRepository->find($id, $userContext);

        $storage_dir = 'public/files/tms/tenant_'. ($userContext->tenantId ?? 0) .'/tenantkycs/tenantkyc'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$tenantKyc) {
            throw new Exception('TenantKyc not found');
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
        $tenantKyc = $this->tenantKycRepository->update($tenantKyc, [$urlFieldName => $fileUrl], $userContext);

        return $tenantKyc->$urlFieldName;
    }

    /**
     * Deactivate a TenantKyc by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return TenantKyc|null
     */
    public function deactivateTenantKyc(int $id, UserContext $userContext): ?TenantKyc
    {
        Log::info('Deactivating TenantKyc in TenantKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $tenantKyc = $this->tenantKycRepository->find($id, $userContext);
        if ($tenantKyc) {
            return $this->tenantKycRepository->update($tenantKyc, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a TenantKyc by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteTenantKyc(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting TenantKyc in TenantKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $tenantKyc = $this->tenantKycRepository->find($id, $userContext);
        if ($tenantKyc) {
            return $this->tenantKycRepository->delete($tenantKyc, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the TenantKyc import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for TenantKycs in TenantKycService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the tenantkycs table
        $columns = Schema::getColumnListing('tenant_kyc');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'tenantkyc_template.xlsx';
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
     * Import TenantKycs from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing TenantKycs from xlsx in TenantKycService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        $importResult = [
            'success' => true,
            'message' => 'Import completed successfully',
            'imported_count' => 0,
            'errors' => []
        ];

        try {
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $file);

            if (empty($data) || !isset($data[0])) {
                throw new Exception('The uploaded file is empty or invalid.');
            }

            $tenantKycs = $data[0];
            $headers = array_shift($tenantKycs); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($tenantKycs as $index => $tenantKycData) {
                try {
                    // Skip rows that don't have the required columns
                    $tenantKycData = array_combine($headers, $tenantKycData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($tenantKycData[$excludeColumn]);
                    }

                    $this->tenantKycRepository->create($tenantKycData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import tenantKyc at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import tenantKyc at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('TenantKycs import completed with errors');
            }else{
                Log::debug('TenantKycs imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing TenantKycs: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export TenantKycs to an Excel file based on the given filters.
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
        Log::info('Exporting TenantKycs to xlsx in TenantKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch tenantkycs data for export
        $tenantKycs = $this->tenantKycRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert tenantkycs to array
        $tenantKycsArray = $tenantKycs->toArray();

        // Retrieve the columns of the tenantkycs table
        $columns = Schema::getColumnListing('tenant_kyc');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'tenantkycs_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $tenantKycsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $tenantKycs;

                public function __construct(array $headers, array $tenantKycs)
                {
                    $this->headers = $headers;
                    $this->tenantKycs = $tenantKycs;
                }

                public function collection()
                {
                    return collect($this->tenantKycs);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('TenantKycs exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting TenantKycs to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
