<?php

namespace App\Feature\Fleet\Services;

use App\Feature\Shared\Helpers\ImgOrFileUploadHelper;
use App\Feature\Fleet\Models\Vehicle;
use App\Feature\Fleet\Repositories\VehicleRepository;
use App\Feature\Shared\Models\UserContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

/**
 * Class VehicleService
 *
 * Service class to handle business logic for the Vehicle entity.
 *
 * @package App\Feature\Fleet\Services
 */
class VehicleService
{
    /**
     * The repository instance for interacting with the Vehicle model.
     *
     * @var VehicleRepository
     */
    protected $vehicleRepository;

    /**
     * VehicleService constructor.
     *
     * @param VehicleRepository $vehicleRepository
     */
    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Create a new Vehicle with the given data: C
     *
     * @param array $data
     * @param UserContext $userContext
     * @return Vehicle
     */
    public function createVehicle(array $data, UserContext $userContext)
    {
        Log::info('Creating a new Vehicle in VehicleService', ['data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vehicleRepository->create($data, $userContext);
    }

    /**
     * Retrieve a Vehicle by its ID: R
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Vehicle|null
     */
    public function getVehicleById(int $id, UserContext $userContext): ?Vehicle
    {
        Log::info('Fetching Vehicle by ID in VehicleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vehicleRepository->find($id, $userContext);
    }

    /**
     * Retrieve all Vehicles based on filters, sorting, and pagination: R
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param UserContext $userContext
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllVehicles(array $filters, string $sortBy, string $sortOrder, int $perPage, UserContext $userContext)
    {
        Log::info('Fetching all vehicles with filters in VehicleService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'perPage' => $perPage, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        return $this->vehicleRepository->getAllWithPagination($filters, $sortBy, $sortOrder, $perPage, $userContext);
    }

    /**
     * Update an existing Vehicle with the given data: U
     *
     * @param int $id
     * @param array $data
     * @param UserContext $userContext
     * @return Vehicle|null
     */
    public function updateVehicle(int $id, array $data, UserContext $userContext): ?Vehicle
    {
        Log::info('Updating Vehicle in VehicleService', ['id' => $id, 'data' => $data, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vehicle = $this->vehicleRepository->find($id, $userContext);
        if ($vehicle) {
            return $this->vehicleRepository->update($vehicle, $data, $userContext);
        }
        return null;
    }

    /**
    * Upload an image or file for the Vehicle and update the URL in the database: U
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
        Log::info('Uploading file for Vehicle in VehicleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vehicle = $this->vehicleRepository->find($id, $userContext);

        $storage_dir = 'public/files/tms/tenant_' . ($userContext->tenantId ?? 0) . '/vehicles/vehicle'.$id;
        $filename_prefix = $urlFieldName;
        if (str_ends_with($filename_prefix, '_url')) {
            $filename_prefix = substr($filename_prefix, 0, -4);
        } elseif (str_ends_with($filename_prefix, 'url')) {
            $filename_prefix = substr($filename_prefix, 0, -3);
        }

        if (!$vehicle) {
            throw new Exception('Vehicle not found');
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
        $vehicle = $this->vehicleRepository->update($vehicle, [$urlFieldName => $fileUrl], $userContext);

        return $vehicle->$urlFieldName;
    }

    /**
     * Deactivate a Vehicle by setting its active field to false: U
     *
     * @param int $id
     * @param UserContext $userContext
     * @return Vehicle|null
     */
    public function deactivateVehicle(int $id, UserContext $userContext): ?Vehicle
    {
        Log::info('Deactivating Vehicle in VehicleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vehicle = $this->vehicleRepository->find($id, $userContext);
        if ($vehicle) {
            return $this->vehicleRepository->update($vehicle, ['active' => false], $userContext);
        }
        return null;
    }

    /**
     * Delete a Vehicle by its ID: D
     *
     * @param int $id
     * @param UserContext $userContext
     * @return bool
     */
    public function deleteVehicle(int $id, UserContext $userContext): bool
    {
        Log::info('Deleting Vehicle in VehicleService', ['id' => $id, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);
        $vehicle = $this->vehicleRepository->find($id, $userContext);
        if ($vehicle) {
            return $this->vehicleRepository->delete($vehicle, $userContext);
        }
        return false;
    }

    /**
     * Generate a template for the Vehicle import XLSX file.
     *
     * @param UserContext $userContext
     * @return string
     * @throws Exception
     */
    public function generateXlsxTemplate(UserContext $userContext): string
    {
        Log::info('Generating xlsx template for Vehicles in VehicleService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Retrieve the columns of the vehicles table
        $columns = Schema::getColumnListing('vehicles');

        // Columns to exclude
        $excludeColumns = ['created_by', 'updated_by', 'created_at', 'updated_at'];

        // Prepare headers by excluding specified columns
        $headers = array_filter($columns, function ($column) use ($excludeColumns) {
            return !in_array($column, $excludeColumns);
        });

        // Create a temporary file path
        $temp_dir = 'public/temp';
        $directory = storage_path('app/' . $temp_dir);
        $fileName = 'vehicle_template.xlsx';
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
     * Import Vehicles from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param UserContext $userContext
     * @return array
     * @throws Exception
     */
    public function importFromXlsx($file, UserContext $userContext): array
    {
        Log::info('Importing Vehicles from xlsx in VehicleService', ['userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

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

            $vehicles = $data[0];
            $headers = array_shift($vehicles); // Remove the first row (headers)
            $excludeColumns = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

            foreach ($vehicles as $index => $vehicleData) {
                try {
                    // Skip rows that don't have the required columns
                    $vehicleData = array_combine($headers, $vehicleData);

                    foreach ($excludeColumns as $excludeColumn) {
                        unset($vehicleData[$excludeColumn]);
                    }

                    $this->vehicleRepository->create($vehicleData, $userContext);
                    $importResult['imported_count']++;
                } catch (Exception $e) {
                    Log::error('Failed to import vehicle at row ' . ($index + 2) . ': ' . $e->getMessage());
                    $importResult['errors'][] = 'Failed to import vehicle at row ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }
            if (!empty($importResult['errors'])) {
                $importResult['success'] = false;
                $importResult['message'] = 'Import completed with errors';
                Log::error('Vehicles import completed with errors');
            }else{
                Log::debug('Vehicles imported successfully');
            }
        } catch (Exception $e) {
            Log::error('Error importing Vehicles: ' . $e->getMessage());
            $importResult['success'] = false;
            $importResult['message'] = 'Import failed: ' . $e->getMessage();
        }

        return $importResult;
    }

    /**
     * Export Vehicles to an Excel file based on the given filters.
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
        Log::info('Exporting Vehicles to xlsx in VehicleService', ['filters' => $filters, 'sortBy' => $sortBy, 'sortOrder' => $sortOrder, 'userContext' => ['userId' => $userContext->userId, 'tenantId' => $userContext->tenantId, 'loginId' => $userContext->loginId]]);

        // Fetch vehicles data for export
        $vehicles = $this->vehicleRepository->getAllWithoutPagination($filters, $sortBy, $sortOrder, $userContext);

        // Convert vehicles to array
        $vehiclesArray = $vehicles->toArray();

        // Retrieve the columns of the vehicles table
        $columns = Schema::getColumnListing('vehicles');

        // Define the headers
        $headers = $columns;

        // Create a temporary file path
        $directory = storage_path('app/public/temp');
        $timestamp = now()->format('Ymd_His');
        $fileName = 'vehicles_export_' . $timestamp . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        try {
            // Generate the Excel file using Maatwebsite Excel
            Excel::store(new class($headers, $vehiclesArray) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $vehicles;

                public function __construct(array $headers, array $vehicles)
                {
                    $this->headers = $headers;
                    $this->vehicles = $vehicles;
                }

                public function collection()
                {
                    return collect($this->vehicles);
                }

                public function headings(): array
                {
                    return $this->headers;
                }
            }, 'public/temp/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            Log::debug('Vehicles exported successfully at ' . $filePath);

            // Check if the file was created
            if (!File::exists($filePath)) {
                throw new \Exception("Failed to create the xlsx file at $filePath");
            }

            return $filePath;

        } catch (Exception $e) {
            Log::error('Error exporting Vehicles to XLSX: ' . $e->getMessage());
            throw $e;
        }
    }
}
