<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\CustContractSlabRate;
use App\Feature\Contract\Repositories\CustContractSlabRateRepository;
use App\Feature\Contract\Requests\CustContractSlabRateStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustContractSlabRateService
 *
 * Service class to handle business logic for the CustContractSlabRate entity.
 *
 * @package App\Feature\Contract\Services
 */
class CustContractSlabRateService
{
    /**
     * The repository instance for interacting with the CustContractSlabRate model.
     *
     * @var CustContractSlabRateRepository
     */
    protected $custContractSlabRateRepository;

    /**
     * CustContractSlabRateService constructor.
     *
     * @param CustContractSlabRateRepository $custContractSlabRateRepository
     */
    public function __construct(CustContractSlabRateRepository $custContractSlabRateRepository)
    {
        $this->custContractSlabRateRepository = $custContractSlabRateRepository;
    }

    /**
     * Create a new CustContractSlabRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabRate
     */
    public function createCustContractSlabRate(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CustContractSlabRate in CustContractSlabRateService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabRateRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CustContractSlabRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractSlabRate|null
     */
    public function getCustContractSlabRateById(int $id, UserContext $userContext): ?CustContractSlabRate
    {
        Log::info('Fetching CustContractSlabRate by ID in CustContractSlabRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabRateRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CustContractSlabRates based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustContractSlabRates(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all custcontractslabrates with filters in CustContractSlabRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabRateRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CustContractSlabRate with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabRate|null
     */
    public function updateCustContractSlabRate(int $id, array $data, UserContext $userContext): ?CustContractSlabRate
    {
        Log::info('Updating CustContractSlabRate in CustContractSlabRateService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractSlabRate = $this->custContractSlabRateRepository->find($id, $userContext);
        if ($custContractSlabRate) {
            return $this->custContractSlabRateRepository->update($custContractSlabRate, $data, $userContext);
        }
        return null;
    }
    
    /**
     * Delete a CustContractSlabRate by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustContractSlabRate(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CustContractSlabRate in CustContractSlabRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractSlabRate = $this->custContractSlabRateRepository->find($id, $userContext);
        if ($custContractSlabRate) {
            return $this->custContractSlabRateRepository->delete($custContractSlabRate, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CustContractSlabRate import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CustContractSlabRates in CustContractSlabRateService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the custcontractslabrates table
        $columns = Schema::getColumnListing('cust_contract_slab_rates');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'custcontractslabrate_template.xlsx';
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
 * Import CustContractSlabRates from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CustContractSlabRates from xlsx in CustContractSlabRateService', [
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

        $custContractSlabRates = $data[0];
        $headers = array_shift($custContractSlabRates); // Remove the first row (headers)

        foreach ($custContractSlabRates as $index => $custContractSlabRateData) {
            try {
                // Combine the headers with the data
                $custContractSlabRateData = array_combine($headers, $custContractSlabRateData);

                // Extract tenant_id from userContext if not present in data
                if (!isset($custContractSlabRateData['tenant_id']) || $custContractSlabRateData['tenant_id'] === null) {
                    $custContractSlabRateData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the data using the appropriate StoreRequest
                $request = new CustContractSlabRateStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($custContractSlabRateData);
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
                    Log::error('Validation failed for CustContractSlabRate at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CustContractSlabRate at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $this->custContractSlabRateRepository->create($custContractSlabRateData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import custContractSlabRate at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import custContractSlabRate at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CustContractSlabRates import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CustContractSlabRates imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CustContractSlabRates: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}

    /**
     * Export CustContractSlabRates to an Excel file based on the given filters.
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
        Log::info('Exporting CustContractSlabRates to xlsx in CustContractSlabRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch custcontractslabrates data for export
        $custContractSlabRates = $this->custContractSlabRateRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert custcontractslabrates to array
        $custContractSlabRatesArray = $custContractSlabRates->toArray();

        // Retrieve the columns of the custcontractslabrates table
        $columns = Schema::getColumnListing('cust_contract_slab_rates');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'custcontractslabrates_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $custContractSlabRatesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $custContractSlabRates;

                public function __construct(array $headers, array $custContractSlabRates)
                {
                    $this->headers = $headers;
                    $this->custContractSlabRates = $custContractSlabRates;
                }

                public function collection()
                {
                    return collect($this->custContractSlabRates);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CustContractSlabRates exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CustContractSlabRates to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
