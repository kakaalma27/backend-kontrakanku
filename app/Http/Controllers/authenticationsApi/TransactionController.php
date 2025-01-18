<?php
namespace App\Http\Controllers\authenticationsApi;

use Log;
use App\Models\User;
use App\Models\house;
use App\Models\transaction;
use App\Models\houseBooking;
use Illuminate\Http\Request;
use App\Models\userBookingHouse;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\transactionsDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function index()
    {
        $houses = House::with('addresses')->get();
        $pemilik = [];

        $penyewa = [];
    
        foreach ($houses as $house) {
            $pemilik[] = User::where('role', 1)->find($house->user_id); // Pemilik rumah
        }

        $data = [
            'pemilik' => $pemilik,
            'penyewa' => $penyewa,
        ];
    
        return ResponseFormatter::success($data, 'Transaksi Berhasil diAmbil');
    }
    
    
  public function store(Request $request)
  {
      try {
          $request->validate([
              'house_id' => 'required|exists:houses,id', 
              'booking_id' => 'required|exists:user_booking_houses,id', 
              'payment' => 'nullable|string',
          ]);
  
          $house = house::find($request->house_id);
          if (!$house) {
              return ResponseFormatter::error('House tidak ditemukan', 404);
          }
  
          $booking = userBookingHouse::find($request->booking_id);
          if (!$booking) {
              return ResponseFormatter::error('Booking tidak ditemukan', 404);
          }
          $total_price = $house->price * $booking->quantity;
          $transaksi = transaction::updateOrCreate([
              'user_id' => auth()->id(),
              'house_id' => $house->id,
              'booking_id' => $booking->id,
              'payment' => $request->payment,
              'price' => $total_price,
'status' => 'pending',
          ]);

          return ResponseFormatter::success($transaksi, 'Transaksi Berhasil');
  
      } catch (\Exception $e) {
        Log::error($e->getMessage()); // Log error ke file Laravel
        return ResponseFormatter::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
      }
  }
}