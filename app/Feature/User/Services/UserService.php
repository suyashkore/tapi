<?php

namespace App\Feature\User\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\User\Models\User;
use App\Feature\User\Repositories\UserRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class UserService
 *
 * Service class to handle business logic for the User entity.
 *
 * @package App\Feature\User\Services
 */
class UserService
{
    /**
     * The repository instance for interacting with the User model.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new User with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return User
     */
    public function createUser(array $data, UserContext $userContext)
    {
        Log::info('Creating a new User in UserService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userRepository->create($data, $userContext);
    }

    /**
     * Retrieve a User by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return User|null
     */
    public function getUserById(int $id, UserContext $userContext): ?User
    {
        Log::info('Fetching User by ID in UserService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Users based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllUsers(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all users with filters in UserService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->userRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing User with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return User|null
     */
    public function updateUser(int $id, array $data, UserContext $userContext): ?User
    {
        Log::info('Updating User in UserService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $user = $this->userRepository->find($id, $userContext);
        if ($user) {
            return $this->userRepository->update($user, $data, $userContext);
        }
        return null;
    }

    /**
    * Upload an image or file for the User and update the URL in the database: U
    *
    * @param int $id
    * @param \Illuminate\Http\UploadedFile $file
    * @param string $urlFieldName
    * @param UserContext $userContext
    * @return string|null
    * @throws Exception
    */
    public function uploadImgOrFileSrvc(int $id, $file, string $urlFieldName, UserContext $userContext): ?string
    {
        Log::info('Uploading file for User in UserService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $user = $this->userRepository->find($id, $userContext);

        $storage_dir = 'public/files/users/user'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$user) {
            throw new Exception('User not found');
        }

        // Determine the file extension
        $extension = strtolower($file->getClientOriginalExtension());

        // generate a unique file name but keep the same extension
        $fileName = $filename_prefix . '_orig_' . $id . '.' . $extension;

        // Store the file
        $path = $file->storeAs($storage_dir, $fileName);

        if (!$path) {
            throw new Exception('Failed to upload file');
        }

        // New file name
        $newFileName = $filename_prefix . '_' . $id . '.' . $extension;

        // Save the file and get the URL
        $fileUrl = ImgOrFileUploadHelper::saveImgOrFile($storage_dir, $fileName, $newFileName);

        // Update the URL in the database
        $user = $this->userRepository->update($user, [$urlFieldName => $fileUrl], $userContext);

        return $user->$urlFieldName;
    }

    /**
     * Deactivate a User by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return User|null
     */
    public function deactivateUser(int $id, UserContext $userContext): ?User
    {
        Log::info('Deactivating User in UserService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $user = $this->userRepository->find($id, $userContext);
        if ($user) {
            return $this->userRepository->update($user, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a User by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteUser(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting User in UserService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $user = $this->userRepository->find($id, $userContext);
        if ($user) {
            return $this->userRepository->delete($user, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the User import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Users in UserService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the users table
        $columns = Schema::getColumnListing('users');

        // Columns to exclude
        $excludeColumns = ['password_hash', 'created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'user_template.xlsx';
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
     * Import Users from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Users from xlsx in UserService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $users = $data[0];
            $headers = array_shift($users); // Remove the first row (headers)

            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($users as $index => $userData) {
                try {
                    // Skip rows that don't have the required columns
                    $userData = array_combine($headers, $userData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($userData[$excludeColumn]);
                    }

                    // Set the default password hash for the user
                    $userData['password_hash'] = Hash::make('007');
                    // Set the default value for failed_login_attempts
                    $userData['failed_login_attempts'] = 0;

                    $this->userRepository->create($userData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import user at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import user at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Users import completed with errors');
            }else{
                Log::debug('Users imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Users: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Users to an Excel file based on the given filters.
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
        Log::info('Exporting Users to xlsx in UserService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch users data for export
        $users = $this->userRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert users to array
        $usersArray = $users->toArray();

        // Retrieve the columns of the users table
        $columns = Schema::getColumnListing('users');

        // Define the headers
        // Exclude the password_hash column
        $headers = array_filter($columns, function ($column) {
            return $column !== 'password_hash';
        });

        // Remove password_hash from each user's data
        foreach ($usersArray as &$user) {
            unset($user['password_hash']);
        }

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'users_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $usersArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $users;

                public function __construct(array $headers, array $users)
                {
                    $this->headers = $headers;
                    $this->users = $users;
                }

                public function collection()
                {
                    return collect($this->users);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Users exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Users to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reset the password of a user.
     *
     * @param int $tenantId
     * @param int $userId
     * @param string $newPassword
     * @return void
     */
    public function resetUserPassword(int $tenantId, int $userId, string $newPassword): void
    {
        Log::info("Resetting password for user ID: $userId in tenant ID: $tenantId");

        $hashedPassword = Hash::make($newPassword);
        $this->userRepository->updateUserPassword($tenantId, $userId, $hashedPassword);
    }
}
