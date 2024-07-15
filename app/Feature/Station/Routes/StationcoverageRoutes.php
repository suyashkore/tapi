<?php

use App\Feature\Station\Controllers\StationcoverageController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Stationcoverage model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Stationcoverage model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Stationcoverage: C
    Route::post('/', [StationcoverageController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single Stationcoverage by its ID: R
    Route::get('/id/{id}', [StationcoverageController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of Stationcoverages with optional filters, sorting, and pagination: R
    Route::get('/', [StationcoverageController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing Stationcoverage: U
    Route::put('/{id}', [StationcoverageController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');


    // Route to deactivate a Stationcoverage (soft delete): U
    Route::patch('/{id}/deactivate', [StationcoverageController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a Stationcoverage permanently: D
    Route::delete('/{id}', [StationcoverageController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing Stationcoverages
    Route::get('/xlsxtemplate', [StationcoverageController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import Stationcoverages from an XLSX file
    Route::post('/import/xlsx', [StationcoverageController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export Stationcoverages to an XLSX file
    Route::get('/export/xlsx', [StationcoverageController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
