<?php

use App\Feature\Contract\Controllers\DriverRateController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the DriverRate model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the DriverRate model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new DriverRate: C
    Route::post('/', [DriverRateController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single DriverRate by its ID: R
    Route::get('/id/{id}', [DriverRateController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of DriverRates with optional filters, sorting, and pagination: R
    Route::get('/', [DriverRateController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing DriverRate: U
    Route::put('/{id}', [DriverRateController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a DriverRate (soft delete): U
    Route::patch('/{id}/deactivate', [DriverRateController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a DriverRate permanently: D
    Route::delete('/{id}', [DriverRateController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing DriverRates
    Route::get('/xlsxtemplate', [DriverRateController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import DriverRates from an XLSX file
    Route::post('/import/xlsx', [DriverRateController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export DriverRates to an XLSX file
    Route::get('/export/xlsx', [DriverRateController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
