<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authenticationsApi\AddressController;
use App\Http\Controllers\authenticationsApi\UserController;
use App\Http\Controllers\authenticationsApi\HouseController;
use App\Http\Controllers\authenticationsApi\BantuanController;
use App\Http\Controllers\authenticationsApi\HouseImageController;
use App\Http\Controllers\authenticationsApi\TransactionController;
use App\Http\Controllers\authenticationsApi\HouseBookingController;

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
  ->prefix('address')
  ->group(function () {
    Route::post('/store', [AddressController::class, 'store']);
  });
Route::middleware('auth:sanctum')
  ->prefix('bantuans')
  ->group(function () {
    Route::get('/cek-user', [BantuanController::class, 'index']);
    Route::post('/store', [BantuanController::class, 'store']);
  });
Route::middleware('auth:sanctum')
  ->prefix('user-bookings')
  ->group(function () {
    Route::post('/store', [HouseBookingController::class, 'store']);
  });
Route::middleware('auth:sanctum')
  ->prefix('transaksi')
  ->group(function () {
    Route::post('/store', [TransactionController::class, 'store']);
    });
Route::middleware('auth:sanctum')
  ->prefix('houses')
  ->group(function () {
    Route::get('/get-all', [HouseController::class, 'all']);
    Route::post('/store', [HouseController::class, 'store']);
    Route::put('/{id}', [HouseController::class, 'update']); 
    Route::delete('/{id}', [HouseController::class, 'destroy']); 
  });

Route::middleware('auth:sanctum')
  ->prefix('house-images')
  ->group(function () {
    Route::post('/store', [HouseImageController::class, 'store']);
    Route::get('/{id}', [HouseImageController::class, 'show']);
    Route::put('/{id}', [HouseImageController::class, 'update']);
    Route::delete('/{id}', [HouseImageController::class, 'destroy']);
  });
