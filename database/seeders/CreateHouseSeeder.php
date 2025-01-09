<?php

namespace Database\Seeders;

use App\Models\house;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Storage;
class CreateHouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat folder images jika belum ada
        if (!Storage::exists('public/images')) {
            Storage::makeDirectory('public/images');
        }

        $imagePaths = [
            'public/images/1.png',
            'public/images/2.png',
        ];

        foreach ($imagePaths as $imagePath) {
            Storage::put($imagePath, ''); 
        }

        House::create([
            'path' => json_encode($imagePaths),
            'name' => 'Rumah Contoh 1',
            'price' => 450000,
            'description' => 'Rumah nyaman dengan fasilitas lengkap.',
            'tags' => 'nyaman, strategis',
            'kamar' => 3,
            'wc' => 2,
            'quantity' => 1,
            'available' => true,
            'user_id' => 2,
        ]);


        $this->command->info('HouseSeeder berhasil dijalankan!');
    }
}