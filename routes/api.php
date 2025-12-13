<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);
Route::get('/brands', [BrandController::class, 'index']); // Pindahin ke sini biar rapi
Route::post('/profile', [App\Http\Controllers\Api\ProfileController::class, 'update']);//update profile dan upload avatar


// Protected (Login required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {return $request->user();});

    // ✅ FITUR USER & UMUM (JANGAN TARUH DI DALAM GROUP ADMIN!)
    // Ini biar User bisa booking (store) dan lihat history (index)
    Route::apiResource('rentals', RentalController::class);

    // 🔒 Admin Only Features
    Route::middleware(['role:admin'])->group(function () {
        // Brand
        Route::post('/brands', [BrandController::class, 'store']);

        // Car
        Route::post('/cars', [CarController::class, 'store']);

        // Rental Management (Khusus Admin lihat semua)
        Route::get('/admin/rentals', [RentalController::class, 'indexAdmin']);
        Route::put('/rentals/{id}', [RentalController::class, 'update']);
    });
});
