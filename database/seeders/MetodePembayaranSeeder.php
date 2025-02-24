<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MetodePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('metode_pembayarans')->insert([
            ['nama' => 'DANA', 'tipe' => 'EWALLET'],
            ['nama' => 'OVO', 'tipe' => 'EWALLET'],
            ['nama' => 'GOPAY', 'tipe' => 'EWALLET'],
            ['nama' => 'BRI', 'tipe' => 'BANK'],
            ['nama' => 'BCA', 'tipe' => 'BANK'],
            ['nama' => 'MANDIRI', 'tipe' => 'BANK'],
            ['nama' => 'BSI', 'tipe' => 'BANK'],
            ['nama' => 'TUNAI', 'tipe' => 'CASH'],

        ]);
    }
}
