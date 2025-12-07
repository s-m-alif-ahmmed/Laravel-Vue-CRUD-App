<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ProfileUpdateController;


// Products List Routes
Route::get('products/list', [ProductController::class, 'index']);

Route::middleware(['guest'])->group(function () {

    //  Authentication routes
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('resend_otp', [RegisterController::class, 'resend_otp']);
    Route::post('verify_otp', [RegisterController::class, 'verify_otp']);
    Route::post('forgot-password', [RegisterController::class, 'forgot_password']);
    Route::post('forgot-verify-otp', [RegisterController::class, 'forgot_verify_otp']);
    Route::post('reset-password', [RegisterController::class, 'reset_password']);
});

Route::middleware('auth:sanctum')->group(function () {

    // common routes
    Route::get('/user-detail', [LoginController::class, 'userDetails']);
    Route::post('/logout', [LoginController::class, 'logout']);

    // Profile Update routes
    Route::post('/update-profile', [ProfileUpdateController::class, 'updateDetails']);
    Route::post('/profile-avatar-upload', [ProfileUpdateController::class, 'profileAvatarUpload']);
    Route::post('/profile-avatar-remove', [ProfileUpdateController::class, 'profileAvatarRemove']);
    Route::post('/change-email', [ProfileUpdateController::class, 'changeEmail']);
    Route::post('/change-password', [ProfileUpdateController::class, 'changePassword']);

    // Products Routes
    Route::apiResource('products', ProductController::class)->only(['store', 'show', 'update', 'destroy']);

});
