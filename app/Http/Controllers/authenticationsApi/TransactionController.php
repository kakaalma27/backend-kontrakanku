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
    public function getUserTransaksi(Request $request)
    {
        try {
            $user_id = auth()->id();
            $id = $request->input('id');
    
            $query = transaction::with('house.houseImage', 'bookings')->where('user_id', $user_id);
    
            if ($id) {
                $query->where('id', $id);
            }
    
            // Ambil data booking
            $user_transaksi = $query->get()->map(function ($transaksi) {
                $images = $transaksi->house->houseImage;
                $imagePath = !$images->isEmpty() ? $images->first()->path : 'URL Gambar Tidak Tersedia';
    
                // Menghitung total harga berdasarkan quantity
                $totalHarga = $transaksi->house->price * $transaksi->quantity;
    
                $formattedHarga = $totalHarga;
    
                return [
                    'id' => $transaksi->id,
                    'user_id' => $transaksi->user_id,
                    'house_id' => $transaksi->house_id,
                    'name_house' => $transaksi->house->name ?? 'Nama Rumah Tidak Tersedia', 
                    'status' => $transaksi->status,
                    'quantity' => $transaksi->bookings->quantity,
                    'harga' => $transaksi->price,
                    'image' => $imagePath, 
                    'start_date' => $transaksi->bookings->start_date,
                    'end_date' => $transaksi->bookings->end_date,
                    'created_at' => $transaksi->created_at,
                    'updated_at' => $transaksi->updated_at,
                ];
            });
    
            if ($user_transaksi->isEmpty()) {
                return ResponseFormatter::error(
                    null,
                    'Anda belum memiliki transaksi.',
                    404
                );
            }
    
            return ResponseFormatter::success(
                $user_transaksi,
                'Daftar transaksi Anda berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage(),
                500
            );
        }
    }
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

          if ($booking->status === 'menunggu') {
            return ResponseFormatter::error('Transaksi tidak dapat dilanjutkan karena status booking masih menunggu.', 400);
        }
          $total_price = $house->price * $booking->quantity;
          $transaksi = transaction::create([
              'user_id' => auth()->id(),
              'house_id' => $house->id,
              'booking_id' => $booking->id,
              'payment' => $request->payment,
              'price' => $total_price,
                'status' => 'menunggu',
          ]);

          return ResponseFormatter::success($transaksi, 'Transaksi Berhasil');
  
      } catch (\Exception $e) {
        Log::error($e->getMessage()); // Log error ke file Laravel
        return ResponseFormatter::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
      }
  }

  public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string|in:menunggu,selesai,ditolak', // Sesuaikan dengan status yang ada di enum
    ]);

    $transaksi = transaction::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$transaksi) {
        return ResponseFormatter::error(
            null,
            'transaksi tidak ditemukan',
            404
        );
    }

    // Perbarui status transaksi
    $transaksi->status = $request->status;
    $transaksi->save();

    return ResponseFormatter::success($transaksi, 'Status transaksi berhasil diperbarui');
}
}