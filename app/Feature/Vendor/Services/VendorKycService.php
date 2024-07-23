<?php

namespace App\Feature\Vendor\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\Vendor\Models\VendorKyc;
use App\Feature\Vendor\Repositories\VendorKycRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class VendorKycService
 *
 * Service class to handle business logic for the VendorKyc entity.
 *
 * @package App\Feature\VendorKyc\Services
 */
class VendorKycService
{
    /**
     * The repository instance for interacting with the VendorKyc model.
     *
     * @var VendorKycRepository
     */
    protected $vendorKycRepository;

    /**
     * VendorKycService constructor.
     *
     * @param VendorKycRepository $vendorKycRepository
     */
    public function __construct(VendorKycRepository $vendorKycRepository)
    {
        $this->vendorKycRepository = $vendorKycRepository;
    }

    /**
     * Create a new VendorKyc with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return VendorKyc
     */
    public function createVendorKyc(array $data, UserContext $userContext)
    {
        Log::info('Creating a new VendorKyc in VendorKycService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorKycRepository->create($data, $userContext);
    }

    /**
     * Retrieve a VendorKyc by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return VendorKyc|null
     */
    public function getVendorKycById(int $id, UserContext $userContext): ?VendorKyc
    {
        Log::info('Fetching VendorKyc by ID in VendorKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorKycRepository->find($id, $userContext);
    }

    /**
     * Retrieve all VendorKycs based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllVendorKycs(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all vendorkycs with filters in VendorKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorKycRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing VendorKyc with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return VendorKyc|null
     */
    public function updateVendorKyc(int $id, array $data, UserContext $userContext): ?VendorKyc
    {
        Log::info('Updating VendorKyc in VendorKycService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendorKyc = $this->vendorKycRepository->find($id, $userContext);
        if ($vendorKyc) {
            return $this->vendorKycRepository->update($vendorKyc, $data, $userContext);
        }
        return null;
    }

    /**
    * Upload an image or file for the VendorKyc and update the URL in the database: U
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
        Log::info('Uploading file for VendorKyc in VendorKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendorKyc = $this->vendorKycRepository->find($id, $userContext);

        $storage_dir = 'public/files/tms/tenant_' . ($userContext->tenantId ?? 0) . '/vendorkycs/vendorkyc'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$vendorKyc) {
            throw new Exception('VendorKyc not found');
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
        $vendorKyc = $this->vendorKycRepository->update($vendorKyc, [$urlFieldName => $fileUrl], $userContext);

        return $vendorKyc->$urlFieldName;
    }

    /**
     * Deactivate a VendorKyc by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return VendorKyc|null
     */
    public function deactivateVendorKyc(int $id, UserContext $userContext): ?VendorKyc
    {
        Log::info('Deactivating VendorKyc in VendorKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendorKyc = $this->vendorKycRepository->find($id, $userContext);
        if ($vendorKyc) {
            return $this->vendorKycRepository->update($vendorKyc, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a VendorKyc by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteVendorKyc(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting VendorKyc in VendorKycService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendorKyc = $this->vendorKycRepository->find($id, $userContext);
        if ($vendorKyc) {
            return $this->vendorKycRepository->delete($vendorKyc, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the VendorKyc import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for VendorKycs in VendorKycService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the vendorkycs table
        $columns = Schema::getColumnListing('vendor_kyc');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'vendorkyc_template.xlsx';
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
     * Import VendorKycs from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing VendorKycs from xlsx in VendorKycService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $vendorKycs = $data[0];
            $headers = array_shift($vendorKycs); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($vendorKycs as $index => $vendorKycData) {
                try {
                    // Skip rows that don't have the required columns
                    $vendorKycData = array_combine($headers, $vendorKycData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($vendorKycData[$excludeColumn]);
                    }

                    $this->vendorKycRepository->create($vendorKycData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import vendorKyc at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import vendorKyc at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('VendorKycs import completed with errors');
            }else{
                Log::debug('VendorKycs imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing VendorKycs: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export VendorKycs to an Excel file based on the given filters.
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
        Log::info('Exporting VendorKycs to xlsx in VendorKycService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch vendorkycs data for export
        $vendorKycs = $this->vendorKycRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert vendorkycs to array
        $vendorKycsArray = $vendorKycs->toArray();

        // Retrieve the columns of the vendorkycs table
        $columns = Schema::getColumnListing('vendor_kyc');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'vendorkycs_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $vendorKycsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $vendorKycs;

                public function __construct(array $headers, array $vendorKycs)
                {
                    $this->headers = $headers;
                    $this->vendorKycs = $vendorKycs;
                }

                public function collection()
                {
                    return collect($this->vendorKycs);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('VendorKycs exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting VendorKycs to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
