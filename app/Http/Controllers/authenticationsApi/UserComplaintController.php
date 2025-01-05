<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\UserComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class UserComplaintController extends Controller
{
    /**
     * Display a listing of complaints.
     */
    public function index()
    {
        try {
            $complaints = UserComplaint::where('user_id', auth()->id())->get();
            return ResponseFormatter::success($complaints, 'Daftar keluhan berhasil diambil');
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
            'transaksi_detail_id' => 'required|exists:user_transactions_details_houses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $pengguna = auth()->id();
            $complaint = UserComplaint::create([
                'user_id' => $pengguna,
                'transaksi_detail_id' => $request->transaksi_detail_id,
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
     * Display the specified complaint.
     */
    public function show($id)
    {
        try {
            $complaint = UserComplaint::where('user_id', auth()->id())->find($id);

            if (!$complaint) {
                return ResponseFormatter::error(null, 'Keluhan tidak ditemukan', 404);
            }

            return ResponseFormatter::success($complaint, 'Detail keluhan berhasil diambil');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal mengambil detail keluhan', 500);
        }
    }

    /**
     * Update the specified complaint.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed,rejected',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $complaint = UserComplaint::where('user_id', auth()->id())->find($id);

            if (!$complaint) {
                return ResponseFormatter::error(null, 'Keluhan tidak ditemukan', 404);
            }

            $complaint->update($request->only(['title', 'description', 'status']));

            return ResponseFormatter::success($complaint, 'Keluhan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Gagal memperbarui keluhan', 500);
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
