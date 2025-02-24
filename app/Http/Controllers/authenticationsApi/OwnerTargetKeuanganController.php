<?php

namespace App\Http\Controllers\authenticationsApi;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\ownerTargetKeuangan;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class OwnerTargetKeuanganController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total' => 'required|regex:/^\d+$/', 
            'price' => 'required|regex:/^\d+$/', 
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $existingData = ownerTargetKeuangan::where('user_id', auth()->id())->first();

            if ($existingData) {
                return ResponseFormatter::error(null, 'Hanya boleh ada satu data target keuangan untuk setiap pengguna.', 422);
            }

            $ownerResponse = ownerTargetKeuangan::create([
                'user_id' => auth()->id(),
                'total' => $request->total,
                'price' => $request->price,
            ]);

            return ResponseFormatter::success($ownerResponse, 'Target Keuangan Berhasil diSimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(), 500);
        }
    }

}