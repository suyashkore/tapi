<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\CustContract;
use App\Feature\Contract\Repositories\CustContractRepository;
use App\Feature\Contract\Requests\CustContractStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustContractService
 *
 * Service class to handle business logic for the CustContract entity.
 *
 * @package App\Feature\Contract\Services
 */
class CustContractService
{
    /**
     * The repository instance for interacting with the CustContract model.
     *
     * @var CustContractRepository
     */
    protected $custContractRepository;

    /**
     * CustContractService constructor.
     *
     * @param CustContractRepository $custContractRepository
     */
    public function __construct(CustContractRepository $custContractRepository)
    {
        $this->custContractRepository = $custContractRepository;
    }

    /**
     * Create a new CustContract with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContract
     */
    public function createCustContract(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CustContract in CustContractService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CustContract by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContract|null
     */
    public function getCustContractById(int $id, UserContext $userContext): ?CustContract
    {
        Log::info('Fetching CustContract by ID in CustContractService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CustContracts based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustContracts(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all custcontracts with filters in CustContractService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CustContract with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CustContract|null
     */
    public function updateCustContract(int $id, array $data, UserContext $userContext): ?CustContract
    {
        Log::info('Updating CustContract in CustContractService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContract = $this->custContractRepository->find($id, $userContext);
        if ($custContract) {
            return $this->custContractRepository->update($custContract, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a CustContract by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContract|null
     */
    public function deactivateCustContract(int $id, UserContext $userContext): ?CustContract
    {
        Log::info('Deactivating CustContract in CustContractService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContract = $this->custContractRepository->find($id, $userContext);
        if ($custContract) {
            return $this->custContractRepository->update($custContract, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a CustContract by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustContract(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CustContract in CustContractService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContract = $this->custContractRepository->find($id, $userContext);
        if ($custContract) {
            return $this->custContractRepository->delete($custContract, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CustContract import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CustContracts in CustContractService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the custcontracts table
        $columns = Schema::getColumnListing('cust_contracts');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'custcontract_template.xlsx';
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
 * Import CustContracts from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CustContracts from xlsx in CustContractService', [
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

        $custContracts = $data[0];
        $headers = array_shift($custContracts); // Remove the first row (headers)

        foreach ($custContracts as $index => $custContractData) {
            try {
                // Combine the headers with the data
                $custContractData = array_combine($headers, $custContractData);

                // Extract tenant_id from userContext if not present in data
                if (!isset($custContractData['tenant_id']) || $custContractData['tenant_id'] === null) {
                    $custContractData['tenant_id'] = $userContext->tenantId;
                }

                // Convert Excel date serial values to Y-m-d H:i:s format
                $custContractData['start_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($custContractData['start_date'])->format('Y-m-d H:i:s');
                $custContractData['end_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($custContractData['end_date'])->format('Y-m-d H:i:s');


                // Validate the data using the appropriate StoreRequest
                $request = new CustContractStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($custContractData);
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
                    Log::error('Validation failed for CustContract at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CustContract at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $this->custContractRepository->create($custContractData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import custContract at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import custContract at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CustContracts import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CustContracts imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CustContracts: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}

    /**
     * Export CustContracts to an Excel file based on the given filters.
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
        Log::info('Exporting CustContracts to xlsx in CustContractService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch custcontracts data for export
        $custContracts = $this->custContractRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        
        // Convert custcontracts to array
        $custContractsArray = $custContracts->toArray();

        // Retrieve the columns of the custcontracts table
        $columns = Schema::getColumnListing('cust_contracts');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'custcontracts_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $custContractsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $custContracts;

                public function __construct(array $headers, array $custContracts)
                {
                    $this->headers = $headers;
                    $this->custContracts = $custContracts;
                }

                public function collection()
                {
                    return collect($this->custContracts);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CustContracts exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CustContracts to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
