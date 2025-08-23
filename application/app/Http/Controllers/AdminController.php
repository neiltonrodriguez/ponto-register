<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TimeClock;
use App\Rules\ValidateCpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEmployees = User::where('admin_id', Auth::id())->count();
        $todayClocks = TimeClock::whereHas('user', function($query) {
            $query->where('admin_id', Auth::id());
        })->whereDate('clocked_at', today())->count();

        return view('admin.dashboard', compact('totalEmployees', 'todayClocks'));
    }

    public function employees()
    {
        $employees = User::where('admin_id', Auth::id())->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        return view('admin.employees.create');
    }

    public function searchZipCode(Request $request)
    {
        $zipCode = preg_replace('/\D/', '', $request->zip_code);
        
        if (strlen($zipCode) !== 8) {
            return response()->json(['error' => 'Invalid ZIP code'], 400);
        }

        try {
            $response = Http::get("https://viacep.com.br/ws/{$zipCode}/json/");
            
            if ($response->successful() && !isset($response->json()['erro'])) {
                $data = $response->json();
                return response()->json([
                    'address' => $data['logradouro'] ?? '',
                    'neighborhood' => $data['bairro'] ?? '',
                    'city' => $data['localidade'] ?? '',
                    'state' => $data['uf'] ?? ''
                ]);
            }
            
            return response()->json(['error' => 'ZIP code not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error searching ZIP code'], 500);
        }
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', new ValidateCpf, Rule::unique('users')],
            'email' => 'required|string|email|max:255|unique:users',
            'position' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'zip_code' => 'required|string|size:9',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ]);

        $cleanCpf = preg_replace('/\D/', '', $request->cpf);
        $defaultPassword = substr($cleanCpf, -6);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $cleanCpf,
            'position' => $request->position,
            'birth_date' => $request->birth_date,
            'zip_code' => preg_replace('/\D/', '', $request->zip_code),
            'address' => $request->address,
            'number' => $request->number,
            'complement' => $request->complement,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
            'password' => Hash::make($defaultPassword),
            'role' => 'employee',
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee created successfully! Default password: 123456');
    }

    public function editEmployee(User $employee)
    {
        if ($employee->admin_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.employees.edit', compact('employee'));
    }

    public function updateEmployee(Request $request, User $employee)
    {
        if ($employee->admin_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', new ValidateCpf, Rule::unique('users')->ignore($employee->id)],
            'email' => 'required|string|email|max:255|' . Rule::unique('users')->ignore($employee->id),
            'position' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'zip_code' => 'required|string|size:9',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => preg_replace('/\D/', '', $request->cpf),
            'position' => $request->position,
            'birth_date' => $request->birth_date,
            'zip_code' => preg_replace('/\D/', '', $request->zip_code),
            'address' => $request->address,
            'number' => $request->number,
            'complement' => $request->complement,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully!');
    }

    public function destroyEmployee(User $employee)
    {
        if ($employee->admin_id !== Auth::id()) {
            abort(403);
        }

        $employee->delete();
        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully!');
    }

    public function resetEmployeePassword(User $employee)
    {
        if ($employee->admin_id !== Auth::id()) {
            abort(403);
        }

        $cleanCpf = preg_replace('/\D/', '', $employee->cpf);
        $newPassword = substr($cleanCpf, -6);

        $employee->update([
            'password' => Hash::make($newPassword)
        ]);

        return redirect()->route('admin.employees')
            ->with('success', "Password reset successfully! New password: {$newPassword} (last 6 digits of CPF)");
    }


    public function timeClocks(Request $request)
    {
        $query = TimeClock::with('user')
            ->whereHas('user', function($q) {
                $q->where('role', 'employee');
            });

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('clocked_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        if ($request->filled('employee_id')) {
            $query->where('user_id', $request->employee_id);
        }

        $timeClocks = $query->orderBy('clocked_at', 'desc')->paginate(20);
        
        $employees = User::where('role', 'employee')->get();

        return view('admin.time-clocks.index', compact('timeClocks', 'employees'));
    }
}