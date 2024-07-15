<?php

use App\Feature\Station\Controllers\StationCoverageController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the StationCoverage model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the StationCoverage model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new StationCoverage: C
    Route::post('/', [StationCoverageController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single StationCoverage by its ID: R
    Route::get('/id/{id}', [StationCoverageController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of StationCoverages with optional filters, sorting, and pagination: R
    Route::get('/', [StationCoverageController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing StationCoverage: U
    Route::put('/{id}', [StationCoverageController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a StationCoverage (soft delete): U
    Route::patch('/{id}/deactivate', [StationCoverageController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a StationCoverage permanently: D
    Route::delete('/{id}', [StationCoverageController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing StationCoverages
    Route::get('/xlsxtemplate', [StationCoverageController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import StationCoverages from an XLSX file
    Route::post('/import/xlsx', [StationCoverageController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export StationCoverages to an XLSX file
    Route::get('/export/xlsx', [StationCoverageController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
