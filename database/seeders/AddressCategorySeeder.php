<?php

namespace Database\Seeders;

use App\Models\addressCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        addressCategory::create([
            'user_id' => 3,
            'utama' => 'Utama',
            'kontrakan' => 'Kontrakan',
        ]);
    }
}