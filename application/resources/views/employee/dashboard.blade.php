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
        
        @if($recentTimeClocks->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($recentTimeClocks as $timeClock)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $timeClock->clocked_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $timeClock->clocked_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-4 py-5 sm:px-6 text-center">
                <p class="text-gray-500">No time clock records found.</p>
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