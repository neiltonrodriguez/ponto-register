<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeClock;

class TimeClockService
{
    public function getPaginatedTimeClocks(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $bindings = [];
        $where = "WHERE u.role = 'employee'";

        if ($request->filled('employee_id')) {
            $where .= " AND u.id = ?";
            $bindings[] = $request->employee_id;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $where .= " AND tc.clocked_at BETWEEN ? AND ?";
            $bindings[] = $request->start_date . ' 00:00:00';
            $bindings[] = $request->end_date . ' 23:59:59';
        }

        $sql = "
            SELECT
                tc.id AS time_clock_id,
                u.name AS employee_name,
                u.position AS position,
                u.birth_date,
                a.name AS manager_name,
                tc.clocked_at
            FROM time_clocks tc
            INNER JOIN users u ON u.id = tc.user_id
            LEFT JOIN users a ON a.id = u.admin_id
            $where
            ORDER BY tc.clocked_at DESC
        ";

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $total = count(DB::select($sql, $bindings));
        $sql .= " LIMIT $perPage OFFSET $offset";

        $timeClocks = collect(DB::select($sql, $bindings))->map(function ($item) {
            $item->age = Carbon::parse($item->birth_date)->age;
            return $item;
        });

        return new LengthAwarePaginator(
            $timeClocks,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function getEmployees()
    {
        return DB::table('users')->where('role', 'employee')->get();
    }

    public function getLast30DaysTimeClocks()
    {
        return Auth::user()->timeClocks()
            ->where('clocked_at', '>=', now()->subDays(30))
            ->orderBy('clocked_at', 'desc')
            ->get()
            ->groupBy(fn($tc) => $tc->clocked_at->format('Y-m-d'))
            ->map(function ($group) {
                return $group->sortBy('clocked_at');
            });
    }

    public function clockIn(Request $request): TimeClock
    {
        return TimeClock::create([
            'user_id'   => Auth::id(),
            'clocked_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
