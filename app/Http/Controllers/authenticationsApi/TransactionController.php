<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\User;
use App\Models\house;
use App\Models\transaction;
use App\Models\houseBooking;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\userBookingHouse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

  public function index()
  {
    // $cekData = DB::table('user_booking_houses')
    // ->join('users', 'users.id', '=', 'user_booking_houses.user_id')
    // ->join('houses', 'houses.id', '=', 'user_booking_houses.house_id')
    // ->get();
    // return response()->json($cekData);
    $cekData = userBookingHouse::all();
     return response()->json($cekData);
  }

  public function store(Request $request)
  {
      try {
          $request->validate([
              'house_id' => 'required|exists:houses,id', 
              'booking_id' => 'required|exists:user_booking_houses,id', 
              'payment' => 'nullable|string',
              'status' => 'integer|nullable',
          ]);
  
          $house = house::find($request->house_id);
          if (!$house) {
              return ResponseFormatter::error('House tidak ditemukan', 404);
          }
  
          $booking = userBookingHouse::find($request->booking_id);
          if (!$booking) {
              return ResponseFormatter::error('Booking tidak ditemukan', 404);
          }
  
          $transaksi = transaction::create([
              'user_id' => auth()->id(),
              'booking_id' => $booking->id,
              'payment' => $request->payment,
              'price' => $house->price,
              'status' => $request->status,
          ]);
  
          return ResponseFormatter::success($transaksi, 'Transaksi Berhasil');
  
      } catch (\Exception $e) {
          return ResponseFormatter::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
      }
  }
}
