<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TimeClock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employee1 = User::create([
            'name' => 'Test Employee',
            'email' => 'employee1@example.com',
            'password' => Hash::make('230053'),
            'cpf' => '852.052.300-53',
            'position' => 'Intern',
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

        $employee2 = User::create([
            'name' => 'Test Employee 2',
            'email' => 'employee2@example.com',
            'password' => Hash::make('663001'),
            'cpf' => '38718663001',
            'position' => 'Analyst',
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

        TimeClock::factory()->count(10)->create(['user_id' => $employee1->id]);
        TimeClock::factory()->count(10)->create(['user_id' => $employee2->id]);
    }
}