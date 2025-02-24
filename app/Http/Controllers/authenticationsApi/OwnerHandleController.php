<?php

namespace App\Http\Controllers\authenticationsApi;


use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\house;
use App\Models\address;
use App\Models\transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\userComplaint;
use App\Models\userBookingHouse;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\ownerTargetKeuangan;
use Illuminate\Support\Facades\Log;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use PDF;


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
    
            $booking->status = $request->status;
    
            if ($request->status === 'ditolak') {
                $booking->delete();
    
                $house->increment('quantity');
            } else {
                event(new NewNotificationEvent($booking->user_id, "Booking Diterima", "Selamat, booking Anda telah disetujui."));
            }
    
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
    
    public function handleTransaksi(Request $request, $transactionId)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:selesai,ditolak', // Validasi status harus "selesai" atau "ditolak"
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
    
            // Jika statusnya "ditolak"
            if ($validated['status'] === 'ditolak') {
                // Hapus transaksi
                DB::table('transactions_houses')->where('id', $transactionId)->delete();
                Log::info('Transaksi ditolak dan dihapus', ['transactionId' => $transactionId]);
    
                // Kirim notifikasi kepada pengguna
                $tenantId = $transaction->user_id;
                event(new NewNotificationEvent($tenantId, "Transaksi Ditolak", "Maaf, transaksi Anda telah ditolak.", ''));
                
                return ResponseFormatter::success(
                    null,
                    'Transaksi berhasil dihapus karena status ditolak.'
                );
            }
    
            // Jika statusnya "selesai", perbarui status
            DB::table('transactions_houses')
                ->where('id', $transactionId)
                ->update([
                    'status' => $validated['status'], // Perbarui status
                    'updated_at' => now(), // Perbarui waktu
                ]);
    
            $updatedTransaction = DB::table('transactions_houses')->where('id', $transactionId)->first();
            $tenantId = $transaction->user_id;
            event(new NewNotificationEvent($tenantId, "Transaksi Diterima", "Selamat, transaksi Anda telah disetujui."));
    
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
        // Ambil ID user yang sedang login
        $user = auth()->id();
    
        // Ambil semua house_id yang dimiliki oleh user tersebut
        $houses = House::where('user_id', $user)->pluck('id');
    
        // Cari transaksi yang terkait dengan house_id tersebut dan statusnya selesai
        $data = Transaction::with(['user', 'bookings'])
            ->whereIn('house_id', $houses) // Filter berdasarkan house_id yang dimiliki user
            ->where('status', 'selesai')
            ->whereHas('bookings', function($query) {
                $query->where('status', 'selesai');
            })
            ->get();
    
        // Format data yang akan ditampilkan
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
    
        // Kembalikan response
        return ResponseFormatter::success(
            $formattedData,
            'Data berhasil diambil'
        );
    }

    public function cekStatusPenyewa()
    {
        // Ambil ID user yang sedang login
        $user = auth()->id();
    
        // Ambil semua house_id yang dimiliki oleh user tersebut
        $houses = House::where('user_id', $user)->pluck('id');
    
        // Cari transaksi yang terkait dengan house_id tersebut dan statusnya selesai
        $data = Transaction::with(['user', 'bookings'])
            ->whereIn('house_id', $houses) // Filter berdasarkan house_id yang dimiliki user
            ->where('status', 'selesai')
            ->whereHas('bookings', function($query) {
                $query->where('status', 'selesai');
            })
            ->count();

        return ResponseFormatter::success(
            $data,
            'Data berhasil diambil'
        );
    }

    public function uangBulanan()
    {
        $user_id = auth()->id();
        $currentMonth = Carbon::now();
        $targetKeuangan = OwnerTargetKeuangan::where('user_id', $user_id)->first(); 
        $houses = House::where('user_id', $user_id)->pluck('id');
        $results = [];
        
        $bulanSingkatan = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
    
        for ($i = 0; $i < 3; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            
            $totalUangMasuk = transaction::where('status', 'selesai')
                ->whereIn('house_id', $houses)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('price');
    
                if ($totalUangMasuk > 0 && $targetKeuangan && $targetKeuangan->price > 0) {
                    $persentase = ($totalUangMasuk / $targetKeuangan->price) * 100;
                } else {
                    $persentase = 0; 
                }
        
    
            $singkatanBulan = $bulanSingkatan[$month->month];
    
            $results[] = [
                'id' => $i + 1,
                'bulan' => $singkatanBulan,
                'total_uang_masuk' => $totalUangMasuk,
                'persentase' => $persentase,
            ];
        }
    
        $results = array_reverse($results);
    
        return ResponseFormatter::success(
            $results,
            'Data uang bulanan berhasil diambil'
        );
    }

    public function TransaksiBulanan()
    {
        $user_id = auth()->id();
        $houses = House::where('user_id', $user_id)->pluck('id');
        $transaksi = transaction::where('status', 'selesai')
        ->whereIn('house_id', $houses)
        ->get();
    
        $total = 0;
        $detailTransaksi = [];
    
        foreach ($transaksi as $item) {
            $total += $item->price;
    
            $formattedDate = $item->created_at->format('d-M-Y - H:i');
    
            $detailTransaksi[] = [
                'tanggal' => $formattedDate,
                'harga' =>  $item->price,
            ];
        }
    
        $output = [
            'total' => $total,
            'transaksi' => $detailTransaksi,
        ];
    
        return ResponseFormatter::success(
            $output,
            'Data transaksi bulanan berhasil diambil'
        );
    }

    public function StatementKeuangan()
    {
        $user_id = auth()->id();
        $targetUang = ownerTargetKeuangan::where('user_id', $user_id)->pluck('price');
    
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
    
        $transaksi = transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'selesai') 
            ->get();
    
    
        $weeklyTransactions = [];
        $daysInMonth = now()->daysInMonth;
    
        for ($week = 0; $week < ceil($daysInMonth / 7); $week++) {
            $startOfWeek = $startOfMonth->copy()->addWeeks($week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
    
            $transactionsInWeek = $transaksi->filter(function($transaction) use ($startOfWeek, $endOfWeek) {
                return $transaction->created_at >= $startOfWeek && $transaction->created_at <= $endOfWeek;
            });
    
    
            $weeklyTransactions[$week + 1] = [
                'total_transaksi' => $transactionsInWeek->count(),
                'total_uang_masuk' => $transactionsInWeek->sum('price'), // Ganti 'price' dengan nama kolom yang sesuai
            ];
        }
    
        return ResponseFormatter::success(
            [
                'target_keuangan' => $targetUang,
                'weekly_summary' => $weeklyTransactions,
            ],
            'Data StatementKeuangan bulanan berhasil diambil'
        );
    }

    public function pdfStatementKeuangan()
    {
        $user_id = auth()->id();
        $targetUang = ownerTargetKeuangan::where('user_id', $user_id)->pluck('price')->first(); // Get the first value
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
    
        $transaksi = transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'selesai') 
            ->get();
    
        $data = [
            'month' => now()->format('F Y'), // Get the current month and year
            'target_keuangan' => $targetUang,
            'transaksi' => $transaksi,
        ];
        $pdf = PDF::loadView('pdf.statement_keuangan', $data);
        return $pdf->download('statement_keuangan_' . now()->format('F_Y') . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="statement_keuangan_' . now()->format('F_Y') . '.pdf"',
        ]);
    }
    public function generateAuthToken(Request $request)
    {
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');
        $secret = env('PUSHER_APP_SECRET');

        $signature = hash_hmac('sha256', $socketId . ':' . $channelName, $secret);

        return response()->json([
            'auth' => env('PUSHER_APP_KEY') . ':' . $signature,
        ]);
    }

    public function fetchNotifikasi(Request $request, $user_id)
    {
        if (!is_numeric($user_id)) {
            return ResponseFormatter::error(
                null,
                'User ID tidak valid',
                400
            );
        }
        $notifly = Notification::where('user_id', $user_id)->get();
        if ($notifly->isEmpty()) {
            return ResponseFormatter::success(
                [],
                'Tidak ada notifikasi untuk user ini'
            );
        }
    
        return ResponseFormatter::success(
            $notifly,
            'Notifikasi Berhasil diambil'
        );
    }

    public function fetchUserSelesai(Request $request)
    {

    }


}