<?php

use Illuminate\Support\Facades\Route;

// Wrap all route definitions within the 'api' middleware group
Route::middleware('api')->group(function () {

    // Prefix all routes with 'stapi/v1' to indicate the API version
    Route::prefix('tapi/v1')->group(function () {

        // Include User Authentication routes
        Route::prefix('userauth')->group(base_path('app/Feature/User/Routes/AuthRoutes.php'));

        // Include Tenant routes
        Route::group(['prefix' => 'tenants'], function () {
            require base_path('app/Feature/Tenant/Routes/TenantRoutes.php');
            require base_path('app/Feature/Tenant/Routes/TenantKycRoutes.php');
        });

        // Include Tenant routes
        Route::group(['prefix' => 'users'], function () {
            require base_path('app/Feature/User/Routes/PrivilegeRoutes.php');
            require base_path('app/Feature/User/Routes/RoleRoutes.php');
            require base_path('app/Feature/User/Routes/UserRoutes.php');
            require base_path('app/Feature/User/Routes/UserOtpRoutes.php');
        });

        // Include Company routes
        Route::group(['prefix' => 'companies'], function () {
            require base_path('app/Feature/Company/Routes/CompanyRoutes.php');
        });

        //Include Office routes
        Route::group(['prefix' => 'offices'], function () {
            require base_path('app/Feature/Office/Routes/OfficeRoutes.php');
            require base_path('app/Feature/Office/Routes/CpKycRoutes.php');
        });

        //Include Contract routes
        Route::group(['prefix' => 'contracts'], function () {
            require base_path('app/Feature/Contract/Routes/LoaderRateRoutes.php');
            require base_path('app/Feature/Contract/Routes/DriverRateRoutes.php');
        });

        //Include Customer routes
        Route::group(['prefix' => 'customers'], function () {
            require base_path('app/Feature/Customer/Routes/CustomerRoutes.php');
        });

        //Include Vendor routes
        Route::group(['prefix' => 'vendors'], function () {
            require base_path('app/Feature/Vendor/Routes/VendorRoutes.php');
            require base_path('app/Feature/Vendor/Routes/VendorKycRoutes.php');

        });

        //Include Fleet routes
        Route::group(['prefix' => 'fleet'], function () {
            require base_path('app/Feature/Fleet/Routes/VehicleRoutes.php');
        });


       // Include stationcoverages routes
        Route::group(['prefix' => 'stationcoverages'], function () {
            require base_path('app/Feature/Station/Routes/StationCoverageRoutes.php');
        });

        // ... include other route groups as necessary

    });

    // ... any additional API routes that are not part of the 'stapi/v1' prefix
});

// ... any additional route definitions outside the API context
