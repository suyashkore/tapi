<?php

use App\Feature\Contract\Controllers\LoaderRateController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the LoaderRate model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the LoaderRate model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new LoaderRate: C
    Route::post('/', [LoaderRateController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single LoaderRate by its ID: R
    Route::get('/id/{id}', [LoaderRateController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of LoaderRates with optional filters, sorting, and pagination: R
    Route::get('/', [LoaderRateController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing LoaderRate: U
    Route::put('/{id}', [LoaderRateController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to deactivate a LoaderRate (soft delete): U
    Route::patch('/{id}/deactivate', [LoaderRateController::class, 'deactivate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to delete a LoaderRate permanently: D
    Route::delete('/{id}', [LoaderRateController::class, 'destroy'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to download an XLSX template for importing LoaderRates
    Route::get('/xlsxtemplate', [LoaderRateController::class, 'xlsxTemplate'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to import LoaderRates from an XLSX file
    Route::post('/import/xlsx', [LoaderRateController::class, 'importXlsx'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to export LoaderRates to an XLSX file
    Route::get('/export/xlsx', [LoaderRateController::class, 'exportXlsx'])->middleware('checkPrivileges:TENANT_ALL');

});
