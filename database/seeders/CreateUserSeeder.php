<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            [
               'name'=>'Alma Muhamad Apriana',
               'username'=>'pengguna',
               'email'=>'alma@gmail.com',
               'role'=> 0,
               'password'=> bcrypt('Batalkan86@'),
            ],
            [
                'name'=>'salaki Muhamad Apriana',
                'username'=>'pengguna',
                'email'=>'salaki@gmail.com',
                'role'=> 0,
                'password'=> bcrypt('Batalkan86@'),
             ],
            [
                'name'=>'Kaka Muhamad Apriana',
                'username'=>'pemilik',
                'email'=>'kaka@gmail.com',
                'role'=> 1,
                'password'=> bcrypt('Batalkan86@'),
            ],
            [
                'name'=>'Kilua Muhamad Apriana',
                'username'=>'pemilik',
                'email'=>'Kilua@gmail.com',
                'role'=> 1,
                'password'=> bcrypt('Batalkan86@'),
            ],
            [
                'name'=>'Admin Muhamad Apriana',
                'username'=>'admin',
                'email'=>'admin@gmail.com',
                'role'=> 2,
                'password'=> bcrypt('Batalkan86@'),
            ],
            
        ];
    
        foreach ($users as $key => $user) 
        {
            User::create($user);
        }
    }
}