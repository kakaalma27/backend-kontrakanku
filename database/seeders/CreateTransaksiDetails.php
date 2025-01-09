<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\transactionsDetails;
class CreateTransaksiDetails extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        TransactionsDetails::create([
            'user_id' => 1,
            'house_id' => 1,
            'booking_id' => 2,
            'payment_id' => 1,
        ]);


    }
}