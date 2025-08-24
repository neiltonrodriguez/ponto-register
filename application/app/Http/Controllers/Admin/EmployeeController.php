<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\EmployeeService;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function employees()
    {
        $employees = $this->employeeService->getPaginatedEmployees();
        return view('admin.employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        $positions = config('employees.positions');
        return view('admin.employees.create', compact('positions'));
    }

    public function storeEmployee(StoreEmployeeRequest $request)
    {
        $this->employeeService->create($request->validated());
        return redirect()->route('admin.employees')->with('success', 'Employee created successfully!');
    }

    public function editEmployee(User $employee)
    {
        $this->authorize('view', $employee);
        $positions = config('employees.positions');
        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function updateEmployee(UpdateEmployeeRequest $request, User $employee)
    {
        $this->employeeService->update($employee, $request->validated());
        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully!');
    }

    public function destroyEmployee(User $employee)
    {
        $this->authorize('delete', $employee);
        $employee->delete();
        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully!');
    }

    public function resetEmployeePassword(User $employee)
    {
        $this->authorize('resetPassword', $employee);
        $newPassword = $this->employeeService->resetPassword($employee);

        return redirect()->route('admin.employees')
            ->with('success', "Password reset successfully! New password: {$newPassword} (last 6 digits of CPF)");
    }
}
