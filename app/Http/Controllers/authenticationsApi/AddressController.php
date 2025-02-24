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
        try {
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string',
                'alamat' => 'required|string',
            ]);
        
            $user = auth()->user();
        
            $phone = preg_replace('/\D/', '', $request->phone);
        
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . ltrim($phone, '0');
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }
        
            $address = Address::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $phone,
                'alamat' => $request->alamat,
                'detail' => $request->detail,
            ]);
        
            return ResponseFormatter::success($address, 'Alamat berhasil ditambahkan');
        
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return ResponseFormatter::error(null, 'Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Update an existing address.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi data yang diterima
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string',
                'alamat' => 'required|string',
            ]);
    
            // Cari alamat berdasarkan ID
            $address = Address::find($id);
    
            if (!$address) {
                return ResponseFormatter::error(null, 'Alamat tidak ditemukan', 404);
            }
    

            $phone = preg_replace('/\D/', '', $request->phone);
    
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . ltrim($phone, '0');
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }
    
            // Update data alamat
            $address->update([
                'name' => $request->name,
                'phone' => $phone,
                'alamat' => $request->alamat,
                'detail' => $request->detail ?? '',  
            ]);
    
            return ResponseFormatter::success($address, 'Alamat berhasil diperbarui');
        } catch (\Exception $e) {
            // Tangani kesalahan yang mungkin terjadi
            return ResponseFormatter::error(null, 'Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
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