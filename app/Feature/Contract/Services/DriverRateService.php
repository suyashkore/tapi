<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\DriverRate;
use App\Feature\Contract\Repositories\DriverRateRepository;
use App\Feature\Contract\Requests\DriverRateStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class DriverRateService
 *
 * Service class to handle business logic for the DriverRate entity.
 *
 * @package App\Feature\Contract\Services
 */
class DriverRateService
{
    /**
     * The repository instance for interacting with the DriverRate model.
     *
     * @var DriverRateRepository
     */
    protected $driverRateRepository;

    /**
     * DriverRateService constructor.
     *
     * @param DriverRateRepository $driverRateRepository
     */
    public function __construct(DriverRateRepository $driverRateRepository)
    {
        $this->driverRateRepository = $driverRateRepository;
    }

    /**
     * Create a new DriverRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return DriverRate
     */
    public function createDriverRate(array $data, UserContext $userContext)
    {
        Log::info('Creating a new DriverRate in DriverRateService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->driverRateRepository->create($data, $userContext);
    }

    /**
     * Retrieve a DriverRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return DriverRate|null
     */
    public function getDriverRateById(int $id, UserContext $userContext): ?DriverRate
    {
        Log::info('Fetching DriverRate by ID in DriverRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->driverRateRepository->find($id, $userContext);
    }

    /**
     * Retrieve all DriverRates based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllDriverRates(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all driverrates with filters in DriverRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->driverRateRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing DriverRate with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return DriverRate|null
     */
    public function updateDriverRate(int $id, array $data, UserContext $userContext): ?DriverRate
    {
        Log::info('Updating DriverRate in DriverRateService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $driverRate = $this->driverRateRepository->find($id, $userContext);
        if ($driverRate) {
            return $this->driverRateRepository->update($driverRate, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a DriverRate by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return DriverRate|null
     */
    public function deactivateDriverRate(int $id, UserContext $userContext): ?DriverRate
    {
        Log::info('Deactivating DriverRate in DriverRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $driverRate = $this->driverRateRepository->find($id, $userContext);
        if ($driverRate) {
            return $this->driverRateRepository->update($driverRate, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a DriverRate by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteDriverRate(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting DriverRate in DriverRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $driverRate = $this->driverRateRepository->find($id, $userContext);
        if ($driverRate) {
            return $this->driverRateRepository->delete($driverRate, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the DriverRate import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for DriverRates in DriverRateService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the driverrates table
        $columns = Schema::getColumnListing('driver_rates');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'driverrate_template.xlsx';
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
 * Import DriverRates from an Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param UserContext $userContext
 * @return array
 * @throws Exception
 */
public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing DriverRates from xlsx in DriverRateService', [
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

        $driverRates = $data[0];
        $headers = array_shift($driverRates); // Remove the first row (headers)

        $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

        foreach ($driverRates as $index => $driverRateData) {
            try {
                // Combine the headers with the driver rate data
                $driverRateData = array_combine($headers, $driverRateData);

                // Extract tenant_id from userContext if not present in driverRateData
                if (!isset($driverRateData['tenant_id']) || $driverRateData['tenant_id'] === null) {
                    $driverRateData['tenant_id'] = $userContext->tenantId;
                }

                // Remove excluded columns
                foreach ($excludeColumns as $excludeColumn) {
                    unset($driverRateData[$excludeColumn]);
                }

                // Validate the driver rate data using DriverRateStoreRequest
                $request = new DriverRateStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($driverRateData);
                $request->setUserResolver(function () use ($userContext) {
                    return $userContext;
                });

                // Get validation rules
                $rules = $request->rules();

                // Validate the driver rate data
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    // Collect validation errors
                    $errors = $validator->errors()->all();
                    Log::error('Validation failed for driverRate at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for driverRate at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the driver rate
                $this->driverRateRepository->create($driverRateData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import driverRate at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import driverRate at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('DriverRates import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('DriverRates imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing DriverRates: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}


    /**
     * Export DriverRates to an Excel file based on the given filters.
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
        Log::info('Exporting DriverRates to xlsx in DriverRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch driverrates data for export
        $driverRates = $this->driverRateRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert driverrates to array
        $driverRatesArray = $driverRates->toArray();

        // Retrieve the columns of the driverrates table
        $columns = Schema::getColumnListing('driver_rates');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'driverrates_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $driverRatesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $driverRates;

                public function __construct(array $headers, array $driverRates)
                {
                    $this->headers = $headers;
                    $this->driverRates = $driverRates;
                }

                public function collection()
                {
                    return collect($this->driverRates);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('DriverRates exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting DriverRates to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
