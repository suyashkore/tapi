<?php

namespace App\Feature\Tenant\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Tenant\Repositories\TenantRepository;
use App\Feature\Tenant\Requests\TenantStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Validator;


/**
 * Class TenantService
 *
 * Service class to handle business logic for the Tenant entity.
 *
 * @package App\Feature\Tenant\Services
 */
class TenantService
{
    /**
     * The repository instance for interacting with the Tenant model.
     *
     * @var TenantRepository
     */
    protected $tenantRepository;

    /**
     * TenantService constructor.
     *
     * @param TenantRepository $tenantRepository
     */
    public function __construct(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Create a new tenant with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Tenant
     */
    public function createTenant(array $data, UserContext $userContext)
    {
        Log::info('Creating a new tenant in TenantService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        return $this->tenantRepository->create($data, $userContext);
    }

    /**
     * Retrieve a tenant by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Tenant|null
     */
    public function getTenantById(int $id, UserContext $userContext): ?Tenant
    {
        Log::info('Fetching tenant by ID in TenantService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        return $this->tenantRepository->find($id, $userContext);
    }

    /**
     * Retrieve all tenants based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTenants(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all tenants with filters in TenantService', [ 'filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        return $this->tenantRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing tenant with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Tenant|null
     */
    public function updateTenant(int $id, array $data, UserContext $userContext): ?Tenant
    {
        Log::info('Updating tenant in TenantService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        $tenant = $this->tenantRepository->find($id, $userContext);
        if ($tenant) {
            return $this->tenantRepository->update($tenant, $data, $userContext);
        }
        return null;
    }

    /**
    * Upload an image or file for the Tenant and update the URL in the database: U
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
        Log::info('Uploading file for Tenant in TenantService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $tenant = $this->tenantRepository->find($id, $userContext);

        $storage_dir = 'public/files/tms/tenant_'. ($userContext->tenantId ?? 0) .'/tenants/tenant'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$tenant) {
            throw new Exception('Tenant not found');
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
        $tenant = $this->tenantRepository->update($tenant, [$urlFieldName => $fileUrl], $userContext);

        return $tenant->$urlFieldName;
    }

    /**
     * Deactivate a tenant by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Tenant|null
     */
    public function deactivateTenant(int $id, UserContext $userContext): ?Tenant
    {
        Log::info('Deactivating tenant in TenantService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        $tenant = $this->tenantRepository->find($id, $userContext);
        if ($tenant) {
            return $this->tenantRepository->update($tenant, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a tenant by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteTenant(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting tenant in TenantService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);
        $tenant = $this->tenantRepository->find($id, $userContext);
        if ($tenant) {
            return $this->tenantRepository->delete($tenant, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the tenant import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for tenants in TenantService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);

        // Retrieve the columns of the tenants table
        $columns = Schema::getColumnListing('tenants');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'tenant_template.xlsx';
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
 * Import tenants from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing tenants from xlsx in TenantService', [
        'userContext' => [
            'userId' => $userContext->userId,
            'tenantId' => $userContext->tenantId,
            'loginId' => $userContext->loginId,
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

        $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        Log::info('Excel data read successfully', ['data' => $data]);

        if (empty($data) || !isset($data[0])) {
            throw new Exception('The uploaded file is empty or invalid.');
        }

        $tenants = $data[0];
        $headers = array_shift($tenants); // Remove the first row (headers)

        foreach ($tenants as $index => $tenantData) {
            try {
                // Combine the headers with the tenant data
                $tenantData = array_combine($headers, $tenantData);

                // Exclude certain columns
                $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];
                foreach ($excludeColumns as $excludeColumn) {
                    unset($tenantData[$excludeColumn]);
                }

                // Extract tenant_id from userContext if not present in tenantData
                if (!isset($tenantData['tenant_id']) || $tenantData['tenant_id'] === null) {
                    $tenantData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the tenant data using TenantStoreRequest
                $request = new TenantStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($tenantData);
                $request->setUserResolver(function () use ($userContext) {
                    return $userContext;
                });

                // Get validation rules
                $rules = $request->rules();

                // Validate the tenant data
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    // Collect validation errors
                    $errors = $validator->errors()->all();
                    Log::error('Validation failed for tenant at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for tenant at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the tenant
                $tenant = $this->tenantRepository->create($tenantData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import tenant at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import tenant at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('Tenants import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('Tenants imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing tenants: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}


    /**
     * Export tenants to an Excel file based on the given filters.
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
        Log::info('Exporting tenants to xlsx in TenantService', [ 'filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId,]]);

        // Fetch tenants data for export
        $tenants = $this->tenantRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert tenants to array
        $tenantsArray = $tenants->toArray();

        // Retrieve the columns of the tenants table
        $columns = Schema::getColumnListing('tenants');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'tenants_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $tenantsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $tenants;

                public function __construct(array $headers, array $tenants)
                {
                    $this->headers = $headers;
                    $this->tenants = $tenants;
                }

                public function collection()
                {
                    return collect($this->tenants);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Tenants exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting tenants to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }

}
