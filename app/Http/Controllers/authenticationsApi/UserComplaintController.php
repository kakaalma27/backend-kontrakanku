<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\UserComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\house;
use App\Models\ownerResponse;
use App\Models\transaction;
use App\Models\transactionsDetails;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserComplaintController extends Controller
{
    /**
     * Display a listing of complaints.
     */
    public function index()
    {
        $user_id = auth()->id();
    
        if (!$user_id) {
            return ResponseFormatter::error(null, 'User not authenticated', 401);
        }
    
        // Ambil data pemilik rumah (developer) dengan role 2
        $developers = User::where('role', 2)->select('id', 'name')->get();
    
        // Ambil data addresses yang berhubungan dengan transaksi dan booking selesai
        $addresses = House::whereHas('bookings', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)
                      ->where('status', 'selesai');
            })
            ->whereHas('transactions', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)
                      ->where('status', 'selesai');
            })
            ->with(['addresses' => function ($query) {
                $query->select('id', 'user_id', 'name');
            }])
            ->get()
            ->pluck('addresses') // Mengambil hanya bagian addresses
            ->flatten() // Meratakan array nested
            ->unique('user_id') // Hindari duplikasi berdasarkan user_id
            ->values(); // Reset indeks array
    
        // Jika ada addresses, gabungkan dengan data developers
        if ($addresses->isNotEmpty()) {
            // Format addresses untuk menampilkan user_id sebagai id
            $formattedAddresses = $addresses->map(function ($address) {
                return [
                    'id' => $address['user_id'], // Gunakan user_id sebagai id
                    'name' => $address['name'],
                ];
            });
    
            $result = $formattedAddresses->merge($developers);
        } else {
            // Jika tidak ada addresses, gunakan data developers sebagai default
            $result = $developers;
        }
    
        // Filter hasil supaya hanya id dan name yang ditampilkan
        $result = $result->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        });
    
        return ResponseFormatter::success($result->values()->all(), 'Data successfully retrieved');
    }
    
    /**
     * Store a newly created complaint.
     */
    public function store(Request $request)
    {
        try {
            $pengguna = auth()->id(); // ID user yang sedang login (penyewa)
            $title = $request->input('title');
            $description = $request->input('description');
    
            // Ambil house_id dari tabel transactions berdasarkan user_id (penyewa)
            $house_ids = transaction::where('user_id', $pengguna)->pluck('house_id');
    
            // Jika penyewa tidak memiliki transaksi (house_ids kosong), kembalikan error
            if ($house_ids->isEmpty()) {
                return ResponseFormatter::error(null, 'Penyewa tidak memiliki transaksi terkait rumah', 404);
            }
    
            // Ambil owner_id dari tabel houses berdasarkan house_id
            $owner_house = house::whereIn('id', $house_ids)->value('user_id');
    
            // Jika owner_house tidak ditemukan, kembalikan error
            if (!$owner_house) {
                return ResponseFormatter::error(null, 'Pemilik rumah tidak ditemukan', 404);
            }
    
            // Buat keluhan
            $complaint = UserComplaint::create([
                'user_id' => $pengguna, // Penyewa
                'owner_id' => $owner_house, // Pemilik rumah
                'title' => $title,
                'description' => $description,
                'status' => 'menunggu', // Sesuaikan dengan enum di migration
            ]);
    
            return ResponseFormatter::success($complaint, 'Keluhan berhasil dibuat');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan keluhan', 500);
        }
    }
    
    
    


    /**
     * Remove the specified complaint.
     */
    public function destroy($id)
    {
        try {
            $complaint = UserComplaint::where('user_id', auth()->id())->find($id);

            if (!$complaint) {
                return ResponseFormatter::error(null, 'Keluhan tidak ditemukan', 404);
            }

            $complaint->delete();

            return ResponseFormatter::success(null, 'Keluhan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal menghapus keluhan', 500);
        }
    }
}