<?php

namespace App\Feature\Station\Services;

use App\Feature\Shared\Helpers\ImageHelper;
use App\Feature\Station\Models\Stationcoverage;
use App\Feature\Station\Repositories\StationcoverageRepository;
use App\Feature\Shared\Services\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class StationcoverageService
 *
 * Service class to handle business logic for the Stationcoverage entity.
 *
 * @package App\Feature\Station\Services
 */
class StationcoverageService
{
    /**
     * The repository instance for interacting with the Stationcoverage model.
     *
     * @var StationcoverageRepository
     */
    protected $stationcoverageRepository;

    /**
     * StationcoverageService constructor.
     *
     * @param StationcoverageRepository $stationcoverageRepository
     */
    public function __construct(StationcoverageRepository $stationcoverageRepository)
    {
        $this->stationcoverageRepository = $stationcoverageRepository;
    }

    /**
     * Create a new Stationcoverage with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Stationcoverage
     */
    public function createStationcoverage(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Stationcoverage in StationcoverageService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationcoverageRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Stationcoverage by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Stationcoverage|null
     */
    public function getStationcoverageById(int $id, UserContext $userContext): ?Stationcoverage
    {
        Log::info('Fetching Stationcoverage by ID in StationcoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationcoverageRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Stationcoverages based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllStationcoverages(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all stationcoverages with filters in StationcoverageService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->stationcoverageRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Stationcoverage with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Stationcoverage|null
     */
    public function updateStationcoverage(int $id, array $data, UserContext $userContext): ?Stationcoverage
    {
        Log::info('Updating Stationcoverage in StationcoverageService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationcoverage = $this->stationcoverageRepository->find($id, $userContext);
        if ($stationcoverage) {
            return $this->stationcoverageRepository->update($stationcoverage, $data, $userContext);
        }
        return null;
    }


    /**
     * Deactivate a Stationcoverage by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Stationcoverage|null
     */
    public function deactivateStationcoverage(int $id, UserContext $userContext): ?Stationcoverage
    {
        Log::info('Deactivating Stationcoverage in StationcoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationcoverage = $this->stationcoverageRepository->find($id, $userContext);
        if ($stationcoverage) {
            return $this->stationcoverageRepository->update($stationcoverage, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Stationcoverage by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteStationcoverage(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Stationcoverage in StationcoverageService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $stationcoverage = $this->stationcoverageRepository->find($id, $userContext);
        if ($stationcoverage) {
            return $this->stationcoverageRepository->delete($stationcoverage, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Stationcoverage import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Stationcoverages in StationcoverageService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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
     * Import Stationcoverages from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Stationcoverages from xlsx in StationcoverageService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $stationcoverages = $data[0];
            $headers = array_shift($stationcoverages); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($stationcoverages as $index => $stationcoverageData) {
                try {
                    // Skip rows that don't have the required columns
                    $stationcoverageData = array_combine($headers, $stationcoverageData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($stationcoverageData[$excludeColumn]);
                    }

                    $this->stationcoverageRepository->create($stationcoverageData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import stationcoverage at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import stationcoverage at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Stationcoverages import completed with errors');
            }else{
                Log::debug('Stationcoverages imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Stationcoverages: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Stationcoverages to an Excel file based on the given filters.
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
        Log::info('Exporting Stationcoverages to xlsx in StationcoverageService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch stationcoverages data for export
        $stationcoverages = $this->stationcoverageRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert stationcoverages to array
        $stationcoveragesArray = $stationcoverages->toArray();

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
            Excel::store(new class($headers, $stationcoveragesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $stationcoverages;

                public function __construct(array $headers, array $stationcoverages)
                {
                    $this->headers = $headers;
                    $this->stationcoverages = $stationcoverages;
                }

                public function collection()
                {
                    return collect($this->stationcoverages);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Stationcoverages exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Stationcoverages to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
