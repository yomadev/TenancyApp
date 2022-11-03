<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Tenant\TenantController;
use App\Http\Controllers\Tenant\User\AuthController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::get('/tenant/{id}', [TenantController::class,"show"]);

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

    Route::middleware(['auth:sanctum'])->prefix("tenantApi")->group(function () {
        Route::get("me/{id}", [\App\Http\Controllers\Admin\Tenant\TenantController::class, 'show']);
    });
});
