<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\PaymentInfoController;
use App\Http\Controllers\API\CuponController;
use App\Http\Controllers\API\ProductController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::put('/category/{id}', [CategoryController::class, 'update']);
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
// Contacts
Route::get('/contacts', [ContactController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/contact/{id}', [ContactController::class, 'show']);
Route::put('/contact/{id}', [ContactController::class, 'update']);
Route::delete('/contact/{id}', [ContactController::class, 'destroy']);
// Payment Info
Route::get('/paymentInfos', [PaymentInfoController::class, 'index']);
Route::post('/paymentInfo', [PaymentInfoController::class, 'store']);
Route::get('/paymentInfo/{id}', [PaymentInfoController::class, 'show']);
Route::put('/paymentInfo/{id}', [PaymentInfoController::class, 'update']);
Route::delete('/paymentInfo/{id}', [PaymentInfoController::class, 'destroy']);
// Coupons
Route::get('/coupons', [CuponController::class, 'index']);
Route::post('/coupon', [CuponController::class, 'store']);
Route::get('/coupon/{id}', [CuponController::class, 'show']);
Route::put('/coupon/{id}', [CuponController::class, 'update']);
Route::delete('/coupon/{id}', [CuponController::class, 'destroy']);
// Products
Route::get('/products', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::put('/product/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);