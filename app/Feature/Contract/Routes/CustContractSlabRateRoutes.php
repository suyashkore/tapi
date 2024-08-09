<?php

use App\Feature\Contract\Controllers\CustContractSlabRateController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CustContractSlabRate model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CustContractSlabRate model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::prefix('custcontractslabrates')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CustContractSlabRate: C
    Route::post('/', [CustContractSlabRateController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CustContractSlabRate by its ID: R
    Route::get('/id/{id}', [CustContractSlabRateController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CustContractSlabRates with optional filters, sorting, and pagination: R
    Route::get('/', [CustContractSlabRateController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CustContractSlabRate: U
    Route::put('/{id}', [CustContractSlabRateController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CustContractSlabRate permanently: D
    Route::delete('/{id}', [CustContractSlabRateController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CustContractSlabRates
    Route::get('/xlsxtemplate', [CustContractSlabRateController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CustContractSlabRates from an XLSX file
    Route::post('/import/xlsx', [CustContractSlabRateController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CustContractSlabRates to an XLSX file
    Route::get('/export/xlsx', [CustContractSlabRateController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
