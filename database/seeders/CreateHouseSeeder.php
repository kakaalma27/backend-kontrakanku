<?php

namespace Database\Seeders;

use App\Models\house;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateHouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengisi tabel houses dengan data dummy
        $houses = [
            [
                'url' => json_encode([
                    'https://46ef-36-74-42-78.ngrok-free.app/storage/images/1.png',
                    'https://46ef-36-74-42-78.ngrok-free.app/storage/images/2.png',
                ]),
                'name' => 'Rumah Minimalis',
                'price' => 450000,
                'description' => 'Rumah minimalis dengan 3 kamar tidur dan 2 kamar mandi.',
                'tags' => 'minimalis, modern',
                'kamar' => 3,
                'wc' => 2,
                'quantity' => 10,
                'available' => true,
                'user_id' => 2, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($houses as $key => $house) 
        {
            house::create($house);
        }
    }
}
