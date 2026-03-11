<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

// authentication endpoints (register/login are public)
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

// public product endpoints (index, show, search)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// categories: index/show are public, create/update/delete require admin
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // user utilities
    Route::get('/user', [\App\Http\Controllers\AuthController::class, 'user']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    // cart operations for authenticated users
    Route::get('/carts', [CartController::class, 'index']);
    Route::post('/carts/add', [CartController::class, 'add']);
    Route::delete('/carts/items/{item}', [CartController::class, 'remove']);

    // order checkout
    Route::post('/orders/checkout', [OrderController::class, 'checkout']);

    // admin-only operations for products and categories
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

});
