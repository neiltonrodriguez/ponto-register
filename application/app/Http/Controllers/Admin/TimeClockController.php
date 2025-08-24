<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TimeClockService;
use Illuminate\Http\Request;

class TimeClockController extends Controller
{
    protected $timeClockService;

    public function __construct(TimeClockService $timeClockService)
    {
        $this->timeClockService = $timeClockService;
    }

    public function index(Request $request)
    {
        $timeClocks = $this->timeClockService->getPaginatedTimeClocks($request);
        $employees = $this->timeClockService->getEmployees();

        return view('admin.time-clocks.index', compact('timeClocks', 'employees'));
    }
}
