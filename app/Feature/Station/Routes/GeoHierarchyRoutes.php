<?php

use App\Feature\Station\Controllers\GeoHierarchyController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the GeoHierarchy model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the GeoHierarchy model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */
//TODO: Check if below line should start with Route::prefix('modelname_lowercase_in_plural')->middleware(['jwt.auth', 'setUserContext'])->group(function () {
Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new GeoHierarchy: C
    Route::post('/', [GeoHierarchyController::class, 'store'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a single GeoHierarchy by its ID: R
    Route::get('/id/{id}', [GeoHierarchyController::class, 'show'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to fetch a list of GeoHierarchys with optional filters, sorting, and pagination: R
    Route::get('/', [GeoHierarchyController::class, 'index'])->middleware('checkPrivileges:TENANT_ALL');

    // Route to update an existing GeoHierarchy: U
    Route::put('/{id}', [GeoHierarchyController::class, 'update'])->middleware('checkPrivileges:TENANT_ALL');

});
