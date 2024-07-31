<?php

namespace App\Feature\Station\Services;

use App\Feature\Station\Models\StationCoverage;
use App\Feature\Station\Repositories\StationCoverageRepository;
use App\Feature\Station\Requests\StationCoverageStoreRequest;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class StationCoverageService
 *
 * Service class to handle business logic for the StationCoverage entity.
 *
 * @package App\Feature\Station\Services
 */
class StationCoverageService
{
    /**
     * The repository instance for interacting with the StationCoverage model.
     *
     * @var StationCoverageRepository
     */
    protected $stationCoverageRepository;

    /**
     * StationCoverageService constructor.
     *
     * @param StationCoverageRepository $stationCoverageRepository
     */
    public function __construct(StationCoverageRepository $stationCoverageRepository)
    {
        $this->stationCoverageRepository = $stationCoverageRepository;
    }

    /**
     * Create a new StationCoverage with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return StationCoverage
     */
    public function createStationCoverage(array $data, UserContext $userContext)
    {
        Log::info('Creating a new StationCoverage in StationCoverageService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationCoverageRepository->create($data, $userContext);
    }

    /**
     * Retrieve a StationCoverage by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return StationCoverage|null
     */
    public function getStationCoverageById(int $id, UserContext $userContext): ?StationCoverage
    {
        Log::info('Fetching StationCoverage by ID in StationCoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationCoverageRepository->find($id, $userContext);
    }

    /**
     * Retrieve all StationCoverages based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllStationCoverages(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all stationcoverages with filters in StationCoverageService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationCoverageRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing StationCoverage with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return StationCoverage|null
     */
    public function updateStationCoverage(int $id, array $data, UserContext $userContext): ?StationCoverage
    {
        Log::info('Updating StationCoverage in StationCoverageService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationCoverage = $this->stationCoverageRepository->find($id, $userContext);
        if ($stationCoverage) {
            return $this->stationCoverageRepository->update($stationCoverage, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a StationCoverage by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return StationCoverage|null
     */
    public function deactivateStationCoverage(int $id, UserContext $userContext): ?StationCoverage
    {
        Log::info('Deactivating StationCoverage in StationCoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationCoverage = $this->stationCoverageRepository->find($id, $userContext);
        if ($stationCoverage) {
            return $this->stationCoverageRepository->update($stationCoverage, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a StationCoverage by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteStationCoverage(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting StationCoverage in StationCoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationCoverage = $this->stationCoverageRepository->find($id, $userContext);
        if ($stationCoverage) {
            return $this->stationCoverageRepository->delete($stationCoverage, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the StationCoverage import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for StationCoverages in StationCoverageService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the stationcoverages table
        $columns = Schema::getColumnListing('station_coverage');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'stationcoverage_template.xlsx';
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
     * Import StationCoverages from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
{
    Log::info('Importing StationCoverages from xlsx in StationCoverageService', [
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
        $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray
        {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        Log::info('Excel data read successfully', ['data' => $data]);
        if (empty($data) || !isset($data[0])) {
            throw new Exception('The uploaded file is empty or invalid.');
        }

        $stationCoverages = $data[0];
        $headers = array_shift($stationCoverages); // Remove the first row (headers)
        $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

        foreach ($stationCoverages as $index => $stationCoverageData) {
            try {
                // Combine the headers with the station coverage data
                $stationCoverageData = array_combine($headers, $stationCoverageData);

                foreach ($excludeColumns as $excludeColumn) {
                    unset($stationCoverageData[$excludeColumn]);
                }

                // Extract tenant_id from userContext if not present in stationCoverageData
                if (!isset($stationCoverageData['tenant_id']) || $stationCoverageData['tenant_id'] === null) {
                    $stationCoverageData['tenant_id'] = $userContext->tenantId;
                }

                // Validate the station coverage data using StationCoverageStoreRequest
                $request = new StationCoverageStoreRequest();

                // Manually set the data and user context on the request
                $request->merge($stationCoverageData);
                $request->setUserResolver(function () use ($userContext) {
                    return $userContext;
                });

                // Get validation rules
                $rules = $request->rules();

                // Validate the station coverage data
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    // Collect validation errors
                    $errors = $validator->errors()->all();
                    Log::error('Validation failed for station coverage at row ' . ($index + 2) . ': ', $errors);
                    $importResult['errors'][] = 'Validation failed for station coverage at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                    continue;
                }

                // Create the station coverage
                $this->stationCoverageRepository->create($stationCoverageData, $userContext);
                $importResult['imported_count']++;
            } catch (Exception $e) {
                Log::error('Failed to import station coverage at row ' . ($index + 2) . ': ' . $e->getMessage());
                $importResult['errors'][] = 'Failed to import station coverage at row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
        if (!empty($importResult['errors'])) {
            $importResult['success'] = false;
            $importResult['message'] = 'Import completed with errors';
            Log::error('Station coverages import completed with errors', ['errors' => $importResult['errors']]);
        } else {
            Log::debug('Station coverages imported successfully');
        }
    } catch (Exception $e) {
        Log::error('Error importing StationCoverages: ' . $e->getMessage());
        $importResult['success'] = false;
        $importResult['message'] = 'Import failed: ' . $e->getMessage();
    }

    return $importResult;
}


    /**
     * Export StationCoverages to an Excel file based on the given filters.
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
        Log::info('Exporting StationCoverages to xlsx in StationCoverageService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch stationcoverages data for export
        $stationCoverages = $this->stationCoverageRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert stationcoverages to array
        $stationCoveragesArray = $stationCoverages->toArray();

        // Retrieve the columns of the stationcoverages table
        $columns = Schema::getColumnListing('station_coverage');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'stationcoverages_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $stationCoveragesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $stationCoverages;

                public function __construct(array $headers, array $stationCoverages)
                {
                    $this->headers = $headers;
                    $this->stationCoverages = $stationCoverages;
                }

                public function collection()
                {
                    return collect($this->stationCoverages);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('StationCoverages exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting StationCoverages to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
