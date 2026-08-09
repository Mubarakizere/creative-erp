<x-layouts.app :title="$title ?? 'Client Portal'">
    <div class="min-h-screen flex flex-col bg-gray-50">

        {{-- Client Top Navigation --}}
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        {{-- Logo --}}
                        <div class="flex-shrink-0 flex items-center">
                            <img src="{{ asset('images/logo.png') }}" alt="Creative Century Engineering" class="h-8 w-auto">
                            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Client</span>
                        </div>

                        {{-- Client Nav Links --}}
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-4">
                            <a href="{{ route('customer.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                                Dashboard
                            </a>
                        </div>
                    </div>

                    {{-- User Menu --}}
                    <div class="flex items-center">
                        @auth
                            <x-dropdown>
                                <x-slot:trigger>
                                    <button class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 transition-colors">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                            {{ auth()->user()->initials }}
                                        </div>
                                        <span class="hidden sm:block">{{ auth()->user()->full_name }}</span>
                                    </button>
                                </x-slot:trigger>
                                <x-slot:content>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </x-slot:content>
                            </x-dropdown>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- Flash Messages handled globally in app.blade.php --}}
        {{-- Page Content --}}
        <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-sm text-gray-500 text-center">
                    &copy; {{ date('Y') }} Creative Century Engineering. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</x-layouts.app>
