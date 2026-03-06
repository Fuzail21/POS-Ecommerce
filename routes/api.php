<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductApiController;
use App\Http\Controllers\Api\V1\SaleApiController;
use App\Http\Controllers\Api\V1\PurchaseApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\InventoryApiController;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
| All routes are prefixed with /api/v1 via bootstrap/app.php
| Authentication: Laravel Sanctum (Bearer token)
*/

Route::prefix('v1')->group(function () {

    // ── Public ───────────────────────────────────────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // ── Protected (Sanctum) ──────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);

        // Products
        Route::get('/products',      [ProductApiController::class, 'index']);
        Route::get('/products/{id}', [ProductApiController::class, 'show']);

        // Sales
        Route::post('/sales', [SaleApiController::class, 'store']);

        // Purchases
        Route::post('/purchases', [PurchaseApiController::class, 'store']);

        // Dashboard KPIs
        Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);

        // Inventory
        Route::get('/inventory/stock', [InventoryApiController::class, 'stock']);
    });
});
