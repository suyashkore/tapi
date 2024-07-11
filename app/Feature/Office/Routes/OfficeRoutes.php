<?php

use App\Feature\Office\Controllers\OfficeController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Office model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Office model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Office: C
    Route::post('/', [OfficeController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to fetch a single Office by its ID: R
    Route::get('/id/{id}', [OfficeController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to fetch a list of Offices with optional filters, sorting, and pagination: R
    Route::get('/', [OfficeController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to update an existing Office: U
    Route::put('/{id}', [OfficeController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to deactivate a Office (soft delete): U
    Route::patch('/{id}/deactivate', [OfficeController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to delete a Office permanently: D
    Route::delete('/{id}', [OfficeController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to download an XLSX template for importing Offices
    Route::get('/xlsxtemplate', [OfficeController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to import Offices from an XLSX file
    Route::post('/import/xlsx', [OfficeController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

    // Route to export Offices to an XLSX file
    Route::get('/export/xlsx', [OfficeController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL,SYS_ALL');

});
