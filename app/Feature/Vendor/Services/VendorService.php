<?php

namespace App\Feature\Vendor\Services;

use App\Feature\Vendor\Models\Vendor;
use App\Feature\Vendor\Repositories\VendorRepository;
use App\Feature\Shared\Services\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class VendorService
 *
 * Service class to handle business logic for the Vendor entity.
 *
 * @package App\Feature\Vendor\Services
 */
class VendorService
{
    /**
     * The repository instance for interacting with the Vendor model.
     *
     * @var VendorRepository
     */
    protected $vendorRepository;

    /**
     * VendorService constructor.
     *
     * @param VendorRepository $vendorRepository
     */
    public function __construct(VendorRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Create a new Vendor with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Vendor
     */
    public function createVendor(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Vendor in VendorService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Vendor by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Vendor|null
     */
    public function getVendorById(int $id, UserContext $userContext): ?Vendor
    {
        Log::info('Fetching Vendor by ID in VendorService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Vendors based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllVendors(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all vendors with filters in VendorService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vendorRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Vendor with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Vendor|null
     */
    public function updateVendor(int $id, array $data, UserContext $userContext): ?Vendor
    {
        Log::info('Updating Vendor in VendorService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendor = $this->vendorRepository->find($id, $userContext);
        if ($vendor) {
            return $this->vendorRepository->update($vendor, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a Vendor by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Vendor|null
     */
    public function deactivateVendor(int $id, UserContext $userContext): ?Vendor
    {
        Log::info('Deactivating Vendor in VendorService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendor = $this->vendorRepository->find($id, $userContext);
        if ($vendor) {
            return $this->vendorRepository->update($vendor, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Vendor by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteVendor(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Vendor in VendorService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vendor = $this->vendorRepository->find($id, $userContext);
        if ($vendor) {
            return $this->vendorRepository->delete($vendor, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Vendor import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Vendors in VendorService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the vendors table
        $columns = Schema::getColumnListing('vendors');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'vendor_template.xlsx';
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
     * Import Vendors from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Vendors from xlsx in VendorService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $vendors = $data[0];
            $headers = array_shift($vendors); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($vendors as $index => $vendorData) {
                try {
                    // Skip rows that don't have the required columns
                    $vendorData = array_combine($headers, $vendorData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($vendorData[$excludeColumn]);
                    }

                    $this->vendorRepository->create($vendorData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import vendor at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import vendor at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Vendors import completed with errors');
            }else{
                Log::debug('Vendors imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Vendors: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Vendors to an Excel file based on the given filters.
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
        Log::info('Exporting Vendors to xlsx in VendorService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch vendors data for export
        $vendors = $this->vendorRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert vendors to array
        $vendorsArray = $vendors->toArray();

        // Retrieve the columns of the vendors table
        $columns = Schema::getColumnListing('vendors');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'vendors_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $vendorsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $vendors;

                public function __construct(array $headers, array $vendors)
                {
                    $this->headers = $headers;
                    $this->vendors = $vendors;
                }

                public function collection()
                {
                    return collect($this->vendors);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Vendors exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Vendors to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
