<?php

namespace App\Http\Controllers\authenticationsApi;

use Carbon\Carbon;
use App\Models\house;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\transactionsDetails;
use App\Models\userBookingHouse;

class UserBookingHouseController extends Controller
{
    public function getUserBookings(Request $request)
    {
        try {
            $user_id = auth()->id();
            $id = $request->input('id');
    
            $query = userBookingHouse::with('house.houseImage')->where('user_id', $user_id);
    
            if ($id) {
                $query->where('id', $id);
            }
    
            // Ambil data booking
            $user_booking = $query->get()->map(function ($booking) {
                $images = $booking->house->houseImage;
                $imagePath = !$images->isEmpty() ? $images->first()->path : 'URL Gambar Tidak Tersedia';
    
                // Menghitung total harga berdasarkan quantity
                $totalHarga = $booking->house->price * $booking->quantity;
    
                $formattedHarga = $totalHarga;
    
                return [
                    'id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'house_id' => $booking->house_id,
                    'name_house' => $booking->house->name ?? 'Nama Rumah Tidak Tersedia', 
                    'status' => $booking->status,
                    'quantity' => $booking->quantity,
                    'harga' => $formattedHarga,
                    'image' => $imagePath, 
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ];
            });
    
            if ($user_booking->isEmpty()) {
                return ResponseFormatter::error(
                    null,
                    'Anda belum memiliki booking.',
                    404
                );
            }
    
            return ResponseFormatter::success(
                $user_booking,
                'Daftar booking Anda berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat mengambil data booking: ' . $e->getMessage(),
                500
            );
        }
    }
    
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'house_id' => 'required|exists:houses,id',
                'quantity' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);
    
            $house = house::find($request->house_id);
    
            if ($house->quantity < $request->quantity) {
                return ResponseFormatter::error([
                    'quantity' => "Maaf, stok rumah tidak mencukupi untuk pesanan Anda."
                ], 'Booking gagal', 400);
            }
    
            $startDate = Carbon::parse($request->start_date);
            $expectedEndDate = $startDate->copy()->addDays(30);
            $endDate = Carbon::parse($request->end_date);
    
            if ($endDate != $expectedEndDate) {
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
    
            $house->decrement('quantity', $request->quantity);
    
            $booking = userBookingHouse::create([
                'user_id' => auth()->id(),
                'house_id' => $house->id,
                'status' => 'menunggu',
                'quantity' => $request->quantity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
    
            return ResponseFormatter::success($booking, 'Booking Berhasil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseFormatter::error([
                'errors' => $e->errors()
            ], 'Validasi gagal', 422);
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'message' => $e->getMessage()
            ], 'Terjadi kesalahan pada server', 500);
        }
    }
    

    public function destroy(Request $request, $id)
    {
        $booking = userBookingHouse::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'menunggu') 
            ->first();

        if (!$booking) {
            return ResponseFormatter::error(
                null,
                'Booking tidak ditemukan atau tidak dapat dihapus',
                404
            );
        }

        $house = house::find($booking->house_id);
        if ($house) {
            $house->increment('quantity');
        }


        $booking->delete();

        return ResponseFormatter::success(null, 'Booking berhasil dihapus');
    }
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string|in:menunggu,selesai,ditolak', // Sesuaikan dengan status yang ada di enum
    ]);

    $booking = userBookingHouse::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$booking) {
        return ResponseFormatter::error(
            null,
            'Booking tidak ditemukan',
            404
        );
    }

    // Perbarui status booking
    $booking->status = $request->status;
    $booking->save();

    return ResponseFormatter::success($booking, 'Status booking berhasil diperbarui');
}

}