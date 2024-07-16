<?php

use App\Feature\Vendor\Controllers\VendorKycController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the VendorKyc model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the VendorKyc model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::prefix('kyc')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new VendorKyc: C
    Route::post('/', [VendorKycController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single VendorKyc by its ID: R
    Route::get('/id/{id}', [VendorKycController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of VendorKycs with optional filters, sorting, and pagination: R
    Route::get('/', [VendorKycController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing VendorKyc: U
    Route::put('/{id}', [VendorKycController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to upload an image or file for a VendorKyc: U
    Route::post('/{id}/uploadimgorfile', [VendorKycController::class, 'uploadImgOrFile'])->middleware('checkPrivileges:TENANT_ALL');

    
    // Route to deactivate a VendorKyc (soft delete): U
    Route::patch('/{id}/deactivate', [VendorKycController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a VendorKyc permanently: D
    Route::delete('/{id}', [VendorKycController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing VendorKycs
    Route::get('/xlsxtemplate', [VendorKycController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import VendorKycs from an XLSX file
    Route::post('/import/xlsx', [VendorKycController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export VendorKycs to an XLSX file
    Route::get('/export/xlsx', [VendorKycController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});