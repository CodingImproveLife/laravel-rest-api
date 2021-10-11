<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('news', \App\Http\Controllers\Api\NewsController::class)
    ->only('index', 'show');
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('news', \App\Http\Controllers\Api\NewsController::class)
        ->only('store', 'update', 'destroy');
});

Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
