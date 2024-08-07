<?php

use App\Feature\Contract\Controllers\CustContractController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CustContract model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CustContract model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
    Route::prefix('custcontracts')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CustContract: C
    Route::post('/', [CustContractController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CustContract by its ID: R
    Route::get('/id/{id}', [CustContractController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CustContracts with optional filters, sorting, and pagination: R
    Route::get('/', [CustContractController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CustContract: U
    Route::put('/{id}', [CustContractController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a CustContract (soft delete): U
    Route::patch('/{id}/deactivate', [CustContractController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CustContract permanently: D
    Route::delete('/{id}', [CustContractController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CustContracts
    Route::get('/xlsxtemplate', [CustContractController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CustContracts from an XLSX file
    Route::post('/import/xlsx', [CustContractController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CustContracts to an XLSX file
    Route::get('/export/xlsx', [CustContractController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
