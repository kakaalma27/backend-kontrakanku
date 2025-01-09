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
      $expectedEndDate = $startDate->copy()->addDays(30);  // Menghitung tanggal yang diharapkan (30 hari setelah start_date)
      $endDate = Carbon::parse($request->end_date);
      
      if ($endDate != $expectedEndDate) {
          $daysDifference = $expectedEndDate->diffInDays($endDate);
      
          if ($endDate < $expectedEndDate) {
              $daysLeft = $expectedEndDate->diffInDays($endDate);
              return ResponseFormatter::error([
                  'end_date' => "Oops, Anda kurang $daysLeft hari untuk mencapai 30 hari."
              ], 'Booking gagal', 400);
          } else {
              $daysExceeded = $endDate->diffInDays($expectedEndDate);
              return ResponseFormatter::error([
                  'end_date' => "Oops, Anda melebihi $daysExceeded hari dari tanggal yang diharapkan."
              ], 'Booking gagal', 400);
          }
      }
        $booking = userBookingHouse::create([
          'user_id' => auth()->id(),
          'house_id' => $house->id,
          'status' => 'pending',
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
      ]);
      $house->decrement('quantity');
      return ResponseFormatter::success($booking, 'Booking Berhasil');
    }
}