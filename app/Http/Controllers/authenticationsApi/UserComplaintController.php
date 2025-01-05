<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\transaction;
use App\Models\userComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Models\transactionsDetails;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
class UserComplaintController extends Controller
{
    public function index()
    {
        try {
            $cek = UserComplaint::with('house', 'addresses', 'users')->get();
    
            if ($cek->isEmpty()) {
                return ResponseFormatter::error(null, 'Tidak ada data keluhan yang ditemukan', 404);
            }
    
            $data = $cek->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'description' => $complaint->users, // Pastikan ini adalah data yang ingin Anda tampilkan
                    'house' => $complaint->house,
                    'addresses' => $complaint->addresses,
                ];
            });
    
            return ResponseFormatter::success($data, 'Relasi Terhubung');
        } catch (\Exception $e) {
            // Menyimpan kesalahan ke dalam log
            Log::error('Error fetching user complaints: ' . $e->getMessage());
    
            // Mengembalikan respons error
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat mengambil data keluhan', 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $transactionDetail = transactionsDetails::where('id', $request->transaksi_detail_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$transactionDetail || !$transactionDetail->house_id) {
                return ResponseFormatter::error(null, 'Tidak dapat mengirim keluhan karena transaksi tidak valid atau rumah tidak terdaftar.', 400);
            }

            $complaint = UserComplaint::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'pending', 
            ]);

            return ResponseFormatter::success($complaint, 'Keluhan berhasil dibuat');
        } catch (\Exception $e) {
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan keluhan', 500);
        }
    }
}
