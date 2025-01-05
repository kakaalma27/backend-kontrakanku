<?php

namespace App\Http\Controllers\authenticationsApi;

use Carbon\Carbon;
use App\Models\house;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\userBookingHouse;

class UserBookingHouseController extends Controller
{
    public function store(Request $request)
    {
      $request->validate([
        'house_id' => 'required|exists:houses,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
      ]);
      $house = house::find($request->house_id);
      $startDate = Carbon::parse($request->start_date);
      $expectedEndDate = $startDate->copy()->addDays(30);
      
      if ($request->end_date != $expectedEndDate->toDateString()) {
        return ResponseFormatter::error([
              'end_date' => 'Tanggal berakhir harus 30 hari setelah tanggal booking.'
        ], 'Booking gagal', 400);
      }
      if ($house->quantity <= 0) {
        return response()->json(['message' => 'Kontrakan Tidak Tersedia'], 400);
      }
        $booking = userBookingHouse::create([
          'user_id' => auth()->id(),
          'house_id' => $house->id,
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
      ]);
      $house->decrement('quantity');
      return ResponseFormatter::success($booking, 'Booking Berhasil');
    }
}
