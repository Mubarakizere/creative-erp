<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ system_name() }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans antialiased text-gray-900 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Optional Illustration Slot -->
        <div class="mb-8 flex justify-center">
            @yield('illustration')
        </div>

        <h1 class="text-6xl font-bold text-gray-900 tracking-tight mb-2">@yield('code')</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">@yield('message')</h2>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">@yield('description')</p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Go Back
            </button>
            
            @auth
                <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Dashboard
                </a>
            @else
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Home Page
                </a>
            @endauth
        </div>
        
        <div class="mt-12 text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ system_name() }}. All rights reserved.
        </div>
    </div>
</body>
</html>
