@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Welcome, {{ auth()->user()->name }}!</h3>

            <div class="text-center">
                <form method="POST" action="{{ route('employee.clock-in') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Clock In
                    </button>
                </form>

                <p class="mt-2 text-sm text-gray-500">
                    Current time: <span id="current-time" class="font-mono"></span>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Time Clocks</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Your last 10 time clock records</p>
        </div>

        @if($timeClocksByDay->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($timeClocksByDay as $day => $timeClocks)
            <li class="px-4 py-4 sm:px-6" x-data="{ expanded: false }">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($day)->format('d/m/Y') }}
                    </span>

                    <span class="font-bold">|</span>

                    @foreach($timeClocks->take(8) as $timeClock)
                    <span class="px-2 py-1 text-xs rounded-md bg-green-100 text-green-800 font-mono">
                        {{ $timeClock->clocked_at->format('H:i:s') }}
                    </span>
                    @endforeach

                    @if($timeClocks->count() > 8)
                    <template x-if="!expanded">
                        <button @click="expanded = true"
                            class="px-2 py-1 text-xs rounded-md bg-gray-200 text-gray-600 font-mono hover:bg-gray-300">
                            ...
                        </button>
                    </template>

                    <template x-if="expanded">
                        @foreach($timeClocks->skip(8) as $timeClock)
                        <span class="px-2 py-1 text-xs rounded-md bg-green-100 text-green-800 font-mono">
                            {{ $timeClock->clocked_at->format('H:i:s') }}
                        </span>
                        @endforeach
                    </template>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="px-4 py-5 sm:px-6 text-center">
            <p class="text-gray-500">Nenhuma batida encontrada nos últimos 30 dias.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current-time').textContent = timeString;
    }

    updateTime();
    setInterval(updateTime, 1000);
</script>
@endpush
@endsection