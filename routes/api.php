<?php

use App\Models\transactionsDetails;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authenticationsApi\UserController;
use App\Http\Controllers\authenticationsApi\HouseController;
use App\Http\Controllers\authenticationsApi\AddressController;
use App\Http\Controllers\authenticationsApi\HouseImageController;
use App\Http\Controllers\authenticationsApi\TransactionController;
use App\Http\Controllers\authenticationsApi\UserBookmarkController;
use App\Http\Controllers\authenticationsApi\OwnerResponseController;
use App\Http\Controllers\authenticationsApi\UserComplaintController;
use App\Http\Controllers\authenticationsApi\AddressCategoryController;
use App\Http\Controllers\authenticationsApi\OwnerHandleController;
use App\Http\Controllers\authenticationsApi\OwnerTargetKeuanganController;
use App\Http\Controllers\authenticationsApi\UserBookingHouseController;

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

//pengguna
Route::middleware('auth:sanctum')
  ->prefix('user-bookmark')
  ->group(function () {
    Route::get('/get-all', [UserBookmarkController::class, 'listBookmarks']);
    Route::post('/add-bookmark', [UserBookmarkController::class, 'addBookmark']);
    Route::delete('/delete-bookmark', [UserBookmarkController::class, 'deleteBookmark']);
  });

  Route::middleware('auth:sanctum')
  ->prefix('user-booking')
  ->group(function () {
    Route::get('/user-bookings', [UserBookingHouseController::class, 'getUserBookings']);
    Route::post('/store', [UserBookingHouseController::class, 'store']);
    Route::delete('/{id}', [UserBookingHouseController::class, 'destroy']);
    Route::put('/{id}', [UserBookingHouseController::class, 'updateStatus']);
  });
Route::middleware('auth:sanctum')
  ->prefix('transaksi')
  ->group(function () {
    Route::get('/user-transaksi', [TransactionController::class, 'getUserTransaksi']);
    Route::get('/cek-data', [TransactionController::class, 'index']);
    Route::post('/store', [TransactionController::class, 'store']);
    });

// pengguna dan pemilik
Route::middleware('auth:sanctum')
  ->prefix('houses')
  ->group(function () {
    Route::get('/get-all', [HouseController::class, 'all']);
    Route::post('/store', [HouseController::class, 'store']);
    Route::post('/{id}', [HouseController::class, 'update']); 
    Route::delete('/{id}', [HouseController::class, 'destroy']); 
  });

//pengguna
  Route::middleware('auth:sanctum')
  ->prefix('user-complain')
  ->group(function () {
        Route::get('/', [UserComplaintController::class, 'index']);
        Route::post('/store', [UserComplaintController::class, 'store']);

  });

  //pemilik
  Route::middleware('auth:sanctum')
  ->prefix('owner-response')
  ->group(function () {
      Route::get('/', [OwnerResponseController::class, 'index']);
      Route::post('/{complaintId}', [OwnerResponseController::class, 'store']);
  });

  Route::middleware('auth:sanctum')
  ->prefix('owner-target')
  ->group(function () {
    Route::post('/', [OwnerTargetKeuanganController::class, 'store']);
  });

  Route::middleware('auth:sanctum')
  ->prefix('owner-handle')
  ->group(function () {
    Route::put('/handleTransaksi/{id}', [OwnerHandleController::class, 'handleTransaksi']);
    Route::get('/getTransaksi', [OwnerHandleController::class, 'getTransaksi']);
    Route::get('/getBookingStatus', [OwnerHandleController::class, 'getBookingStatus']);
    Route::get('/getBooking', [OwnerHandleController::class, 'getBooking']);
    Route::put('/handleBooking/{id}', [OwnerHandleController::class, 'handleBooking']);
    Route::get('/getTransaksiStatus', [OwnerHandleController::class, 'getTransaksiStatus']);
    Route::get('/checkResolvedStatus', [OwnerHandleController::class, 'checkResolvedStatus']);
    Route::get('/checkBantunStatus', [OwnerHandleController::class, 'checkBantunStatus']);
    Route::get('/getPenyewa', [OwnerHandleController::class, 'getPenyewa']);
    Route::get('/cekStatusPenyewa', [OwnerHandleController::class, 'cekStatusPenyewa']);
  });