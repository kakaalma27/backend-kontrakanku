<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\UserComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\house;
use App\Models\ownerResponse;
use App\Models\transactionsDetails;
use Illuminate\Support\Facades\Log;

class UserComplaintController extends Controller
{
    /**
     * Display a listing of complaints.
     */
    public function index()
    {
        try {
            $user = auth()->id();
            
            $cekUserHouse = transactionsDetails::where('user_id', $user)->first(); 
    
            if ($cekUserHouse) {
                // Check if the house exists before attempting to get addresses
                $house = house::find($cekUserHouse->house_id);
                
                if ($house) {
                    $address = $house->addresses()->select('user_id', 'name')->first();
                    
                    $complaints = UserComplaint::where('user_id', $user)->get();
                    
                    $data = [
                        'user_id' => $address->user_id,
                        'name' => $address->name,
                        'penyewa' => $complaints
                    ];
    
                    return ResponseFormatter::success($data, 'Data Pemilik Kontrakan');
                } else {
                    return ResponseFormatter::error(null, 'Rumah tidak ditemukan', 404);
                }
            } else {
                $cekUserHouse = transactionsDetails::where('user_id', $user);
    
                if ($cekUserHouse->exists()) {
                    $complaints = UserComplaint::where('user_id', $user)->get();
                    return ResponseFormatter::success($complaints, 'Daftar keluhan berhasil diambil');
                } else {
                    return ResponseFormatter::error(null, 'Tidak dapat mengirim keluhan', 500);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal mengambil daftar keluhan', 500);
        }
    }
    
    
    
    

    /**
     * Store a newly created complaint.
     */
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
            $pengguna = auth()->id();
    
            // Cari transaksi untuk mendapatkan house_id dan user_id pemilik
            $cekUserHouse = transactionsDetails::where('user_id', $pengguna)->first();
    
            if (!$cekUserHouse) {
                return ResponseFormatter::error(null, 'Tidak ada transaksi terkait dengan pengguna ini', 500);
            }
    
            // Ambil user_id pemilik kontrakan dari alamat rumah
            $owner_id = house::find($cekUserHouse->house_id)->addresses()->select('user_id')->first()->user_id;
    
            // Buat keluhan baru
            $complaint = UserComplaint::create([
                'user_id' => $pengguna, // Penyewa
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'pending',
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