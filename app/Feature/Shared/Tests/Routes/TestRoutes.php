<?php

use Illuminate\Support\Facades\Route;
use App\Feature\Shared\Tests\Controllers\TestController;

/**
 * Define test routes for internal testing of shared services only.
 */

Route::middleware(['jwt.auth', 'setUserContext'])->group(function () {
    // Route to send a test sms.
    Route::post('/sendsms', [TestController::class, 'sendSms'])->middleware('checkPrivileges:SYS_ALL');


    // Other Test Routes...

});
