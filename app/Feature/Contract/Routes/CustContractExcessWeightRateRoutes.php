<?php

use App\Feature\Contract\Controllers\CustContractExcessWeightRateController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CustContractExcessWeightRate model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CustContractExcessWeightRate model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
    Route::prefix('custcontractexcessweightrates')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CustContractExcessWeightRate: C
    Route::post('/', [CustContractExcessWeightRateController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CustContractExcessWeightRate by its ID: R
    Route::get('/id/{id}', [CustContractExcessWeightRateController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CustContractExcessWeightRates with optional filters, sorting, and pagination: R
    Route::get('/', [CustContractExcessWeightRateController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CustContractExcessWeightRate: U
    Route::put('/{id}', [CustContractExcessWeightRateController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CustContractExcessWeightRate permanently: D
    Route::delete('/{id}', [CustContractExcessWeightRateController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CustContractExcessWeightRates
    Route::get('/xlsxtemplate', [CustContractExcessWeightRateController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CustContractExcessWeightRates from an XLSX file
    Route::post('/import/xlsx', [CustContractExcessWeightRateController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CustContractExcessWeightRates to an XLSX file
    Route::get('/export/xlsx', [CustContractExcessWeightRateController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
