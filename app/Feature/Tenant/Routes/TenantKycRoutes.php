<?php

use App\Feature\Tenant\Controllers\TenantKycController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the TenantKyc model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the TenantKyc model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

 Route::prefix('kyc')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new TenantKyc: C
    Route::post('/', [TenantKycController::class, 'store'])->middleware('checkPrivileges:SYS_ALL');

    // Route to fetch a single TenantKyc by its ID: R
    Route::get('/id/{id}', [TenantKycController::class, 'show'])->middleware('checkPrivileges:SYS_ALL');

    // Route to fetch a list of TenantKycs with optional filters, sorting, and pagination: R
    Route::get('/', [TenantKycController::class, 'index'])->middleware('checkPrivileges:SYS_ALL');

    // Route to update an existing TenantKyc: U
    Route::put('/{id}', [TenantKycController::class, 'update'])->middleware('checkPrivileges:SYS_ALL');

    // Route to upload a logo for a TenantKyc: U
    Route::post('/{id}/uploadownerphoto', [TenantKycController::class, 'uploadOwnerPhoto'])->middleware('checkPrivileges:SYS_ALL');

    // Route to delete a TenantKyc permanently: D
    Route::delete('/{id}', [TenantKycController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL');

    // Route to download an XLSX template for importing TenantKycs
    Route::get('/xlsxtemplate', [TenantKycController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL');

    // Route to import TenantKycs from an XLSX file
    Route::post('/import/xlsx', [TenantKycController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL');

    // Route to export TenantKycs to an XLSX file
    Route::get('/export/xlsx', [TenantKycController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL');

});
