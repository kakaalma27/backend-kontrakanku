<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;

class AddressController extends Controller
{
    /**
     * Store a newly created address.
     */
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
            return ResponseFormatter::error(null, 'User not found', 404);
        }

        $address = Address::create([
            'user_id' => $user_id,
            'name' => $user->name,
            'phone' => $phone,
            'alamat' => $request->alamat,
            'jalan' => $request->jalan,
            'detail' => $request->detail,
        ]);

        return ResponseFormatter::success($address, 'Alamat berhasil ditambahkan');
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'sometimes|string',
            'alamat' => 'sometimes|string',
            'jalan' => 'sometimes|string',
            'detail' => 'sometimes|string',
        ]);

        $address = Address::find($id);

        if (!$address) {
            return ResponseFormatter::error(null, 'Alamat tidak ditemukan', 404);
        }

        if ($request->has('phone')) {
            $phone = preg_replace('/\D/', '', $request->phone);
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . ltrim($phone, '0');
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }
            $address->phone = $phone;
        }

        $address->update($request->only(['alamat', 'jalan', 'detail']));

        return ResponseFormatter::success($address, 'Alamat berhasil diperbarui');
    }

    /**
     * Delete an address.
     */
    public function destroy($id)
    {
        $address = Address::find($id);

        if (!$address) {
            return ResponseFormatter::error(null, 'Alamat tidak ditemukan', 404);
        }

        $address->delete();

        return ResponseFormatter::success(null, 'Alamat berhasil dihapus');
    }

    /**
     * Get a list of addresses for the authenticated user.
     */
    public function index()
    {
        $user_id = auth()->id();
        $addresses = Address::where('user_id', $user_id)->get();

        return ResponseFormatter::success($addresses, 'Daftar alamat berhasil diambil');
    }

    /**
     * Show a single address.
     */
    public function show($id)
    {
        $address = Address::find($id);

        if (!$address) {
            return ResponseFormatter::error(null, 'Alamat tidak ditemukan', 404);
        }

        return ResponseFormatter::success($address, 'Alamat berhasil diambil');
    }

    public function allAddresses()
{
    $user = auth()->user();
    if (!$user || !$user->is_admin) {
        return ResponseFormatter::error(null, 'Anda tidak memiliki izin untuk melihat semua alamat', 403);
    }
    $addresses = Address::with('user')->get();
    return ResponseFormatter::success($addresses, 'Seluruh alamat berhasil diambil');
}
}
