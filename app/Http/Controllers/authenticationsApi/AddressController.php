<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\address;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'alamat' => 'required|string',
            'jalan' => 'required|string',
            'detail' => 'required|string',
        ]);

        $phone = preg_replace('/\D/', '', $request->phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . ltrim($phone, '0'); 
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        $user_id = auth()->id();
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['error' => 'User  not found'], 404);
        }
        $address = address::create([
            'user_id' => $user_id,
            'name' => $user->name,
            'phone' => $phone,
            'alamat' => $request->alamat,
            'jalan' => $request->jalan,
            'detail' => $request->detail,
        ]);
        return ResponseFormatter::success($address, 'Alamat berhasil ditambahkan');
    }
}
