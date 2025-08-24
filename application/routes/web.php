<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\TimeClockController;
use App\Http\Controllers\Admin\UtilityController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    Route::middleware('check.employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::post('/clock-in', [EmployeeController::class, 'clockIn'])->name('clock-in');
    });
    
    Route::middleware('check.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/employees', [AdminEmployeeController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [AdminEmployeeController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees', [AdminEmployeeController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [AdminEmployeeController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/employees/{employee}', [AdminEmployeeController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{employee}', [AdminEmployeeController::class, 'destroyEmployee'])->name('employees.destroy');
        Route::post('/employees/{employee}/reset-password', [AdminEmployeeController::class, 'resetEmployeePassword'])->name('employees.reset-password');

        Route::get('/search-zip-code', [UtilityController::class, 'searchZipCode'])->name('search-zip-code');
        
        Route::get('/time-clocks', [TimeClockController::class, 'index'])->name('time-clocks');
    });
});