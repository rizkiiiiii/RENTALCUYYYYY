<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RentalController;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);

// Protected (Login required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) { return $request->user(); });

    // User features
    Route::post('/rentals', [RentalController::class, 'store']);
    // Route::post('/comments', [CommentController::class, 'store']); // Implementasi serupa

    // Admin features (Boss & Employee)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Brand
    Route::post('/brands', [App\Http\Controllers\Api\BrandController::class, 'store']);
    
    // Car
    Route::post('/cars', [App\Http\Controllers\Api\CarController::class, 'store']);
    // Update/Delete Car bisa ditambahkan nanti
    
    // Rental Management
    Route::get('/admin/rentals', [App\Http\Controllers\Api\RentalController::class, 'indexAdmin']);
    Route::put('/rentals/{id}', [App\Http\Controllers\Api\RentalController::class, 'update']);
});

// Route Public untuk Brand (biar form frontend bisa ambil list merek)
Route::get('/brands', [App\Http\Controllers\Api\BrandController::class, 'index']);
});