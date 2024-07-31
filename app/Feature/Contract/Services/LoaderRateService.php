<?php

namespace App\Feature\Contract\Services;

use App\Feature\Contract\Models\LoaderRate;
use App\Feature\Contract\Repositories\LoaderRateRepository;
use App\Feature\Contract\Requests\LoaderRateStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class LoaderRateService
 *
 * Service class to handle business logic for the LoaderRate entity.
 *
 * @package App\Feature\Contract\Services
 */
class LoaderRateService
{
    /**
     * The repository instance for interacting with the LoaderRate model.
     *
     * @var LoaderRateRepository
     */
    protected $loaderRateRepository;

    /**
     * LoaderRateService constructor.
     *
     * @param LoaderRateRepository $loaderRateRepository
     */
    public function __construct(LoaderRateRepository $loaderRateRepository)
    {
        $this->loaderRateRepository = $loaderRateRepository;
    }

    /**
     * Create a new LoaderRate with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return LoaderRate
     */
    public function createLoaderRate(array $data, UserContext $userContext)
    {
        Log::info('Creating a new LoaderRate in LoaderRateService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->loaderRateRepository->create($data, $userContext);
    }

    /**
     * Retrieve a LoaderRate by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return LoaderRate|null
     */
    public function getLoaderRateById(int $id, UserContext $userContext): ?LoaderRate
    {
        Log::info('Fetching LoaderRate by ID in LoaderRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->loaderRateRepository->find($id, $userContext);
    }

    /**
     * Retrieve all LoaderRates based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllLoaderRates(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all loaderrates with filters in LoaderRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->loaderRateRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing LoaderRate with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return LoaderRate|null
     */
    public function updateLoaderRate(int $id, array $data, UserContext $userContext): ?LoaderRate
    {
        Log::info('Updating LoaderRate in LoaderRateService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $loaderRate = $this->loaderRateRepository->find($id, $userContext);
        if ($loaderRate) {
            return $this->loaderRateRepository->update($loaderRate, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a LoaderRate by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return LoaderRate|null
     */
    public function deactivateLoaderRate(int $id, UserContext $userContext): ?LoaderRate
    {
        Log::info('Deactivating LoaderRate in LoaderRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $loaderRate = $this->loaderRateRepository->find($id, $userContext);
        if ($loaderRate) {
            return $this->loaderRateRepository->update($loaderRate, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a LoaderRate by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteLoaderRate(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting LoaderRate in LoaderRateService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $loaderRate = $this->loaderRateRepository->find($id, $userContext);
        if ($loaderRate) {
            return $this->loaderRateRepository->delete($loaderRate, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the LoaderRate import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for LoaderRates in LoaderRateService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the loaderrates table
        $columns = Schema::getColumnListing('loader_rates');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'loaderrate_template.xlsx';
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

    public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing LoaderRates from xlsx in LoaderRateService', [
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

        $loaderRates = $data[0];
        $headers = array_shift($loaderRates); // Remove the first row (headers)
        $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

        foreach ($loaderRates as $index => $loaderRateData) {
            try {
                // Combine headers with loaderRate data
                $loaderRateData = array_combine($headers, $loaderRateData);

                // Remove excluded columns
                foreach ($excludeColumns as $excludeColumn) {
                    unset($loaderRateData[$excludeColumn]);
                }

                // Validate the loaderRate data using LoaderRateStoreRequest
                $request = new LoaderRateStoreRequest();
                $request->merge($loaderRateData);
                $request->setUserResolver(function () use ($userContext) {
                    return $userContext;
                });

                // Get validation rules
                $rules = $request->rules();

                // Validate the loaderRate data
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    // Collect validation errors
                    $errors = $validator->errors()->all();
                    Log::error('Validation failed for loaderRate at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for loaderRate at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the loaderRate
                $this->loaderRateRepository->create($loaderRateData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import loaderRate at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import loaderRate at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('LoaderRates import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('LoaderRates imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing LoaderRates: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}


    /**
     * Export LoaderRates to an Excel file based on the given filters.
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
        Log::info('Exporting LoaderRates to xlsx in LoaderRateService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch loaderrates data for export
        $loaderRates = $this->loaderRateRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert loaderrates to array
        $loaderRatesArray = $loaderRates->toArray();

        // Retrieve the columns of the loaderrates table
        $columns = Schema::getColumnListing('loader_rates');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'loaderrates_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $loaderRatesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $loaderRates;

                public function __construct(array $headers, array $loaderRates)
                {
                    $this->headers = $headers;
                    $this->loaderRates = $loaderRates;
                }

                public function collection()
                {
                    return collect($this->loaderRates);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('LoaderRates exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting LoaderRates to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
