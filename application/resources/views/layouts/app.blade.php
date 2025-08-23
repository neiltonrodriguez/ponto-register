<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Time Clock System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @auth
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">Time Clock System</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Hello, {{ auth()->user()->name }}</span>
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <a href="{{ route('admin.employees') }}" class="text-blue-600 hover:text-blue-800">Employees</a>
                        <a href="{{ route('admin.time-clocks') }}" class="text-blue-600 hover:text-blue-800">Time Clocks</a>
                    @else
                        <a href="{{ route('employee.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    @endif
                    
                    <a href="{{ route('change-password') }}" class="text-blue-600 hover:text-blue-800">Change Password</a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="@auth py-8 @endauth">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>