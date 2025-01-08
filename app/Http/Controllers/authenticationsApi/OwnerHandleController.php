<?php

namespace App\Http\Controllers\authenticationsApi;


use Exception;
use App\Models\house;
use App\Models\address;
use Illuminate\Http\Request;
use App\Models\userBookingHouse;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class OwnerHandleController extends Controller
{
    public function getBooking()
    {
        try {
            $user_id = auth()->id();
        
            $owner_house = house::where('user_id', $user_id)->pluck('id');
        
            $user_booking = userBookingHouse::with('user')
                ->whereIn('house_id', $owner_house)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'user_name' => $booking->user->name ?? 'Tidak Ada Nama',
                        'house_id' => $booking->house_id,
                        'status' => $booking->status,
                        'start_date' => $booking->start_date,
                        'end_date' => $booking->end_date,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                    ];
                });
        
            if ($user_booking->isEmpty()) {
                return ResponseFormatter::error(
                    null,
                    'Tidak ada booking yang ditemukan untuk rumah Anda.',
                    404
                );
            }
        
            return ResponseFormatter::success(
                $user_booking,
                'Daftar Booking berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat mengambil data booking: ' . $e->getMessage(),
                500
            );
        }
    }

    public function handleBooking(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:resolved,rejected',  // Validasi status yang diperbolehkan
            ]);
    
            $user_id = auth()->id();
    

            $house = house::where('user_id', $user_id)->find($id);
            if (!$house) {
                return ResponseFormatter::error(
                    null,
                    'Rumah tidak ditemukan atau Anda tidak memiliki akses ke rumah ini.',
                    403
                );
            }

            $booking = userBookingHouse::where('house_id', $id)
                ->where('status', 'pending')
                ->first();
    
            if (!$booking) {
                return ResponseFormatter::error(
                    null,
                    'Booking tidak ditemukan untuk rumah ini.',
                    404
                );
            }
    
            $booking->status = $request->status;
            $booking->save();
    
            return ResponseFormatter::success(
                $booking,
                'Status booking berhasil diperbarui.'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat memproses booking: ' . $e->getMessage(),
                500
            );
        }
    }
    
    
    

}