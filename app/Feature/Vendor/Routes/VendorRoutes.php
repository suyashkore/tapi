<?php

use App\Feature\Vendor\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Vendor model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Vendor model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Vendor: C
    Route::post('/', [VendorController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single Vendor by its ID: R
    Route::get('/id/{id}', [VendorController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of Vendors with optional filters, sorting, and pagination: R
    Route::get('/', [VendorController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing Vendor: U
    Route::put('/{id}', [VendorController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a Vendor (soft delete): U
    Route::patch('/{id}/deactivate', [VendorController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a Vendor permanently: D
    Route::delete('/{id}', [VendorController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing Vendors
    Route::get('/xlsxtemplate', [VendorController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import Vendors from an XLSX file
    Route::post('/import/xlsx', [VendorController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export Vendors to an XLSX file
    Route::get('/export/xlsx', [VendorController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
