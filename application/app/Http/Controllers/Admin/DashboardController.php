<?php

namespace App\Http\Controllers\Admin;

use App\Services\EmployeeService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $stats = $this->employeeService->getDashboardStats();
        return view('admin.dashboard', $stats);
    }
}
