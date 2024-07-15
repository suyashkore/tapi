<?php

use App\Feature\User\Controllers\PrivilegeController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Privilege model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Privilege model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

 Route::prefix('privileges')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Privilege: C
    Route::post('/', [PrivilegeController::class, 'store'])->middleware('checkPrivileges:SYS_ALL');

    // Route to fetch a single Privilege by its ID: R
    Route::get('/id/{id}', [PrivilegeController::class, 'show'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a list of Privileges with optional filters, sorting, and pagination: R
    Route::get('/', [PrivilegeController::class, 'index'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to update an existing Privilege: U
    Route::put('/{id}', [PrivilegeController::class, 'update'])->middleware('checkPrivileges:SYS_ALL');

    // Route to delete a Privilege permanently: D
    Route::delete('/{id}', [PrivilegeController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL');

    // Route to download an XLSX template for importing Privileges
    Route::get('/xlsxtemplate', [PrivilegeController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL');

    // Route to import Privileges from an XLSX file
    Route::post('/import/xlsx', [PrivilegeController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL');

    // Route to export Privileges to an XLSX file
    Route::get('/export/xlsx', [PrivilegeController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

});
