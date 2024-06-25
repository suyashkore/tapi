<?php

use App\Feature\User\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Role model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Role model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

 Route::prefix('roles')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Role: C
    Route::post('/', [RoleController::class, 'store'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a single Role by its ID: R
    Route::get('/id/{id}', [RoleController::class, 'show'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a list of Roles with optional filters, sorting, and pagination: R
    Route::get('/', [RoleController::class, 'index'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to update an existing Role: U
    Route::put('/{id}', [RoleController::class, 'update'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to delete a Role permanently: D
    Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to download an XLSX template for importing Roles
    Route::get('/xlsxtemplate', [RoleController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to import Roles from an XLSX file
    Route::post('/import/xlsx', [RoleController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to export Roles to an XLSX file
    Route::get('/export/xlsx', [RoleController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

});
