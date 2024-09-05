<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Products
Route::get('/products', [ProductController::class, 'showProducts']);
Route::get('/products/{id}', [ProductController::class, 'showProductById']);

Route::get('/posts', [PostController::class, 'showPosts']);
