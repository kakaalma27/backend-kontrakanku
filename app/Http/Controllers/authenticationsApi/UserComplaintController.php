<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\userComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Notifications\UserActionNotification;

use Illuminate\Support\Facades\Log;
class UserComplaintController extends Controller
{


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
            \Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan keluhan', 500);
        }
    }
}
