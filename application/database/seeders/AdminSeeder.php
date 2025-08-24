<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator 1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('admin123'),
            'cpf' => '74686837006',
            'position' => 'Administrator',
            'birth_date' => '1990-01-01',
            'zip_code' => '01310100',
            'address' => 'Avenida Paulista',
            'number' => '1000',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Administrator 2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('admin456'),
            'cpf' => '87695883093',
            'position' => 'Administrator',
            'birth_date' => '1987-01-01',
            'zip_code' => '01310100',
            'address' => 'Avenida Paulista',
            'number' => '1000',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'role' => 'admin',
        ]);
    }
}