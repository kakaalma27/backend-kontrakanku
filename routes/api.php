<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authenticationsApi\AddressController;
use App\Http\Controllers\authenticationsApi\UserController;
use App\Http\Controllers\authenticationsApi\HouseController;
use App\Http\Controllers\authenticationsApi\HouseImageController;
use App\Http\Controllers\authenticationsApi\TransactionController;
use App\Http\Controllers\authenticationsApi\UserBookingHouseController;
use App\Http\Controllers\authenticationsApi\OwnerResponseController;
use App\Http\Controllers\authenticationsApi\UserBookmarkController;
use App\Http\Controllers\authenticationsApi\UserComplaintController;
use App\Models\transactionsDetails;

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
    Route::put('user', [UserController::class, 'editProfile']);
    Route::post('logout', [UserController::class, 'logout']);
  });
});
Route::middleware('auth:sanctum')->group(function () {
  Route::get('/addresses', [AddressController::class, 'index']);
  Route::get('/addresses/all', [AddressController::class, 'allAddresses']); // Melihat semua alamat (Admin Only)
  Route::get('/addresses/{id}', [AddressController::class, 'show']);
  Route::post('/addresses', [AddressController::class, 'store']);
  Route::put('/addresses/{id}', [AddressController::class, 'update']);
  Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
});


Route::middleware('auth:sanctum')
  ->prefix('user-bookmark')
  ->group(function () {
    Route::get('/get-all', [UserBookmarkController::class, 'listBookmarks']);
    Route::post('/add-bookmark', [UserBookmarkController::class, 'addBookmark']);
    Route::delete('/delete-bookmark', [UserBookmarkController::class, 'deleteBookmark']);
  });

  Route::middleware('auth:sanctum')
  ->prefix('user-bookmark')
  ->group(function () {
    Route::post('/store', [UserBookingHouseController::class, 'store']);
  });
Route::middleware('auth:sanctum')
  ->prefix('transaksi')
  ->group(function () {
    Route::get('/cek-data', [TransactionController::class, 'index']);
    Route::post('/store', [TransactionController::class, 'store']);
    });
Route::middleware('auth:sanctum')
    ->prefix('transaksi-details')
    ->group(function () {
      Route::post('/store', [transactionsDetails::class, 'store']);
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
  ->prefix('user-complain')
  ->group(function () {
    Route::get('/cek-data', [UserComplaintController::class, 'index']);
    Route::post('/store', [UserComplaintController::class, 'store']);
  });

  Route::middleware('auth:sanctum')
  ->prefix('ower-response')
  ->group(function () {
    Route::post('/store', [OwnerResponseController::class, 'store']);
  });
