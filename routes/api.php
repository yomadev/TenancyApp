<?php

use App\Http\Controllers\Admin\Tenant\TenantController;
use App\Http\Controllers\Admin\User\AuthController;
use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("v1")->group(function () {

    // auth
    Route::post("register", [AuthController::class, 'register']);
    Route::post("login", [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->post("logout", [AuthController::class, 'logout']);

    Route::middleware(['auth:sanctum'])->group(function () {

        // user
        Route::apiResource("user", UserController::class);

        // tenant
        Route::apiResource("tenant", TenantController::class);

    });
});

