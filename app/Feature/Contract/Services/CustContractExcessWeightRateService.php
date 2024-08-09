<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\CustContractExcessWeightRate;
use App\Feature\Contract\Repositories\CustContractExcessWeightRateRepository;
use App\Feature\Contract\Requests\CustContractExcessWeightRateStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustContractExcessWeightRateService
 *
 * Service class to handle business logic for the CustContractExcessWeightRate entity.
 *
 * @package App\Feature\Contract\Services
 */
class CustContractExcessWeightRateService
{
    /**
     * The repository instance for interacting with the CustContractExcessWeightRate model.
     *
     * @var CustContractExcessWeightRateRepository
     */
    protected $custContractExcessWeightRateRepository;

    /**
     * CustContractExcessWeightRateService constructor.
     *
     * @param CustContractExcessWeightRateRepository $custContractExcessWeightRateRepository
     */
    public function __construct(CustContractExcessWeightRateRepository $custContractExcessWeightRateRepository)
    {
        $this->custContractExcessWeightRateRepository = $custContractExcessWeightRateRepository;
    }

    /**
     * Create a new CustContractExcessWeightRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractExcessWeightRate
     */
    public function createCustContractExcessWeightRate(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CustContractExcessWeightRate in CustContractExcessWeightRateService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractExcessWeightRateRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CustContractExcessWeightRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractExcessWeightRate|null
     */
    public function getCustContractExcessWeightRateById(int $id, UserContext $userContext): ?CustContractExcessWeightRate
    {
        Log::info('Fetching CustContractExcessWeightRate by ID in CustContractExcessWeightRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractExcessWeightRateRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CustContractExcessWeightRates based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustContractExcessWeightRates(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all custcontractexcessweightrates with filters in CustContractExcessWeightRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractExcessWeightRateRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CustContractExcessWeightRate with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractExcessWeightRate|null
     */
    public function updateCustContractExcessWeightRate(int $id, array $data, UserContext $userContext): ?CustContractExcessWeightRate
    {
        Log::info('Updating CustContractExcessWeightRate in CustContractExcessWeightRateService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractExcessWeightRate = $this->custContractExcessWeightRateRepository->find($id, $userContext);
        if ($custContractExcessWeightRate) {
            return $this->custContractExcessWeightRateRepository->update($custContractExcessWeightRate, $data, $userContext);
        }
        return null;
    }

    /**
     * Delete a CustContractExcessWeightRate by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustContractExcessWeightRate(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CustContractExcessWeightRate in CustContractExcessWeightRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractExcessWeightRate = $this->custContractExcessWeightRateRepository->find($id, $userContext);
        if ($custContractExcessWeightRate) {
            return $this->custContractExcessWeightRateRepository->delete($custContractExcessWeightRate, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CustContractExcessWeightRate import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CustContractExcessWeightRates in CustContractExcessWeightRateService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the custcontractexcessweightrates table
        $columns = Schema::getColumnListing('cust_contract_excess_weight_rates');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'custcontractexcessweightrate_template.xlsx';
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
 * Import CustContractExcessWeightRates from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CustContractExcessWeightRates from xlsx in CustContractExcessWeightRateService', [
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

        $custContractExcessWeightRates = $data[0];
        $headers = array_shift($custContractExcessWeightRates); // Remove the first row (headers)

        foreach ($custContractExcessWeightRates as $index => $custContractExcessWeightRateData) {
            try {
                // Combine the headers with the data
                $custContractExcessWeightRateData = array_combine($headers, $custContractExcessWeightRateData);

                // Extract tenant_id from userContext if not present in data
                if (!isset($custContractExcessWeightRateData['tenant_id']) || $custContractExcessWeightRateData['tenant_id'] === null) {
                    $custContractExcessWeightRateData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the data using the appropriate StoreRequest
                $request = new CustContractExcessWeightRateStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($custContractExcessWeightRateData);
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
                    Log::error('Validation failed for CustContractExcessWeightRate at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CustContractExcessWeightRate at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $this->custContractExcessWeightRateRepository->create($custContractExcessWeightRateData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import custContractExcessWeightRate at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import custContractExcessWeightRate at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CustContractExcessWeightRates import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CustContractExcessWeightRates imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CustContractExcessWeightRates: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}

    /**
     * Export CustContractExcessWeightRates to an Excel file based on the given filters.
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
        Log::info('Exporting CustContractExcessWeightRates to xlsx in CustContractExcessWeightRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch custcontractexcessweightrates data for export
        $custContractExcessWeightRates = $this->custContractExcessWeightRateRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert custcontractexcessweightrates to array
        $custContractExcessWeightRatesArray = $custContractExcessWeightRates->toArray();

        // Retrieve the columns of the custcontractexcessweightrates table
        $columns = Schema::getColumnListing('cust_contract_excess_weight_rates');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'custcontractexcessweightrates_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $custContractExcessWeightRatesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $custContractExcessWeightRates;

                public function __construct(array $headers, array $custContractExcessWeightRates)
                {
                    $this->headers = $headers;
                    $this->custContractExcessWeightRates = $custContractExcessWeightRates;
                }

                public function collection()
                {
                    return collect($this->custContractExcessWeightRates);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CustContractExcessWeightRates exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CustContractExcessWeightRates to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
