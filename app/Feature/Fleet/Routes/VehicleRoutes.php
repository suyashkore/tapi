<?php

use App\Feature\Fleet\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Vehicle model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Vehicle model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Vehicle: C
    Route::post('/', [VehicleController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single Vehicle by its ID: R
    Route::get('/id/{id}', [VehicleController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of Vehicles with optional filters, sorting, and pagination: R
    Route::get('/', [VehicleController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing Vehicle: U
    Route::put('/{id}', [VehicleController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    //TODO: Check if this route is required for Vehicle
    // Route to upload an image for a Vehicle: U
    Route::post('/{id}/uploadimage', [VehicleController::class, 'uploadImage'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a Vehicle (soft delete): U
    Route::patch('/{id}/deactivate', [VehicleController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a Vehicle permanently: D
    Route::delete('/{id}', [VehicleController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing Vehicles
    Route::get('/xlsxtemplate', [VehicleController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import Vehicles from an XLSX file
    Route::post('/import/xlsx', [VehicleController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export Vehicles to an XLSX file
    Route::get('/export/xlsx', [VehicleController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
