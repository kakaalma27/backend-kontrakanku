<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\authenticationsApi\ChatController;
use App\Http\Controllers\authenticationsApi\UserController;
use App\Http\Controllers\authenticationsApi\HouseController;
use App\Http\Controllers\authenticationsApi\AddressController;
use App\Http\Controllers\authenticationsApi\OwnerHandleController;
use App\Http\Controllers\authenticationsApi\TransactionController;
use App\Http\Controllers\authenticationsApi\UserBookmarkController;
use App\Http\Controllers\authenticationsApi\OwnerResponseController;
use App\Http\Controllers\authenticationsApi\UserComplaintController;
use App\Http\Controllers\authenticationsApi\UserBookingHouseController;
use App\Http\Controllers\authenticationsApi\OwnerTargetKeuanganController;
use App\Http\Controllers\authenticationsApi\PembayaranController;

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
    Route::post('user', [UserController::class, 'editProfile']);
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
    Route::get('/cekPembayaran/{house_id}/{booking_id}', [TransactionController::class, 'cekPembayaran']);
    Route::get('/cek-data', [TransactionController::class, 'index']);
    Route::post('/store', [TransactionController::class, 'store']);
    });

// pengguna dan pemilik
Route::middleware('auth:sanctum')
  ->prefix('houses')
  ->group(function () {
    Route::get('/get-allOwner', [HouseController::class, 'allOwner']);
    Route::get('/get-all', [HouseController::class, 'all']);
    Route::post('/store', [HouseController::class, 'store']);
    Route::post('/{id}', [HouseController::class, 'update']); 
    Route::delete('/{id}', [HouseController::class, 'destroy']); 
    Route::delete('/{house_id}/images/{image_id}', [HouseController::class, 'deleteImage']);
    });

//pengguna
  Route::middleware('auth:sanctum')
  ->prefix('user-complain')
  ->group(function () {
        Route::get('/', [UserComplaintController::class, 'index']);
        Route::get('/getUserComplain', [UserComplaintController::class, 'getUserComplain']);
        Route::post('/store', [UserComplaintController::class, 'store']);
        Route::get('/keluhanCount', [UserComplaintController::class, 'keluhanCount']);
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
    Route::post('/store', [OwnerTargetKeuanganController::class, 'store']);
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
    Route::get('/uangBulanan', [OwnerHandleController::class, 'uangBulanan']);
    Route::get('/TransaksiBulanan', [OwnerHandleController::class, 'TransaksiBulanan']);
    Route::post('/generateAuthToken', [OwnerHandleController::class, 'generateAuthToken']);
    Route::get('/fetchNotifikasi/{id}', [OwnerHandleController::class, 'fetchNotifikasi']);
    Route::get('/StatementKeuangan', [OwnerHandleController::class, 'StatementKeuangan']);
    Route::get('/pdfStatementKeuangan', [OwnerHandleController::class, 'pdfStatementKeuangan']);
    Route::get('/fetchUserSelesai', [OwnerHandleController::class, 'fetchUserSelesai']);
  });

  Broadcast::routes(['middleware' => ['auth:sanctum']]);
  Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});