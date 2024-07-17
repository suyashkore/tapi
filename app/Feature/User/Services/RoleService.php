<?php

namespace App\Feature\User\Services;

use App\Feature\User\Models\Role;
use App\Feature\User\Repositories\RoleRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Exception;

/**
 * Class RoleService
 *
 * Service class to handle business logic for the Role entity.
 *
 * @package App\Feature\User\Services
 */
class RoleService
{
    /**
     * The repository instance for interacting with the Role model.
     *
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * RoleService constructor.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Assign privileges to a role.
     *
     * @param Role $role
     * @param array $privilegeIds
     * @return void
     */
    public function assignPrivilegesToRole(Role $role, array $privilegeIds): void
    {
        Log::info('Assigning privileges to role in RoleService', ['role_id' => $role->id, 'privileges' => $privilegeIds]);
        try {
            $role->privileges()->sync($privilegeIds);
        } catch (\Exception $e) {
            Log::error('Failed to assign privileges to role in RoleService: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new Role with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Role
     */
    public function createRole(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Role in RoleService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->roleRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Role by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Role|null
     */
    public function getRoleById(int $id, UserContext $userContext): ?Role
    {
        Log::info('Fetching Role by ID in RoleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->roleRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Roles based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllRoles(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all roles with filters in RoleService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->roleRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Role with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Role|null
     */
    public function updateRole(int $id, array $data, UserContext $userContext): ?Role
    {
        Log::info('Updating Role in RoleService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $role = $this->roleRepository->find($id, $userContext);
        if ($role) {
            return $this->roleRepository->update($role, $data, $userContext);
        }
        return null;
    }

    /**
     * Delete a Role by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteRole(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Role in RoleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $role = $this->roleRepository->find($id, $userContext);
        if ($role) {
            return $this->roleRepository->delete($role, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Role import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Roles in RoleService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the roles table
        $columns = Schema::getColumnListing('roles');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns and adding 'privileges'
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        //ATTENTION: We added the privileges column extra.
        $headers[] = 'privileges'; // Add the 'privileges' column

        //ATTENTION: We added the sample data extra as compared to other models.
        // Create a sample data row for the template
        $sampleData = [
            [
                'id' => 1,
                'name' => 'USER_MANAGER_1',
                'description' => 'Role for managing users',
                'tenant_id' => $userContext->tenantId ?? 1, // Use tenantId from userContext or default to 1
                'privileges' => json_encode([['id' => 6, 'name' => 'USER_READ'], ['id' => 7, 'name' => 'USER_WRITE']])
            ],
            [
                'id' => 2,
                'name' => 'USER_MANAGER_2',
                'description' => 'Role for managing users',
                'tenant_id' => $userContext->tenantId ?? 1, // Use tenantId from userContext or default to 1
                'privileges' => json_encode([['id' => 6, 'name' => 'USER_READ'], ['id' => 7, 'name' => 'USER_WRITE']])
            ]
        ];

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'role_template.xlsx';
        $templatePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the template using Maatwebsite Excel
            Excel::store(new class($headers, $sampleData) implements FromCollection, WithHeadings {
                protected $headers;
                protected $data;

                public function __construct(array $headers, array $data)
                {
                    $this->headers = $headers;
                    $this->data = $data;
                }

                public function collection()
                {
                    return collect($this->data);
                }

                public function headings(): array
                {
                    return $this->headers;
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
     * Import Roles from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Roles from xlsx in RoleService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $roles = $data[0];
            $headers = array_shift($roles); // Remove the first row (headers)

            foreach ($roles as $index => $roleData) {
                try {
                    // Combine the headers with the role data
                    $roleData = array_combine($headers, $roleData);

                    // Extract tenant_id from userContext if not present in roleData
                    if (!isset($roleData['tenant_id']) || $roleData['tenant_id'] === null) {
                        $roleData['tenant_id'] = $userContext->tenantId;
                    }

                    // Decode the privileges JSON string
                    $privilegesJson = $roleData['privileges'];
                    $privilegesArray = json_decode($privilegesJson, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Invalid JSON in privileges field at row ' . ($index + 2));
                    }

                    // Validate the decoded privileges
                    $privilegeIds = array_column($privilegesArray, 'id');
                    $privilegeValidation = Validator::make(
                        ['privileges' => $privilegeIds],
                        ['privileges.*' => 'exists:privileges,id']
                    );

                    if ($privilegeValidation->fails()) {
                        $errors = $privilegeValidation->errors()->all();
                        Log::error('Validation failed for privileges at row ' . ($index + 2) . ': ', $errors);
                        $importResult['errors'][] = 'Validation failed for privileges at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                        continue;
                    }

                    // Define validation rules for role data
                    $rules = [
                        'tenant_id' => 'nullable|exists:tenants,id',
                        'name' => [
                            'required',
                            'string',
                            'max:24',
                            Rule::unique('roles')->where(function ($query) use ($roleData) {
                                return $query->where('tenant_id', $roleData['tenant_id']);
                            })
                        ],
                        'description' => 'nullable|string|max:255'
                    ];

                    // Validate the role data
                    $validator = Validator::make($roleData, $rules);

                    if ($validator->fails()) {
                        // Collect validation errors
                        $errors = $validator->errors()->all();
                        Log::error('Validation failed for role at row ' . ($index + 2) . ': ', $errors);
                        $importResult['errors'][] = 'Validation failed for role at row ' . ($index + 2) . ': ' . implode(', ', $errors);
                        continue;
                    }

                    // Remove non-role fields before creation
                    unset($roleData['privileges']);

                    // Create the role
                    $role = $this->roleRepository->create($roleData, $userContext);

                    // Assign privileges to the role
                    $this->assignPrivilegesToRole($role, $privilegeIds);

                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import role at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import role at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }

            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Roles import completed with errors');
            }else{
                Log::debug('Roles imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Roles: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Roles to an Excel file based on the given filters.
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
        Log::info('Exporting Roles to xlsx in RoleService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch roles data for export
        $roles = $this->roleRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert roles to array
        $rolesArray = $roles->toArray();

        // Retrieve the columns of the roles table
        $columns = Schema::getColumnListing('roles');

        // Define the headers and add 'privileges' after 'description'
        $headers = [];
        foreach ($columns as $column) {
            $headers[] = $column;
            if ($column == 'description') {
                $headers[] = 'privileges';
            }
        }

        // Create a new array to store the modified roles data
        $exportData = [];

        // Add privileges to each role in rolesArray
        foreach ($rolesArray as $role) {
            $roleModel = Role::find($role['id']);
            $privileges = $roleModel->privileges()->get(['id', 'name'])->toArray();
            $role['privileges'] = json_encode($privileges);

            // Create a new array for each role with the headers in the correct order
            $roleData = [];
            foreach ($headers as $header) {
                $roleData[$header] = $role[$header] ?? '';
            }

            $exportData[] = $roleData;
        }

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'roles_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $exportData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $roles;

                public function __construct(array $headers, array $roles)
                {
                    $this->headers = $headers;
                    $this->roles = $roles;
                }

                public function collection()
                {
                    return collect($this->roles);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Roles exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Roles to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
