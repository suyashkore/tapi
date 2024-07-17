<?php

namespace App\Feature\Office\Services;

use App\Feature\Office\Models\Office;
use App\Feature\Office\Repositories\OfficeRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class OfficeService
 *
 * Service class to handle business logic for the Office entity.
 *
 * @package App\Feature\Office\Services
 */
class OfficeService
{
    /**
     * The repository instance for interacting with the Office model.
     *
     * @var OfficeRepository
     */
    protected $officeRepository;

    /**
     * OfficeService constructor.
     *
     * @param OfficeRepository $officeRepository
     */
    public function __construct(OfficeRepository $officeRepository)
    {
        $this->officeRepository = $officeRepository;
    }

    /**
     * Create a new Office with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Office
     */
    public function createOffice(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Office in OfficeService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->officeRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Office by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Office|null
     */
    public function getOfficeById(int $id, UserContext $userContext): ?Office
    {
        Log::info('Fetching Office by ID in OfficeService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->officeRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Offices based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllOffices(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all offices with filters in OfficeService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->officeRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Office with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Office|null
     */
    public function updateOffice(int $id, array $data, UserContext $userContext): ?Office
    {
        Log::info('Updating Office in OfficeService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $office = $this->officeRepository->find($id, $userContext);
        if ($office) {
            return $this->officeRepository->update($office, $data, $userContext);
        }
        return null;
    }

    /**
     * Deactivate a Office by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Office|null
     */
    public function deactivateOffice(int $id, UserContext $userContext): ?Office
    {
        Log::info('Deactivating Office in OfficeService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $office = $this->officeRepository->find($id, $userContext);
        if ($office) {
            return $this->officeRepository->update($office, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Office by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteOffice(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Office in OfficeService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $office = $this->officeRepository->find($id, $userContext);
        if ($office) {
            return $this->officeRepository->delete($office, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Office import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Offices in OfficeService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the offices table
        $columns = Schema::getColumnListing('offices');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'office_template.xlsx';
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
     * Import Offices from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Offices from xlsx in OfficeService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $offices = $data[0];
            $headers = array_shift($offices); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($offices as $index => $officeData) {
                try {
                    // Skip rows that don't have the required columns
                    $officeData = array_combine($headers, $officeData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($officeData[$excludeColumn]);
                    }

                    $this->officeRepository->create($officeData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import office at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import office at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Offices import completed with errors');
            }else{
                Log::debug('Offices imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Offices: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Offices to an Excel file based on the given filters.
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
        Log::info('Exporting Offices to xlsx in OfficeService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch offices data for export
        $offices = $this->officeRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert offices to array
        $officesArray = $offices->toArray();

        // Retrieve the columns of the offices table
        $columns = Schema::getColumnListing('offices');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'offices_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $officesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $offices;

                public function __construct(array $headers, array $offices)
                {
                    $this->headers = $headers;
                    $this->offices = $offices;
                }

                public function collection()
                {
                    return collect($this->offices);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Offices exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Offices to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
