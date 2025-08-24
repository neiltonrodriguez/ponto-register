<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TimeClock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Test Employee 1',
            'email' => 'employee1@example.com',
            'password' => Hash::make('230053'),
            'cpf' => '85205230053',
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

        User::create([
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

        $users = User::where('role', 'employee')->get();

        foreach ($users as $user) {
            for ($i = 1; $i <= 29; $i++) {
                $day = Carbon::now()->subDays($i)->startOfDay();

                $timestamps = [
                    $day->copy()->setHour(8)->setMinute(0),
                    $day->copy()->setHour(12)->setMinute(0),
                    $day->copy()->setHour(13)->setMinute(0),
                    $day->copy()->setHour(17)->setMinute(0),
                ];

                foreach ($timestamps as $time) {
                    TimeClock::factory()->create([
                        'user_id' => $user->id,
                        'clocked_at' => $time,
                    ]);
                }
            }
        }
    }
}
