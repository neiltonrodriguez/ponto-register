@extends('layouts.app')

@section('title', 'Time Clock Reports')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Time Clock Reports</h1>
            <p class="mt-2 text-sm text-gray-700">View and filter employee time clock records.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.time-clocks') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select name="employee_id" id="employee_id"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date"
                    value="{{ request('start_date') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date"
                    value="{{ request('end_date') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($timeClocks->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($timeClocks as $timeClock)
            <li class="px-6 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 items-center">
                    <div class="text-sm font-medium text-gray-900">{{ $timeClock->time_clock_id }}</div>
                    <div class="text-sm text-gray-900">{{ $timeClock->employee_name }}</div>
                    <div class="text-sm text-gray-500">{{ $timeClock->position }}</div>
                    <div class="text-sm text-gray-500">{{ $timeClock->age }} years</div>
                    <div class="text-sm text-gray-500">{{ $timeClock->manager_name ?? '-' }}</div>
                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($timeClock->clocked_at)->format('d/m/Y H:i:s') }}</div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No time clocks found</h3>
            <p class="mt-1 text-sm text-gray-500">No time clock records match your current filters.</p>
        </div>
        @endif
    </div>

    @if($timeClocks->hasPages())
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        {{ $timeClocks->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection