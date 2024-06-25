<?php

// <project_root>/app/Feature/User/Routes/AuthRoutes.php

use App\Feature\User\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/withloginid', [AuthController::class, 'authByLoginId']);
// Define similar routes for other login methods
