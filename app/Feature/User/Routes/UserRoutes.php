<?php

use App\Feature\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Define routes for the User model.
 *
 * This file contains routes for CRUD operations and other functionalities related to the User model.
 * Each route is protected by the 'jwt.auth' and 'setUserContext' middleware to ensure the user is authenticated
 * and the user context is properly set.
 */

Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {

    // Route to create a new User: C
    Route::post('/', [UserController::class, 'store'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a single User by its ID: R
    Route::get('/id/{id}', [UserController::class, 'show'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to fetch a list of Users with optional filters, sorting, and pagination: R
    Route::get('/', [UserController::class, 'index'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to update an existing User: U
    Route::put('/{id}', [UserController::class, 'update'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to upload an image or file for a User: U
    Route::post('/{id}/uploadimgorfile', [UserController::class, 'uploadImgOrFile'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to deactivate a User (soft delete): U
    Route::patch('/{id}/deactivate', [UserController::class, 'deactivate'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to delete a User permanently: D
    Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to download an XLSX template for importing Users
    Route::get('/xlsxtemplate', [UserController::class, 'xlsxTemplate'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to import Users from an XLSX file
    Route::post('/import/xlsx', [UserController::class, 'importXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to export Users to an XLSX file
    Route::get('/export/xlsx', [UserController::class, 'exportXlsx'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to reset password by admin user
    Route::put('/admin/resetpassword', [UserController::class, 'adminResetPassword'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

    // Route to change self password while logged in
    Route::put('/change/selfpassword', [UserController::class, 'changeSelfPassword'])->middleware('checkPrivileges:SYS_ALL,TENANT_ALL');

});

// Routes that do not require authentication
Route::group([], function () {
    // Route to generate OTP
    Route::post('/gen/otp', [UserController::class, 'generateOtp']);
});
