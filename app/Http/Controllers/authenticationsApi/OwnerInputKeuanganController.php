<?php

namespace App\Http\Controllers\authenticationsApi;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\OwnerInputKeuangan; // Pastikan nama model sesuai
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class OwnerInputKeuanganController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'metode' => 'required|string',
            'price' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors(), 'Validation Error', 422);
        }

        try {
            $inputKeuangan = OwnerInputKeuangan::create([
                'user_id' => $request->user_id,
                'metode' => $request->metode,
                'price' => $request->uang,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return ResponseFormatter::success($inputKeuangan, 'Data keuangan berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Gagal menyimpan data keuangan', 500);
        }
    }
}