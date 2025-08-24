<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TimeClockService;

class EmployeeController extends Controller
{
    protected $timeClockService;

    public function __construct(TimeClockService $timeClockService)
    {
        $this->timeClockService = $timeClockService;
    }

    public function dashboard()
    {
        $timeClocksByDay = $this->timeClockService->getLast30DaysTimeClocks();

        return view('employee.dashboard', compact('timeClocksByDay'));
    }

    public function clockIn(Request $request)
    {
        $timeClock = $this->timeClockService->clockIn($request);

        if (!$timeClock) {
            return redirect()->back()->with('error', 'Você já bateu o ponto 4 vezes hoje.');
        }

        return redirect()->back()->with('success', 'Ponto registrado com sucesso!');
    }
}
