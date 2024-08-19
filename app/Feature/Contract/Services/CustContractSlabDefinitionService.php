<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\CustContractSlabDefinition;
use App\Feature\Contract\Repositories\CustContractSlabDefinitionRepository;
use App\Feature\Contract\Requests\CustContractSlabDefinitionStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustContractSlabDefinitionService
 *
 * Service class to handle business logic for the CustContractSlabDefinition entity.
 *
 * @package App\Feature\Contract\Services
 */
class CustContractSlabDefinitionService
{
    /**
     * The repository instance for interacting with the CustContractSlabDefinition model.
     *
     * @var CustContractSlabDefinitionRepository
     */
    protected $custContractSlabDefinitionRepository;

    /**
     * CustContractSlabDefinitionService constructor.
     *
     * @param CustContractSlabDefinitionRepository $custContractSlabDefinitionRepository
     */
    public function __construct(CustContractSlabDefinitionRepository $custContractSlabDefinitionRepository)
    {
        $this->custContractSlabDefinitionRepository = $custContractSlabDefinitionRepository;
    }

    /**
     * Create a new CustContractSlabDefinition with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabDefinition
     */
    public function createCustContractSlabDefinition(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CustContractSlabDefinition in CustContractSlabDefinitionService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabDefinitionRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CustContractSlabDefinition by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractSlabDefinition|null
     */
    public function getCustContractSlabDefinitionById(int $id, UserContext $userContext): ?CustContractSlabDefinition
    {
        Log::info('Fetching CustContractSlabDefinition by ID in CustContractSlabDefinitionService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabDefinitionRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CustContractSlabDefinitions based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustContractSlabDefinitions(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all custcontractslabdefinitions with filters in CustContractSlabDefinitionService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractSlabDefinitionRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CustContractSlabDefinition with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractSlabDefinition|null
     */
    public function updateCustContractSlabDefinition(int $id, array $data, UserContext $userContext): ?CustContractSlabDefinition
    {
        Log::info('Updating CustContractSlabDefinition in CustContractSlabDefinitionService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractSlabDefinition = $this->custContractSlabDefinitionRepository->find($id, $userContext);
        if ($custContractSlabDefinition) {
            return $this->custContractSlabDefinitionRepository->update($custContractSlabDefinition, $data, $userContext);
        }
        return null;
    }

    /**
     * Delete a CustContractSlabDefinition by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustContractSlabDefinition(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CustContractSlabDefinition in CustContractSlabDefinitionService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractSlabDefinition = $this->custContractSlabDefinitionRepository->find($id, $userContext);
        if ($custContractSlabDefinition) {
            return $this->custContractSlabDefinitionRepository->delete($custContractSlabDefinition, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CustContractSlabDefinition import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CustContractSlabDefinitions in CustContractSlabDefinitionService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the custcontractslabdefinitions table
        $columns = Schema::getColumnListing('cust_contract_slab_definitions');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'custcontractslabdefinition_template.xlsx';
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
 * Import CustContractSlabDefinitions from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CustContractSlabDefinitions from xlsx in CustContractSlabDefinitionService', [
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

        $custContractSlabDefinitions = $data[0];
        $headers = array_shift($custContractSlabDefinitions); // Remove the first row (headers)

        foreach ($custContractSlabDefinitions as $index => $custContractSlabDefinitionData) {
            try {
                // Combine the headers with the data
                $custContractSlabDefinitionData = array_combine($headers, $custContractSlabDefinitionData);

                // Extract tenant_id from userContext if not present in data
                if (!isset($custContractSlabDefinitionData['tenant_id']) || $custContractSlabDefinitionData['tenant_id'] === null) {
                    $custContractSlabDefinitionData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the data using the appropriate StoreRequest
                $request = new CustContractSlabDefinitionStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($custContractSlabDefinitionData);
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
                    Log::error('Validation failed for CustContractSlabDefinition at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CustContractSlabDefinition at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $this->custContractSlabDefinitionRepository->create($custContractSlabDefinitionData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import custContractSlabDefinition at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import custContractSlabDefinition at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CustContractSlabDefinitions import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CustContractSlabDefinitions imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CustContractSlabDefinitions: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}

    /**
     * Export CustContractSlabDefinitions to an Excel file based on the given filters.
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
        Log::info('Exporting CustContractSlabDefinitions to xlsx in CustContractSlabDefinitionService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch custcontractslabdefinitions data for export
        $custContractSlabDefinitions = $this->custContractSlabDefinitionRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert custcontractslabdefinitions to array
        $custContractSlabDefinitionsArray = $custContractSlabDefinitions->toArray();

        // Retrieve the columns of the custcontractslabdefinitions table
        $columns = Schema::getColumnListing('cust_contract_slab_definitions');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'custcontractslabdefinitions_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $custContractSlabDefinitionsArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $custContractSlabDefinitions;

                public function __construct(array $headers, array $custContractSlabDefinitions)
                {
                    $this->headers = $headers;
                    $this->custContractSlabDefinitions = $custContractSlabDefinitions;
                }

                public function collection()
                {
                    return collect($this->custContractSlabDefinitions);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CustContractSlabDefinitions exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CustContractSlabDefinitions to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
