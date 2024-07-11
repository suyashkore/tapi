<?php

namespace App\Feature\Company\Services;

use App\Feature\Shared\Helpers\ImageHelper;
use App\Feature\Company\Models\Company;
use App\Feature\Company\Repositories\CompanyRepository;
use App\Feature\Shared\Services\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CompanyService
 *
 * Service class to handle business logic for the Company entity.
 *
 * @package App\Feature\Company\Services
 */
class CompanyService
{
    /**
     * The repository instance for interacting with the Company model.
     *
     * @var CompanyRepository
     */
    protected $companyRepository;

    /**
     * CompanyService constructor.
     *
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Create a new Company with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Company
     */
    public function createCompany(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Company in CompanyService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->companyRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Company by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Company|null
     */
    public function getCompanyById(int $id, UserContext $userContext): ?Company
    {
        Log::info('Fetching Company by ID in CompanyService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->companyRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Companies based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCompanies(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all companies with filters in CompanyService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->companyRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Company with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Company|null
     */
    public function updateCompany(int $id, array $data, UserContext $userContext): ?Company
    {
        Log::info('Updating Company in CompanyService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $company = $this->companyRepository->find($id, $userContext);
        if ($company) {
            return $this->companyRepository->update($company, $data, $userContext);
        }
        return null;
    }

    //TODO: Remove below method if not required.
    /**
     * Upload an image for the Company and update the image URL in the database: U
     *
     * @param int $id
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return string|null
     * @throws Exception
     */
    public function uploadLogo(int $id, $file, $userContext): ?string
    {
        Log::info('Uploading image for Company in CompanyService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        
        $company = $this->companyRepository->find($id, $userContext);

        // Check the directory path and edit the folder name 'img' to something suitable
        $storage_dir = 'public/images/company/img';
        $filename_prefix = 'company';

        if (!$company) {
            throw new Exception('Company not found');
        }

        // Generate a unique file name but keep the same extension
        $fileName = $filename_prefix . '_orig_' . $id . '.' . $file->getClientOriginalExtension();

        // Store the file
        $path = $file->storeAs($storage_dir, $fileName);

        if (!$path) {
            throw new Exception('Failed to upload image');
        }

        // Generate a unique file name but keep the same extension
        $newFileName = $filename_prefix . '_' . $id . '.' . 'jpeg';

        // Optimize and convert the image
        $optimizedUrl = ImageHelper::optimizeAndConvertImage($storage_dir, $fileName, $newFileName);

        // Update the image URL in the database
        $company = $this->companyRepository->update($company, ['logo_url' => $optimizedUrl], $userContext);

        return $company->logo_url;
    }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a Company by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Company|null
     */
    public function deactivateCompany(int $id, UserContext $userContext): ?Company
    {
        Log::info('Deactivating Company in CompanyService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $company = $this->companyRepository->find($id, $userContext);
        if ($company) {
            return $this->companyRepository->update($company, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Company by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCompany(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Company in CompanyService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $company = $this->companyRepository->find($id, $userContext);
        if ($company) {
            return $this->companyRepository->delete($company, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Company import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Companies in CompanyService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the companies table
        $columns = Schema::getColumnListing('companies');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'company_template.xlsx';
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
     * Import Companies from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Companies from xlsx in CompanyService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $companys = $data[0];
            $headers = array_shift($companys); // Remove the first row (headers)
            //TODO Check if you would like to exclude 'id'
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($companys as $index => $companyData) {
                try {
                    // Skip rows that don't have the required columns
                    $companyData = array_combine($headers, $companyData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($companyData[$excludeColumn]);
                    }

                    $this->companyRepository->create($companyData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import company at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import company at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Companies import completed with errors');
            }else{
                Log::debug('Companies imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Companies: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Companies to an Excel file based on the given filters.
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
        Log::info('Exporting Companies to xlsx in CompanyService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch companies data for export
        $companys = $this->companyRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert companies to array
        $companysArray = $companys->toArray();

        // Retrieve the columns of the companies table
        $columns = Schema::getColumnListing('companies');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'companies_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $companysArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $companys;

                public function __construct(array $headers, array $companys)
                {
                    $this->headers = $headers;
                    $this->companys = $companys;
                }

                public function collection()
                {
                    return collect($this->companys);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Companies exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Companies to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
