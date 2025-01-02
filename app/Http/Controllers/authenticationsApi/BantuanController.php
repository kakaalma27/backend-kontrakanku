<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\house;
use App\Models\bantuan;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter; 
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;

class BantuanController extends Controller
{
    public function index()
    {
        $users = DB::table('bantuans')
        ->join('users', 'users.id', '=', 'bantuans.user_id')
        ->join('houses', 'houses.id', '=', 'bantuans.house_id') // Pastikan ini benar
        ->get();


        if ($users->isEmpty()) {
            return ResponseFormatter::error(null, 'Data pengguna tidak ditemukan', 404);
        }
        return ResponseFormatter::success($users, 'Data pengguna ditemukan');
    }
    public function store(Request $request)
    {
        $request->validate([
            'house_id' => 'required|exists:houses,id',
            'name' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);
        
        $house = house::find($request->house_id);
        $bantuan = bantuan::create([
            'user_id' => auth()->id(),
            'house_id' => $house->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return ResponseFormatter::success($bantuan, 'Bantuan berhasil diKirim');
    }
}
