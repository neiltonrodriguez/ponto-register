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

        User::create([
            'name' => 'Test Employee',
            'email' => 'employee1@example.com',
            'password' => Hash::make('230053'),
            'cpf' => '852.052.300-53',
            'position' => 'Test Position',
            'birth_date' => '1995-05-15',
            'zip_code' => '01310100',
            'address' => 'Avenida Paulista',
            'number' => '1500',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'role' => 'employee',
            'admin_id' => 1,
        ]);

        User::create([
            'name' => 'Test Employee 2',
            'email' => 'employee2@example.com',
            'password' => Hash::make('663001'),
            'cpf' => '38718663001',
            'position' => 'Test Position',
            'birth_date' => '1975-05-15',
            'zip_code' => '01310100',
            'address' => 'Avenida Paulista',
            'number' => '1500',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'role' => 'employee',
            'admin_id' => 2,
        ]);
    }
}