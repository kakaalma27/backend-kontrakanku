<?php

namespace Database\Seeders;

use App\Models\address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'name' => 'Alma Muhamad Apriana',
            'phone' => '0851564591910',
            'alamat' => 'Jl Awipari Rt 05 Rw 07.',
            'detail' => '',
            'category' => 'kontrakan', 
            'user_id' => 1,
            'house_id' => 1
        ]);
    }
}