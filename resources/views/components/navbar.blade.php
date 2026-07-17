{{-- Top Navbar Component --}}
<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        {{-- Left: Mobile menu + Search --}}
        <div class="flex items-center gap-4">
            {{-- Mobile Menu Toggle --}}
            <button
                @click="mobileMenuOpen = true"
                class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Search Trigger --}}
            <div class="hidden sm:block relative">
                <button type="button" @click="$dispatch('open-search')" class="flex items-center gap-2 pl-3 pr-4 py-2 w-64 text-sm bg-gray-100 text-gray-500 border-0 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Search...</span>
                    <span class="ml-auto text-xs text-gray-400 font-semibold border border-gray-300 rounded px-1.5 py-0.5 shadow-sm">Ctrl K</span>
                </button>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-3">
            {{-- Timer --}}
            <x-timer />

            {{-- Notifications --}}
            @can('notification.view')
                <x-notification-bell />
            @endcan

            {{-- User Dropdown --}}
            @auth
                <x-dropdown align="right">
                    <x-slot:trigger>
                        <button class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                {{ auth()->user()->initials }}
                            </div>
                        </button>
                    </x-slot:trigger>
                    <x-slot:content>
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </span>
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </span>
                        </a>
                        <div class="border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </span>
                                </button>
                            </form>
                        </div>
                    </x-slot:content>
                </x-dropdown>
            @endauth
        </div>
    </div>
</header>

{{-- Global Search Component --}}
<x-global-search />
