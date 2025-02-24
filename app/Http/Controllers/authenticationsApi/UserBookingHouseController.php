<?php

namespace App\Http\Controllers\authenticationsApi;

use Carbon\Carbon;
use App\Models\house;
use Illuminate\Http\Request;
use App\Models\userBookingHouse;
use App\Helpers\ResponseFormatter;
use App\Models\transactionsDetails;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;

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
            $user_booking = $query->get()->map(function ($booking) {
                $images = $booking->house->houseImage;
                $imagePath = !$images->isEmpty() ? $images->first()->path : 'URL Gambar Tidak Tersedia';
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
                'end_date' => 'nullable|date|after:start_date', // end_date bisa null
            ]);
    
            $house = house::find($request->house_id);
            if ($house->quantity < $request->quantity) {
                return ResponseFormatter::error([
                    'quantity' => "Maaf, stok rumah tidak mencukupi untuk pesanan Anda."
                ], 'Booking gagal', 400);
            }
    
            $startDate = Carbon::parse($request->start_date);
            
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy()->addMonth();
    
            if ($endDate->day !== $startDate->day) {
                return ResponseFormatter::error([
                    null => "Maaf, awal harus jatuh pada tanggal yang sama dengan akhir."
                ], 'Booking gagal', 400);
            }
    
            $monthsDiff = $startDate->diffInMonths($endDate);
    
            if ($monthsDiff > 6) {
                return ResponseFormatter::error([
                    'end_date' => "Maaf, durasi booking tidak boleh lebih dari 6 bulan."
                ], 'Booking gagal', 400);
            }
    
            if ($monthsDiff < 1) {
                return ResponseFormatter::error([
                    'end_date' => "Maaf, durasi booking minimal 1 bulan."
                ], 'Booking gagal', 400);
            }
            $pengguna = auth()->check() ? auth()->user()->name : 'Guest';

            $house->decrement('quantity', $request->quantity);
            $pemilik = $house->addresses->user_id;

            $booking = userBookingHouse::create([
                'user_id' => auth()->id(),
                'house_id' => $house->id,
                'status' => 'menunggu',
                'quantity' => $request->quantity,
                'start_date' => $request->start_date,
                'end_date' => $endDate->toDateString(), // Simpan end_date yang sudah diatur
            ]);
            event(new NewNotificationEvent($pemilik, "Booking Menunggu", "$pengguna, Mengirim Permintaan Booking"));

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

}