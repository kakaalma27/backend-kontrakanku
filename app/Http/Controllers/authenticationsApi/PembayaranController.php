<?php

namespace App\Http\Controllers\authenticationsApi;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\house;
use App\Models\pembayaran;

class PembayaranController extends Controller
{
    public function pemilik(Request $request)
    {
        $request->validate([
            'metode_pembayaran_id' => 'required|exists:metode_pembayarans,id',
            'nomor_pembayaran' => 'required|string',
        ]);
    
        $pembayaran = Pembayaran::create([
            'owner_id' => auth()->id(), // ID pemilik
            'user_id' => null, // Penyewa tidak ada (null)
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'nomor_pembayaran' => $request->nomor_pembayaran,
            'jumlah' => null, // Jumlah tidak diisi oleh pemilik
            'status' => 'menunggu', // Default status
        ]);
    
        return ResponseFormatter::success($pembayaran, 'Metode Pembayaran Pemilik Berhasil diSimpan');
    }
    public function penyewa(Request $request)
    {
        $request->validate([
            'metode_pembayaran_id' => 'required|exists:metode_pembayarans,id',
            'nomor_pembayaran' => 'required|string',
            'jumlah' => 'required|numeric',
        ]);
    
        // Cari rumah yang disewa
        $rumah = House::find($request->house_id);
        if (!$rumah) {
            return ResponseFormatter::error('Rumah tidak ditemukan', 404);
        }
    
        $pembayaran = Pembayaran::create([
            'user_id' => auth()->id(), // ID penyewa
            'owner_id' => $rumah->user_id, // ID pemilik
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'nomor_pembayaran' => $request->nomor_pembayaran,
            'jumlah' => $request->jumlah, // Jumlah pembayaran
            'status' => 'menunggu', // Default status
        ]);
    
        return ResponseFormatter::success($pembayaran, 'Metode Pembayaran Penyewa Berhasil diSimpan');
    }
}
