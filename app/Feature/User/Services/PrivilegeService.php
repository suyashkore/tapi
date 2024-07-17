<?php

namespace App\Feature\User\Services;

use App\Feature\User\Models\Privilege;
use App\Feature\User\Repositories\PrivilegeRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class PrivilegeService
 *
 * Service class to handle business logic for the Privilege entity.
 *
 * @package App\Feature\User\Services
 */
class PrivilegeService
{
    /**
     * The repository instance for interacting with the Privilege model.
     *
     * @var PrivilegeRepository
     */
    protected $privilegeRepository;

    /**
     * PrivilegeService constructor.
     *
     * @param PrivilegeRepository $privilegeRepository
     */
    public function __construct(PrivilegeRepository $privilegeRepository)
    {
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * Create a new Privilege with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Privilege
     */
    public function createPrivilege(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Privilege in PrivilegeService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->privilegeRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Privilege by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Privilege|null
     */
    public function getPrivilegeById(int $id, UserContext $userContext): ?Privilege
    {
        Log::info('Fetching Privilege by ID in PrivilegeService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->privilegeRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Privileges based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPrivileges(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all privileges with filters in PrivilegeService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->privilegeRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Privilege with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Privilege|null
     */
    public function updatePrivilege(int $id, array $data, UserContext $userContext): ?Privilege
    {
        Log::info('Updating Privilege in PrivilegeService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $privilege = $this->privilegeRepository->find($id, $userContext);
        if ($privilege) {
            return $this->privilegeRepository->update($privilege, $data, $userContext);
        }
        return null;
    }

    /**
     * Delete a Privilege by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deletePrivilege(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Privilege in PrivilegeService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $privilege = $this->privilegeRepository->find($id, $userContext);
        if ($privilege) {
            return $this->privilegeRepository->delete($privilege, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Privilege import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Privileges in PrivilegeService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the privileges table
        $columns = Schema::getColumnListing('privileges');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'privilege_template.xlsx';
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
     * Import Privileges from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Privileges from xlsx in PrivilegeService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $privileges = $data[0];
            $headers = array_shift($privileges); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($privileges as $index => $privilegeData) {
                try {
                    // Skip rows that don't have the required columns
                    $privilegeData = array_combine($headers, $privilegeData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($privilegeData[$excludeColumn]);
                    }

                    $this->privilegeRepository->create($privilegeData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import privilege at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import privilege at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }

            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Privileges import completed with errors');
            }else{
                Log::debug('Privileges imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Privileges: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Privileges to an Excel file based on the given filters.
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
        Log::info('Exporting Privileges to xlsx in PrivilegeService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch privileges data for export
        $privileges = $this->privilegeRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert privileges to array
        $privilegesArray = $privileges->toArray();

        // Retrieve the columns of the privileges table
        $columns = Schema::getColumnListing('privileges');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'privileges_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $privilegesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $privileges;

                public function __construct(array $headers, array $privileges)
                {
                    $this->headers = $headers;
                    $this->privileges = $privileges;
                }

                public function collection()
                {
                    return collect($this->privileges);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Privileges exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Privileges to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
