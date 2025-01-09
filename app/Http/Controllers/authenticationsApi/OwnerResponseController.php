<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\OwnerResponse;
use App\Models\UserComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class OwnerResponseController extends Controller
{
    /**
     * Display a listing of responses for a specific complaint.
     */
    public function index($complaintId)
    {
        try {
            $complaint = UserComplaint::find($complaintId);

            if (!$complaint) {
                return ResponseFormatter::error(null, 'Keluhan tidak ditemukan', 404);
            }

            $responses = OwnerResponse::where('complaint_id', $complaintId)->get();
            return ResponseFormatter::success($responses, 'Daftar respon berhasil diambil');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal mengambil daftar respon', 500);
        }
    }

    /**
     * Store a newly created response.
     */
    public function store(Request $request, $complaintId)
    {
        $validator = Validator::make($request->all(), [
            'response' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }
    
        try {
            $complaint = UserComplaint::find($complaintId);
    
            if (!$complaint) {
                return ResponseFormatter::error(null, 'Keluhan tidak ditemukan', 404);
            }
    
            $user = auth()->user();
            $houseOwnerId = $complaint->transaksiDetail->house->owner_id;  // Cek pemilik rumah
    
            if ($user->id != $houseOwnerId) {
                return ResponseFormatter::error(null, 'Anda bukan pemilik properti ini', 403);
            }
    
            $ownerResponse = OwnerResponse::create([
                'complaint_id' => $complaintId,
                'response' => $request->response,
                'owner_id' => $user->id,  // ID pemilik rumah
            ]);
    
            return ResponseFormatter::success($ownerResponse, 'Respon berhasil dikirim');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan respon', 500);
        }
    }
    
    /**
     * Display the specified response.
     */
    public function show($complaintId, $id)
    {
        try {
            $response = OwnerResponse::where('complaint_id', $complaintId)->find($id);

            if (!$response) {
                return ResponseFormatter::error(null, 'Respon tidak ditemukan', 404);
            }

            return ResponseFormatter::success($response, 'Detail respon berhasil diambil');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal mengambil detail respon', 500);
        }
    }

    /**
     * Update the specified response.
     */
    public function update(Request $request, $complaintId, $id)
    {
        $validator = Validator::make($request->all(), [
            'response' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $response = OwnerResponse::where('complaint_id', $complaintId)->find($id);

            if (!$response) {
                return ResponseFormatter::error(null, 'Respon tidak ditemukan', 404);
            }

            // Cek apakah pengguna adalah pemilik yang sama
            if ($response->owner_id != auth()->id()) {
                return ResponseFormatter::error(null, 'Anda tidak memiliki izin untuk memperbarui respon ini', 403);
            }

            $response->update([
                'response' => $request->response,
            ]);

            return ResponseFormatter::success($response, 'Respon berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal memperbarui respon', 500);
        }
    }

    /**
     * Remove the specified response.
     */
    public function destroy($complaintId, $id)
    {
        try {
            $response = OwnerResponse::where('complaint_id', $complaintId)->find($id);

            if (!$response) {
                return ResponseFormatter::error(null, 'Respon tidak ditemukan', 404);
            }

            // Cek apakah pengguna adalah pemilik yang sama
            if ($response->owner_id != auth()->id()) {
                return ResponseFormatter::error(null, 'Anda tidak memiliki izin untuk menghapus respon ini', 403);
            }

            $response->delete();

            return ResponseFormatter::success(null, 'Respon berhasil dihapus');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal menghapus respon', 500);
        }
    }
}