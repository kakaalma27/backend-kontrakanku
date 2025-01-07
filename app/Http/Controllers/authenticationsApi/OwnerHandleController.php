<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\house;
use Illuminate\Http\Request;
use App\Models\userBookingHouse;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\address;

class OwnerHandleController extends Controller
{
    public function getBooking()
    {
        $user_id = auth()->id();
        $owner_house = house::with('addresses')->where('user_id', operator: $user_id)->pluck('id');
        $user_booking = userBookingHouse::
        return ResponseFormatter::success($owner_house, 'Daftar respon berhasil diambil');

    }
    
    public Function getTransaksi()
    {
        
    }
}