<?php
namespace App\Http\Controllers\authenticationsApi;

use Exception;

use App\Models\User;
use App\Models\house;
use App\Models\transaction;
use Illuminate\Http\Request;
use App\Models\userBookingHouse;
use App\Helpers\ResponseFormatter;
use App\Models\transactionsDetails;
use App\Http\Controllers\Controller;

class TransactionsDetailsController extends Controller
{

  public function store(Request $request)
  {
      // Validasi input
      $request->validate([
          'house_id' => 'required|exists:houses,id',
          'booking_id' => 'required|exists:booking,id',
          'payment_id' => 'required|exists:transactions,id',
      ]);

      try {
        
          // Temukan model yang diperlukan
          $house = House::find($request->house_id);
          $booking = UserBookingHouse::find($request->booking_id);
          $payment = Transaction::find($request->payment_id); // Perbaiki dari $request->booking_id ke $request->payment_id

          // Buat detail transaksi
          $transaksiDetails = TransactionsDetails::create([
              'user_id' => auth()->id(),
              'house_id' => $house->id,
              'booking_id' => $booking->id,
              'payment_id' => $payment->id,
          ]);

          // Kembalikan respons sukses
          return ResponseFormatter::success($transaksiDetails, 'Transaksi Detail berhasil dibuat');
      } catch (Exception $e) {
          // Kembalikan respons kesalahan
          return ResponseFormatter::error(null, 'Terjadi kesalahan saat membuat detail transaksi: ' . $e->getMessage(), 500);
      }
  }
}
