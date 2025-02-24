<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\User;
use App\Models\house;
use App\Models\transaction;
use Illuminate\Http\Request;
use App\Models\ownerResponse;
use App\Models\UserComplaint;
use App\Helpers\ResponseFormatter;
use App\Models\transactionsDetails;
use Illuminate\Support\Facades\Log;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
    
        $developers = User::where('role', 2)->select('id', 'name')->get();
    
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
            ->pluck('addresses') 
            ->flatten()
            ->unique('user_id') 
            ->values(); 
    
        if ($addresses->isNotEmpty()) {
            $formattedAddresses = $addresses->map(function ($address) {
                return [
                    'id' => $address['user_id'], 
                    'name' => $address['name'],
                ];
            });
    
            $result = $formattedAddresses->merge($developers);
        } else {
            $result = $developers;
        }
    
        $result = $result->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        });
    
        return ResponseFormatter::success($result->values()->all(), 'Data successfully retrieved');
    }
    public function getUserComplain(Request $request)
    {
        $user_id = auth()->id();
        $data = UserComplaint::where('user_id', $user_id)->get();
        return ResponseFormatter::success($data, 'Keluhan berhasil di Ambil');

    }
    /**
     * Store a newly created complaint.
     */
    public function store(Request $request)
    {
        try {
            $pengguna = auth()->id(); 
            $title = $request->input('title');
            $description = $request->input('description');
    
            $house_ids = transaction::where('user_id', $pengguna)->pluck('house_id');
    
            if ($house_ids->isEmpty()) {
                return ResponseFormatter::error(null, 'Penyewa tidak memiliki transaksi terkait rumah', 404);
            }
    
            $owner_house = house::whereIn('id', $house_ids)->value('user_id');
    
            if (!$owner_house) {
                return ResponseFormatter::error(null, 'Pemilik rumah tidak ditemukan', 404);
            }
    
            $complaint = UserComplaint::create([
                'user_id' => $pengguna,
                'owner_id' => $owner_house,
                'title' => $title,
                'description' => $description,
                'status' => 'menunggu',
            ]);
            $tenantId = $complaint->user_id;
            event(new NewNotificationEvent($tenantId, "Booking Diterima", "Selamat, booking Anda telah disetujui."));
            
            return ResponseFormatter::success($complaint, 'Keluhan berhasil dibuat');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan keluhan', 500);
        }
    }

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

    public function keluhanCount(Request $request)
    {
        $user_id = auth()->id();
        
        // Menghitung keluhan yang terkirim
        $terkirim = UserComplaint::where('user_id', $user_id)->count();
        
        // Menghitung keluhan yang menunggu dan selesai
        $menunggu = UserComplaint::where('user_id', $user_id)->where('status', 'menunggu')->count();
        $selesai = UserComplaint::where('user_id', $user_id)->where('status', 'selesai')->count();
    
        // Menyusun data untuk dikembalikan
        $data = [
            'terkirim' => $terkirim,
            'menunggu' => $menunggu,
            'selesai' => $selesai
        ];
        
        return ResponseFormatter::success($data, 'Keluhan berhasil dihitung');
    }


}