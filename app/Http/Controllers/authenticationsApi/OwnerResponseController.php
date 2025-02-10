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
    public function index()
    {
        try {
            $userId = auth()->id(); 

            $complaints = UserComplaint::where('user_id', $userId) 
                ->orWhere('owner_id', $userId) 
                ->with(['user', 'ownerResponses']) 
                ->get();

            if ($complaints->isEmpty()) {
                return ResponseFormatter::error(null, 'Tidak ada keluhan ditemukan untuk user ini', 404);
            }

            // Format response
            $formattedComplaints = $complaints->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'user_id' => $complaint->user_id,
                    'owner_id' => $complaint->owner_id,
                    'name' => $complaint->user->name,

                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'status' => $complaint->status,
                    'responses' => $complaint->ownerResponses->map(function ($response) {
                        return [
                            'id' => $response->id,
                            'response' => $response->response,
                            'owner_id' => $response->owner_id,
                            'created_at' => $response->created_at,
                            'updated_at' => $response->updated_at,
                        ];
                    }),
                ];
            });

            return ResponseFormatter::success($formattedComplaints, 'Daftar keluhan dan respon berhasil diambil');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal mengambil daftar keluhan dan respon', 500);
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
    
            $userId = auth()->id(); // Ambil ID pengguna yang sedang login

    
            // Debugging: Cek nilai sebelum menyimpan
            Log::info('Menyimpan respons untuk keluhan ID: ' . $complaintId);
            Log::info('User  ID: ' . $userId);
            $ownerResponse = OwnerResponse::create([
                'complaint_id' => $complaintId,
                'response' => $request->response,
                'user_id' => $userId,
            ]);
            $complaint->update([
                'status' => 'selesai',
                'owner_response' => $request->response,

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