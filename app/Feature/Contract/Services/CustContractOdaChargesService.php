<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\CustContractOdaCharges;
use App\Feature\Contract\Repositories\CustContractOdaChargesRepository;
use App\Feature\Contract\Requests\CustContractOdaChargesStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustContractOdaChargesService
 *
 * Service class to handle business logic for the CustContractOdaCharges entity.
 *
 * @package App\Feature\Contract\Services
 */
class CustContractOdaChargesService
{
    /**
     * The repository instance for interacting with the CustContractOdaCharges model.
     *
     * @var CustContractOdaChargesRepository
     */
    protected $custContractOdaChargesRepository;

    /**
     * CustContractOdaChargesService constructor.
     *
     * @param CustContractOdaChargesRepository $custContractOdaChargesRepository
     */
    public function __construct(CustContractOdaChargesRepository $custContractOdaChargesRepository)
    {
        $this->custContractOdaChargesRepository = $custContractOdaChargesRepository;
    }

    /**
     * Create a new CustContractOdaCharges with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractOdaCharges
     */
    public function createCustContractOdaCharges(array $data, UserContext $userContext)
    {
        Log::info('Creating a new CustContractOdaCharges in CustContractOdaChargesService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractOdaChargesRepository->create($data, $userContext);
    }

    /**
     * Retrieve a CustContractOdaCharges by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return CustContractOdaCharges|null
     */
    public function getCustContractOdaChargesById(int $id, UserContext $userContext): ?CustContractOdaCharges
    {
        Log::info('Fetching CustContractOdaCharges by ID in CustContractOdaChargesService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractOdaChargesRepository->find($id, $userContext);
    }

    /**
     * Retrieve all CustContractOdaCharges based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustContractOdaCharges(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all custcontractodacharges with filters in CustContractOdaChargesService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->custContractOdaChargesRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing CustContractOdaCharges with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return CustContractOdaCharges|null
     */
    public function updateCustContractOdaCharges(int $id, array $data, UserContext $userContext): ?CustContractOdaCharges
    {
        Log::info('Updating CustContractOdaCharges in CustContractOdaChargesService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractOdaCharges = $this->custContractOdaChargesRepository->find($id, $userContext);
        if ($custContractOdaCharges) {
            return $this->custContractOdaChargesRepository->update($custContractOdaCharges, $data, $userContext);
        }
        return null;
    }


    /**
     * Delete a CustContractOdaCharges by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustContractOdaCharges(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting CustContractOdaCharges in CustContractOdaChargesService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $custContractOdaCharges = $this->custContractOdaChargesRepository->find($id, $userContext);
        if ($custContractOdaCharges) {
            return $this->custContractOdaChargesRepository->delete($custContractOdaCharges, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the CustContractOdaCharges import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for CustContractOdaCharges in CustContractOdaChargesService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the custcontractodacharges table
        $columns = Schema::getColumnListing('cust_contract_oda_charges');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'custcontractodacharges_template.xlsx';
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
 * Import CustContractOdaCharges from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing CustContractOdaCharges from xlsx in CustContractOdaChargesService', [
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

        $custContractOdaChargess = $data[0];
        $headers = array_shift($custContractOdaChargess); // Remove the first row (headers)

        foreach ($custContractOdaChargess as $index => $custContractOdaChargesData) {
            try {
                // Combine the headers with the data
                $custContractOdaChargesData = array_combine($headers, $custContractOdaChargesData);

                // Extract tenant_id from userContext if not present in data
                if (!isset($custContractOdaChargesData['tenant_id']) || $custContractOdaChargesData['tenant_id'] === null) {
                    $custContractOdaChargesData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the data using the appropriate StoreRequest
                $request = new CustContractOdaChargesStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($custContractOdaChargesData);
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
                    Log::error('Validation failed for CustContractOdaCharges at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for CustContractOdaCharges at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the entity
                $this->custContractOdaChargesRepository->create($custContractOdaChargesData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import custContractOdaCharges at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import custContractOdaCharges at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('CustContractOdaCharges import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('CustContractOdaCharges imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing CustContractOdaCharges: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}

    /**
     * Export CustContractOdaCharges to an Excel file based on the given filters.
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
        Log::info('Exporting CustContractOdaCharges to xlsx in CustContractOdaChargesService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch custcontractodacharges data for export
        $custContractOdaChargess = $this->custContractOdaChargesRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert custcontractodacharges to array
        $custContractOdaChargessArray = $custContractOdaChargess->toArray();

        // Retrieve the columns of the custcontractodacharges table
        $columns = Schema::getColumnListing('cust_contract_oda_charges');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'custcontractodacharges_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $custContractOdaChargessArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $custContractOdaChargess;

                public function __construct(array $headers, array $custContractOdaChargess)
                {
                    $this->headers = $headers;
                    $this->custContractOdaChargess = $custContractOdaChargess;
                }

                public function collection()
                {
                    return collect($this->custContractOdaChargess);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('CustContractOdaCharges exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting CustContractOdaCharges to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
