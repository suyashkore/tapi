<?php

use App\Feature\Customer\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the Customer model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the Customer model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new Customer: C
    Route::post('/', [CustomerController::class, 'store'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a single Customer by its ID: R
    Route::get('/id/{id}', [CustomerController::class, 'show'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a list of Customers with optional filters, sorting, and pagination: R
    Route::get('/', [CustomerController::class, 'index'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to update an existing Customer: U
    Route::put('/{id}', [CustomerController::class, 'update'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

  
    // Route to deactivate a Customer (soft delete): U
    Route::patch('/{id}/deactivate', [CustomerController::class, 'deactivate'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to delete a Customer permanently: D
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to download an XLSX template for importing Customers
    Route::get('/xlsxtemplate', [CustomerController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to import Customers from an XLSX file
    Route::post('/import/xlsx', [CustomerController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to export Customers to an XLSX file
    Route::get('/export/xlsx', [CustomerController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

});
