<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authenticationsApi\UserController;
use App\Http\Controllers\authenticationsApi\HouseController;
use App\Http\Controllers\authenticationsApi\HouseImageController;
use App\Http\Controllers\authenticationsApi\HouseCategoryController;
// use App\Http\Controllers\authenticationsApi\UserVerificationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
  Route::post('/get-user', [UserController::class, 'fetch']);
  Route::post('/login', [UserController::class, 'login']);
  Route::post('/register', [UserController::class, 'register']);
  Route::post('/check-email', [UserController::class, 'checkEmail']);
  Route::post('/verify-token', [UserController::class, 'verifyToken']);
  Route::post('/reset-password', [UserController::class, 'resetPassword']);
  Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::put('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
  });
});

Route::middleware('auth:sanctum')
  ->prefix('houses')
  ->group(function () {
    Route::get('/get-all', [HouseController::class, 'all']); // Get all houses / filtered
    Route::post('/store', [HouseController::class, 'store']); // Create new house
    Route::put('/{id}', [HouseController::class, 'update']); // Update existing house
    Route::delete('/{id}', [HouseController::class, 'destroy']); // Delete house by ID
  });

Route::middleware('auth:sanctum')
  ->prefix('house-images')
  ->group(function () {
    Route::post('/store', [HouseImageController::class, 'store']);
    Route::get('/{id}', [HouseImageController::class, 'show']);
    Route::put('/{id}', [HouseImageController::class, 'update']);
    Route::delete('/{id}', [HouseImageController::class, 'destroy']);
  });
