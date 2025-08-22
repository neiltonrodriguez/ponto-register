<?php

namespace App\Http\Controllers;

use App\Models\TimeClock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $recentTimeClocks = Auth::user()->timeClocks()
            ->orderBy('clocked_at', 'desc')
            ->take(10)
            ->get();

        return view('employee.dashboard', compact('recentTimeClocks'));
    }

    public function clockIn(Request $request)
    {
        TimeClock::create([
            'user_id' => Auth::id(),
            'clocked_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Time clocked successfully!');
    }
}