<?php

namespace App\Services;

use App\Models\User;
use App\Models\TimeClock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EmployeeService
{
    public function getDashboardStats(): array
    {
        $adminId = Auth::id();

        $totalEmployees = User::where('admin_id', $adminId)->count();

        $todayClocks = TimeClock::whereHas('user', function ($query) use ($adminId) {
            $query->where('admin_id', $adminId);
        })->whereDate('clocked_at', today())->count();

        return compact('totalEmployees', 'todayClocks');
    }

    public function getPaginatedEmployees()
    {
        return User::where('admin_id', Auth::id())->paginate(10);
    }

    public function create(array $data): User
    {
        $cleanCpf = preg_replace('/\D/', '', $data['cpf']);
        $defaultPassword = substr($cleanCpf, -6);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $cleanCpf,
            'position' => $data['position'],
            'birth_date' => $data['birth_date'],
            'zip_code' => preg_replace('/\D/', '', $data['zip_code']),
            'address' => $data['address'],
            'number' => $data['number'],
            'complement' => $data['complement'],
            'neighborhood' => $data['neighborhood'],
            'city' => $data['city'],
            'state' => $data['state'],
            'password' => Hash::make($defaultPassword),
            'role' => 'employee',
            'admin_id' => Auth::id(),
        ]);
    }

    public function update(User $employee, array $data): bool
    {
        return $employee->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => preg_replace('/\D/', '', $data['cpf']),
            'position' => $data['position'],
            'birth_date' => $data['birth_date'],
            'zip_code' => preg_replace('/\D/', '', $data['zip_code']),
            'address' => $data['address'],
            'number' => $data['number'],
            'complement' => $data['complement'],
            'neighborhood' => $data['neighborhood'],
            'city' => $data['city'],
            'state' => $data['state'],
        ]);
    }

    public function resetPassword(User $employee): string
    {
        $cleanCpf = preg_replace('/\D/', '', $employee->cpf);
        $newPassword = substr($cleanCpf, -6);

        $employee->update([
            'password' => Hash::make($newPassword)
        ]);

        return $newPassword;
    }

    public function getFilteredTimeClocks(array $filters): LengthAwarePaginator
    {
        $query = TimeClock::with(['user:id,name,position,birth_date,admin_id', 'user.admin:id,name'])
            ->join('users', 'users.id', '=', 'time_clocks.user_id')
            ->where('users.role', 'employee')
            ->select('time_clocks.*');

        $query->when($filters['employee_id'] ?? null, function ($q, $employeeId) {
            return $q->where('time_clocks.user_id', $employeeId);
        });

        $query->when(($filters['start_date'] ?? null) && ($filters['end_date'] ?? null), function ($q) use ($filters) {
            return $q->whereBetween('clocked_at', [$filters['start_date'] . ' 00:00:00', $filters['end_date'] . ' 23:59:59']);
        });

        return $query->orderBy('clocked_at', 'desc')->paginate(10);
    }
}
