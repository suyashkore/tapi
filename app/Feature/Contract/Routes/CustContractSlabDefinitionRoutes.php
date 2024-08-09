<?php

use App\Feature\Contract\Controllers\CustContractSlabDefinitionController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the CustContractSlabDefinition model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the CustContractSlabDefinition model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::prefix('custcontractslabdefinitions')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new CustContractSlabDefinition: C
    Route::post('/', [CustContractSlabDefinitionController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single CustContractSlabDefinition by its ID: R
    Route::get('/id/{id}', [CustContractSlabDefinitionController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of CustContractSlabDefinitions with optional filters, sorting, and pagination: R
    Route::get('/', [CustContractSlabDefinitionController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing CustContractSlabDefinition: U
    Route::put('/{id}', [CustContractSlabDefinitionController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a CustContractSlabDefinition permanently: D
    Route::delete('/{id}', [CustContractSlabDefinitionController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing CustContractSlabDefinitions
    Route::get('/xlsxtemplate', [CustContractSlabDefinitionController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import CustContractSlabDefinitions from an XLSX file
    Route::post('/import/xlsx', [CustContractSlabDefinitionController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export CustContractSlabDefinitions to an XLSX file
    Route::get('/export/xlsx', [CustContractSlabDefinitionController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
