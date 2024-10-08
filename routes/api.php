<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/user/{id}', 'showUserById')->middleware('auth:sanctum');
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Products
Route::get('/products', [ProductController::class, 'showProducts']);
Route::get('/products/{product:slug}', [ProductController::class, 'showProductById']);

// Posts
Route::get('/posts', [PostController::class, 'showPosts']);
Route::get('/posts/{post:slug}', [PostController::class, 'showPostById']);

// Orders
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderController::class, 'showOrders']);
    Route::get('/orders/{orderId}', [OrderController::class, 'showOrderById']);
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::put('/orders/{orderId}', [OrderController::class, 'updateOrder']);
    Route::delete('/orders/{orderId}', [OrderController::class, 'cancelOrder']);
});
