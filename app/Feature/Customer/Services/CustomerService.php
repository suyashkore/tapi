<?php

namespace App\Feature\Customer\Services;

use App\Feature\Shared\Helpers\ImageHelper;
use App\Feature\Customer\Models\Customer;
use App\Feature\Customer\Repositories\CustomerRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class CustomerService
 *
 * Service class to handle business logic for the Customer entity.
 *
 * @package App\Feature\Customer\Services
 */
class CustomerService
{
    /**
     * The repository instance for interacting with the Customer model.
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerService constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Create a new Customer with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Customer
     */
    public function createCustomer(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Customer in CustomerService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->customerRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Customer by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Customer|null
     */
    public function getCustomerById(int $id, UserContext $userContext): ?Customer
    {
        Log::info('Fetching Customer by ID in CustomerService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->customerRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Customers based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustomers(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all customers with filters in CustomerService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->customerRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Customer with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Customer|null
     */
    public function updateCustomer(int $id, array $data, UserContext $userContext): ?Customer
    {
        Log::info('Updating Customer in CustomerService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $customer = $this->customerRepository->find($id, $userContext);
        if ($customer) {
            return $this->customerRepository->update($customer, $data, $userContext);
        }
        return null;
    }

    //TODO: Remove below method if not required.
    /**
     * Upload an image for the Customer and update the image URL in the database: U
     *
     * @param int $id
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return string|null
     * @throws Exception
     */
    public function uploadImage(int $id, $file, UserContext $userContext): ?string
    {
        Log::info('Uploading image for Customer in CustomerService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $customer = $this->customerRepository->find($id, $userContext);
        //TODO: Check the directory path and edit the folder name 'img' to something suitable
        $storage_dir = 'public/images/customer/img';
        $filename_prefix = 'customer';

        if (!$customer) {
            throw new Exception('Customer not found');
        }

        // generate a unique file name but keep the same extension
        $fileName = $filename_prefix . '_orig_' . $id . '.' . $file->getClientOriginalExtension();

        // Store the file
        $path = $file->storeAs($storage_dir, $fileName);

        if (!$path) {
            throw new Exception('Failed to upload image');
        }

        // generate a unique file name but keep the same extension
        $newFileName = $filename_prefix . '_' . $id . '.' . 'jpeg';

        // Optimize and convert the image
        $optimizedUrl = ImageHelper::optimizeAndConvertImage($storage_dir, $fileName, $newFileName );

        //TODO: Replace or update 'image_url' with relevant field of model Customer
        // Update the image URL in the database
        $customer = $this->customerRepository->update($customer, ['image_url' => $optimizedUrl], $userContext);

        //TODO: Replace or update 'image_url' with relevant field of model Customer
        return $customer->image_url;
    }

    //TODO: Remove below method if not required.
    /**
     * Deactivate a Customer by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Customer|null
     */
    public function deactivateCustomer(int $id, UserContext $userContext): ?Customer
    {
        Log::info('Deactivating Customer in CustomerService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $customer = $this->customerRepository->find($id, $userContext);
        if ($customer) {
            return $this->customerRepository->update($customer, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Customer by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteCustomer(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Customer in CustomerService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $customer = $this->customerRepository->find($id, $userContext);
        if ($customer) {
            return $this->customerRepository->delete($customer, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Customer import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Customers in CustomerService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the customers table
        $columns = Schema::getColumnListing('customers');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'customer_template.xlsx';
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
     * Import Customers from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Customers from xlsx in CustomerService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $customers = $data[0];
            $headers = array_shift($customers); // Remove the first row (headers)
            // Check if you would like to exclude 'id'
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($customers as $index => $customerData) {
                try {
                    // Skip rows that don't have the required columns
                    $customerData = array_combine($headers, $customerData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($customerData[$excludeColumn]);
                    }

                    $this->customerRepository->create($customerData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import customer at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import customer at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Customers import completed with errors');
            }else{
                Log::debug('Customers imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Customers: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Customers to an Excel file based on the given filters.
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
        Log::info('Exporting Customers to xlsx in CustomerService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch customers data for export
        $customers = $this->customerRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert customers to array
        $customersArray = $customers->toArray();

        // Retrieve the columns of the customers table
        $columns = Schema::getColumnListing('customers');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'customers_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $customersArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $customers;

                public function __construct(array $headers, array $customers)
                {
                    $this->headers = $headers;
                    $this->customers = $customers;
                }

                public function collection()
                {
                    return collect($this->customers);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Customers exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Customers to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
