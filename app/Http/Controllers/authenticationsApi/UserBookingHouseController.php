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
    public function getUserBookings()
    {
        try {
            $user_id = auth()->id();
    
            $user_booking = userBookingHouse::with('house')  
                ->where('user_id', $user_id)  
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'house_id' => $booking->house_id,
                        'house_name' => $booking->house->name ?? 'Nama Rumah Tidak Tersedia', 
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
    
        $booking = userBookingHouse::updateOrCreate([
            'user_id' => auth()->id(),
            'house_id' => $house->id,
            'status' => 'pending',
        ], [
            'quantity' => $request->quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return ResponseFormatter::success($booking, 'Booking Berhasil');
    }
    

    public function destroy(Request $request, $id)
{
    $booking = userBookingHouse::where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending') // Hanya bisa menghapus booking dengan status 'pending'
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
        'status' => 'required|string|in:pending,resolved,rejected', // Sesuaikan dengan status yang ada di enum
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