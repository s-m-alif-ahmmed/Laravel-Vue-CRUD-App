<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Product\ProductController;


// Products List Routes
Route::get('products/list', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    // Products Routes
    Route::apiResource('products', ProductController::class)->only(['store', 'show', 'update', 'destroy']);

});
