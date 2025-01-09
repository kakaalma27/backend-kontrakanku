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
            'name' => 'Kakaalma Muhamad Apriana',
            'phone' => '0851564591910',
            'alamat' => 'Jl Awipari Rt 05 Rw 07.',
            'detail' => '',
            'category' => 'kontrakan', 
            'user_id' => 1,
        ]);
        Address::create([
            'name' => 'Kaka Muhamad Apriana',
            'phone' => '0851564591910',
            'alamat' => 'Jl Awipari Rt 05 Rw 07.',
            'detail' => '',
            'category' => 'kontrakan', 
            'user_id' => 2,
        ]);
    }
}