<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/users/{id}', 'showUserById');
});



Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Products
Route::get('/products', [ProductController::class, 'showProducts']);
Route::get('/products/{productId}', [ProductController::class, 'showProductById']);
Route::post('/products/{productId}/check-stock', [ProductController::class, 'checkStock']);
Route::post('/products/{productId}/decrease-stock', [ProductController::class, 'decreaseStock']);

// Posts
Route::get('/posts', [PostController::class, 'showPosts']);
Route::get('/posts/{post:slug}', [PostController::class, 'showPostById']);

// Members
Route::get('/members', [MemberController::class, 'showMembers']);
Route::get('/members/{memberId}', [MemberController::class, 'showMemberById']);
