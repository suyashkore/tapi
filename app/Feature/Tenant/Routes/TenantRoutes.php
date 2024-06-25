<?php

use App\Feature\Tenant\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Tenant model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Tenant model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new tenant: C
    Route::post('/', [TenantController::class, 'store'])->middleware('checkPrivileges:SYS_ALL');

    // Route to fetch a single tenant by its ID: R
    Route::get('/id/{id}', [TenantController::class, 'show'])->middleware('checkPrivileges:SYS_ALL');

    // Route to fetch a list of tenants with optional filters, sorting, and pagination: R
    Route::get('/', [TenantController::class, 'index'])->middleware('checkPrivileges:SYS_ALL');

    // Route to update an existing tenant: U
    Route::put('/{id}', [TenantController::class, 'update'])->middleware('checkPrivileges:SYS_ALL');

    // Route to upload a logo for a tenant: U
    Route::post('/{id}/uploadlogo', [TenantController::class, 'uploadLogo'])->middleware('checkPrivileges:SYS_ALL');

    // Route to deactivate a tenant (soft delete): U
    Route::patch('/{id}/deactivate', [TenantController::class, 'deactivate'])->middleware('checkPrivileges:SYS_ALL');

    // Route to delete a tenant permanently: D
    Route::delete('/{id}', [TenantController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL');

    // Route to download an XLSX template for importing tenants
    Route::get('/xlsxtemplate', [TenantController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL');

    // Route to import tenants from an XLSX file
    Route::post('/import/xlsx', [TenantController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL');

    // Route to export tenants to an XLSX file
    Route::get('/export/xlsx', [TenantController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL');

});
