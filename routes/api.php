<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- */

// Public Routes
Route::post('/register', [AuthController::class , 'register']);
Route::post('/login', [AuthController::class , 'login']);

Route::get('/cars', [CarController::class , 'index']);
Route::get('/cars/{id}', [CarController::class , 'show']);
Route::get('/brands', [BrandController::class , 'index']);

// Protected Routes (Login Required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout']);
    Route::get('/user', function (Request $request) {
            return $request->user();
        }
        );

        // User Features
        Route::post('/profile', [ProfileController::class , 'update']);
        Route::apiResource('rentals', RentalController::class)->except(['create', 'edit']); // Standard Resource
    
        // Admin Features
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/brands', [BrandController::class , 'store']);
            Route::post('/cars', [CarController::class , 'store']);

            // Admin Rental Management
            Route::get('/admin/rentals', [RentalController::class , 'indexAdmin']);
            Route::put('/rentals/{id}', [RentalController::class , 'update']);
        }
        );    });
