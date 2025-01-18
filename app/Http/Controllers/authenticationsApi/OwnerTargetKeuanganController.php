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
            'uang' => 'required|regex:/^\d+$/', 
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors()->first(), 422);
        }

        try {
            $ownerResponse = ownerTargetKeuangan::create([
                'user_id' => auth()->id(),
                'total' => $request->total,
                'uang' => $request->uang,
            ]);

            return ResponseFormatter::success($ownerResponse, 'Target Keuangan Berhasil diSimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(), 500);
        }
    }
}