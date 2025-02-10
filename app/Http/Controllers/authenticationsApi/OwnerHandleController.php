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
use App\Models\transaction;
use App\Models\User;
use App\Models\userComplaint;
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
                        'name_house' => $booking->house->name,
                        'status' => $booking->status,
                        'quantity' => $booking->quantity,
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

    public function getTransaksiStatus()
    {
        try {
            $user_id = auth()->id();
    
            // Ambil semua house_id milik user
            $house_ids = house::where('user_id', $user_id)->pluck('id');
    
            // Hitung jumlah transaksi berdasarkan status
            $pendingTransactionsCount = transaction::whereIn('house_id', $house_ids)
                ->where('status', 'menunggu')
                ->count();
    
            // Return jumlah transaksi berdasarkan status
            return ResponseFormatter::success(
                [
                    'pending_transactions_count' => $pendingTransactionsCount,
                ],
                'Jumlah transaksi berdasarkan status berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat menghitung data transaksi: ' . $e->getMessage(),
                500
            );
        }
    }
    public function checkResolvedStatus(Request $request)
    {
        $user_id = auth()->id();
    
        $houses = House::with(['addresses', 'bookings.user', 'transactions', 'user', 'houseImage'])
            ->whereHas('bookings', function ($query) {
                $query->where('status', 'selesai');
            })
            ->orWhereHas('transactions', function ($query) {
                $query->where('status', 'selesai'); 
            })
            ->where('user_id', $user_id) 
            ->get();
    
        if ($houses->isEmpty()) {
            return ResponseFormatter::error(
                null,
                'Tidak ada data yang selesai',
                404
            );
        }
    
        // Format hasil untuk output
        $results = $houses->map(function ($house) {
            return [
                'name_house' => $house->name,
                'path' => $house->houseImage->isNotEmpty() ? $house->houseImage->first()->path : null,
                'addresses' => $house->addresses->alamat,
                'owner_name' => $house->addresses->name,
                'quantity' => $house->bookings->sum('quantity'), 
                'start_date' => $house->bookings->first()->start_date ?? null, 
                'end_date' => $house->bookings->first()->end_date ?? null,  
                'metode_embayaran' => $house->transactions->first()->payment ?? null,  
                'total_harga' => $house->transactions->first()->price ?? null, 
                'penyewa' => $house->bookings->first()->user->name ?? null,  // Ambil nama penyewa dari booking pertama
            ];
        });
    
        return ResponseFormatter::success(
            $results,
            'Data berhasil didapatkan'
        );
    }
    
    public function checkBantunStatus(Request $request)
    {
        $user_complaint_count = userComplaint::where('owner_id', auth()->id())
                                            ->where('status', 'menunggu')
                                            ->count();
    
        // Jika ada keluhan yang menunggu
        if ($user_complaint_count > 0) {
            return ResponseFormatter::success(
                $user_complaint_count,
                'Data berhasil didapatkan'
            );
        } else {
            // Jika tidak ada keluhan yang menunggu
            return ResponseFormatter::success(
                0,
                'Tidak ada keluhan yang menunggu'
            );
        }
    }
    
    
    public function handleBooking(Request $request, $id)
    {
        try {
            Log::info('Validating booking status update', ['request' => $request->all()]);

            $request->validate([
                'status' => 'required|in:selesai,ditolak',
            ]);
    
            $user_id = auth()->id();
    
            $house = house::where('user_id', $user_id)->whereHas('bookings', function ($query) use ($id) {
                $query->where('id', $id);
            })->first();
    
            if (!$house) {
                return ResponseFormatter::error(
                    null,
                    'Rumah atau booking tidak ditemukan, atau Anda tidak memiliki akses ke data ini.',
                    403
                );
            }
    
            $booking = userBookingHouse::find($id);
            if (!$booking || $booking->status !== 'menunggu') {
                return ResponseFormatter::error(
                    null,
                    'Booking tidak ditemukan atau sudah diproses.',
                    404
                );
            }
            Log::info('Updating booking status', ['id' => $id, 'new_status' => $request->status]);

            // Update status
            $booking->status = $request->status;
            $booking->save();
    
            return ResponseFormatter::success(
                $booking,
                'Status booking berhasil diperbarui.'
            );
        } catch (Exception $e) {
            Log::error('Error updating booking status', ['exception' => $e->getMessage()]);
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat memproses booking: ' . $e->getMessage(),
                500
            );
        }
    }
    
    public function handleTransaksi(Request $request, $transactionId)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:selesai,ditolak', // Validasi status harus "resolved" atau "rejected"
            ]);
    
            $owner_id = auth()->id();
    
            $transaction = DB::table('transactions_houses')
                ->join('houses', 'transactions_houses.house_id', '=', 'houses.id')
                ->join('users', 'houses.user_id', '=', 'users.id') 
                ->where('houses.user_id', $owner_id) 
                ->where('transactions_houses.id', $transactionId) 
                ->select('transactions_houses.*', 'users.name as user_name', 'houses.name as house_name') // Ambil nama pengguna dan rumah
                ->first();
    
            if (!$transaction) {
                return ResponseFormatter::error(
                    null,
                    'Transaksi tidak ditemukan atau bukan milik Anda.',
                    404
                );
            }
    
            DB::table('transactions_houses')
                ->where('id', $transactionId)
                ->update([
                    'status' => $validated['status'], // Perbarui status
                    'updated_at' => now(), // Perbarui waktu
                ]);
    
            $updatedTransaction = DB::table('transactions_houses')
                ->where('id', $transactionId)
                ->first();
    
            return ResponseFormatter::success(
                $updatedTransaction,
                'Status transaksi berhasil diubah.'
            );
        } catch (ValidationException $e) {
            // Tanggapi error validasi
            return ResponseFormatter::error(
                null,
                'Validasi gagal: ' . implode(', ', $e->errors()),
                422
            );
        } catch (Exception $e) {
            // Log error dan tanggapi error umum
            Log::error('Error updating transaction status', ['exception' => $e->getMessage()]);
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat mengubah status transaksi: ' . $e->getMessage(),
                500
            );
        }
    }
     
    public function getTransaksi()
    {
        try {
            $user_id = auth()->id();
            $house_ids = house::where('user_id', $user_id)->pluck('id');
            $transactions = transaction::with('bookings', 'user', 'house')
                ->whereIn('house_id', $house_ids)
                ->get();
    
            // Mengubah struktur data sesuai kebutuhan
            $simplifiedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'price' => $transaction->price,
                    'payment' => $transaction->payment,
                    'name_house' => $transaction->house->name,
                    'name_user' => $transaction->user->name,
                    'status' => $transaction->status,
                    'quantity' => $transaction->bookings->quantity,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ];
            });
    
            return ResponseFormatter::success(
                $simplifiedTransactions,
                'Daftar transaksi berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat mengambil data Transaksi: ' . $e->getMessage(),
                500
            );
        }
    }
    
    public function getBookingStatus()
    {
        try {
            $user_id = auth()->id();
        
            $owner_house = house::where('user_id', $user_id)->pluck('id');
            $pendingBookingCount = userBookingHouse::whereIn('house_id', $owner_house)
            ->where('status', 'menunggu')  // Menambahkan filter berdasarkan status 'pending'
            ->count();  // Menghitung jumlah booking dengan status 'pending'

            return ResponseFormatter::success(
                ['pending_booking_count' => $pendingBookingCount],
                'Jumlah booking dengan status pending berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan saat menghitung data booking: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getPenyewa()
    {
        // Ambil data transaksi dengan relasi user dan bookings
        $data = Transaction::with(['user', 'bookings'])
            ->where('status', 'selesai') // Filter status transaksi selesai
            ->whereHas('bookings', function($query) {
                $query->where('status', 'selesai'); // Filter status bookings selesai
            })
            ->get();
    
        // Format data sesuai kebutuhan
        $formattedData = $data->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->user->name,
                'kontrakan' => $item->house_id,
                'masuk' => $item->bookings->start_date,
                'keluar' => $item->bookings->end_date,
                'pembayaran' => $item->payment,
            ];
        });
    
        return ResponseFormatter::success(
            $formattedData,
            'Data berhasil diambil'
        );
    }

    public function cekStatusPenyewa()
    {
        $data = Transaction::with(['user', 'bookings'])
        ->where('status', 'selesai') // Filter status transaksi selesai
        ->whereHas('bookings', function($query) {
            $query->where('status', 'selesai'); // Filter status bookings selesai
        })
        ->count();

        return ResponseFormatter::success(
            $data,
            'Data berhasil diambil'
        );
    }
}