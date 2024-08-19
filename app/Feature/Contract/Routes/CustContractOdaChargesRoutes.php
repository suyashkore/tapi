<?php

use App\Feature\Contract\Controllers\CustContractOdaChargesController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CustContractOdaCharges model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CustContractOdaCharges model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::prefix('custcontractodacharges')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CustContractOdaCharges: C
    Route::post('/', [CustContractOdaChargesController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CustContractOdaCharges by its ID: R
    Route::get('/id/{id}', [CustContractOdaChargesController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CustContractOdaCharges with optional filters, sorting, and pagination: R
    Route::get('/', [CustContractOdaChargesController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CustContractOdaCharges: U
    Route::put('/{id}', [CustContractOdaChargesController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CustContractOdaCharges permanently: D
    Route::delete('/{id}', [CustContractOdaChargesController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CustContractOdaCharges
    Route::get('/xlsxtemplate', [CustContractOdaChargesController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CustContractOdaCharges from an XLSX file
    Route::post('/import/xlsx', [CustContractOdaChargesController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CustContractOdaCharges to an XLSX file
    Route::get('/export/xlsx', [CustContractOdaChargesController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
