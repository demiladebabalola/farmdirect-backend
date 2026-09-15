<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NegotiationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);        // Browse page
Route::get('/products/{slug}', [ProductController::class, 'show']);  // Product Details page

/*
|--------------------------------------------------------------------------
| Authenticated routes (require Bearer token from login/register)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Farmer product management
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{slug}', [ProductController::class, 'update']);
    Route::delete('/products/{slug}', [ProductController::class, 'destroy']);

    // Negotiation page (negotiate.$productId.tsx)
    Route::get('/products/{slug}/negotiation', [NegotiationController::class, 'show']);
    Route::post('/negotiations/{negotiation}/offer', [NegotiationController::class, 'sendOffer']);
    Route::post('/negotiations/{negotiation}/accept', [NegotiationController::class, 'accept']);
    Route::post('/negotiations/{negotiation}/reject', [NegotiationController::class, 'reject']);
    Route::post('/negotiations/{negotiation}/message', [NegotiationController::class, 'sendMessage']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{order}/payments/initialize', [PaymentController::class, 'initialize']);
    Route::post('/payments/{reference}/verify', [PaymentController::class, 'verify']);

    // Dashboards (dashboard.customer.tsx / dashboard.farmer.tsx)
    Route::get('/dashboard/customer', [DashboardController::class, 'customer']);
    Route::get('/dashboard/farmer', [DashboardController::class, 'farmer']);
Route::get('/admin/farmers/pending', [AdminController::class, 'pendingFarmers']);
Route::get('/admin/farmers', [AdminController::class, 'allFarmers']);
Route::post('/admin/farmers/{user}/approve', [AdminController::class, 'approve']);
Route::post('/admin/farmers/{user}/suspend', [AdminController::class, 'suspend']);
});
