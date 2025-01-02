<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\house;
use App\Models\transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\houseBooking;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'house_id' => 'required|exists:houses,id',
      'booking_id' => 'required|exists:booking,id',
      'payment' => 'nullable|string',
      'price' => 'numeric|required',
      'status' => 'integer|nullable',
    ]);
    $house = house::find($request->house_id);
    $booking = houseBooking::find($request->booking_id);

    $transaksi = transaction::create([
      'user_id' => auth()->id(),
      'house_id' => $house->id,
      'booking_id' => $booking->id,
      'payment' => $request->payment,
      'price' => $house->price,
      'status' => $request->status,
  ]);

  return ResponseFormatter::success($transaksi, 'Booking Berhasil');
  }
}
