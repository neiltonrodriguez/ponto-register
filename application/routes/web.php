<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
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
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [AdminController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [AdminController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/employees/{employee}', [AdminController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{employee}', [AdminController::class, 'destroyEmployee'])->name('employees.destroy');
        Route::post('/employees/{employee}/reset-password', [AdminController::class, 'resetEmployeePassword'])->name('employees.reset-password');

        Route::get('/search-zip-code', [AdminController::class, 'searchZipCode'])->name('search-zip-code');
        
        Route::get('/time-clocks', [AdminController::class, 'timeClocks'])->name('time-clocks');
    });
});