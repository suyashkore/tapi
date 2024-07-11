<?php

use App\Feature\Company\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Company model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Company model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Company: C
    Route::post('/', [CompanyController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single Company by its ID: R
    Route::get('/id/{id}', [CompanyController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of Companies with optional filters, sorting, and pagination: R
    Route::get('/', [CompanyController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing Company: U
    Route::put('/{id}', [CompanyController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to upload an image for a Company: U
    Route::post('/{id}/uploadlogo', [CompanyController::class, 'uploadlogo'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a Company (soft delete): U
    Route::patch('/{id}/deactivate', [CompanyController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a Company permanently: D
    Route::delete('/{id}', [CompanyController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing Companies
    Route::get('/xlsxtemplate', [CompanyController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import Companies from an XLSX file
    Route::post('/import/xlsx', [CompanyController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export Companies to an XLSX file
    Route::get('/export/xlsx', [CompanyController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
