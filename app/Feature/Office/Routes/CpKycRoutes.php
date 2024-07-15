<?php

use App\Feature\Office\Controllers\CpKycController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CpKyc model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CpKyc model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::prefix('cpkycs')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CpKyc: C
    Route::post('/', [CpKycController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CpKyc by its ID: R
    Route::get('/id/{id}', [CpKycController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CpKycs with optional filters, sorting, and pagination: R
    Route::get('/', [CpKycController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CpKyc: U
    Route::put('/{id}', [CpKycController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to upload an image or file for a CpKyc: U
    Route::post('/{id}/uploadimgorfile', [CpKycController::class, 'uploadImgOrFile'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a CpKyc (soft delete): U
    Route::patch('/{id}/deactivate', [CpKycController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CpKyc permanently: D
    Route::delete('/{id}', [CpKycController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CpKycs
    Route::get('/xlsxtemplate', [CpKycController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CpKycs from an XLSX file
    Route::post('/import/xlsx', [CpKycController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CpKycs to an XLSX file
    Route::get('/export/xlsx', [CpKycController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
