<?php

use App\Feature\User\Controllers\UserOtpController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the UserOtp model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the UserOtp model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

 Route::prefix('otps')->middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new UserOtp: C
    Route::post('/', [UserOtpController::class, 'store'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a single UserOtp by its ID: R
    Route::get('/id/{id}', [UserOtpController::class, 'show'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a list of UserOtps with optional filters, sorting, and pagination: R
    Route::get('/', [UserOtpController::class, 'index'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to update an existing UserOtp: U
    Route::put('/{id}', [UserOtpController::class, 'update'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to delete a UserOtp permanently: D
    Route::delete('/{id}', [UserOtpController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

});
