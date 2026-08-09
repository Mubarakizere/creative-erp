{{-- Top Navbar Component --}}
<header class="sticky top-0 z-20 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        {{-- Left: Mobile menu + Search --}}
        <div class="flex items-center gap-4 flex-1">
            {{-- Mobile Menu Toggle --}}
            <button
                @click="mobileMenuOpen = true"
                class="lg:hidden text-gray-500 hover:text-gray-900 transition-colors focus:outline-none"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Desktop Sidebar Toggle (Visible when sidebar is closed) --}}
            <button
                x-cloak
                x-show="!sidebarOpen"
                @click="sidebarOpen = true"
                class="hidden lg:block text-gray-500 hover:text-gray-900 transition-colors focus:outline-none"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Search Trigger --}}
            <div class="hidden sm:block flex-1 max-w-lg ml-4 lg:ml-0">
                <button type="button" @click="$dispatch('open-search')" class="flex items-center w-full px-4 py-2.5 bg-gray-50 text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="ml-3 text-sm">Search projects, tasks, or members...</span>
                    <div class="ml-auto flex items-center gap-1">
                        <kbd class="hidden md:inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded shadow-sm">Ctrl</kbd>
                        <span class="hidden md:inline text-gray-400 text-xs">+</span>
                        <kbd class="hidden md:inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded shadow-sm">K</kbd>
                    </div>
                </button>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-4 ml-4">
            {{-- Mobile Search Trigger --}}
            <button type="button" @click="$dispatch('open-search')" class="sm:hidden text-gray-500 hover:text-gray-900 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            {{-- Timer --}}
            <x-timer />

            {{-- Notifications --}}
            @can('notification.view')
                <x-notification-bell />
            @endcan

            {{-- User Dropdown --}}
            @auth
                <div class="h-8 w-px bg-gray-200 mx-2 hidden sm:block"></div>
                <x-dropdown align="right">
                    <x-slot:trigger>
                        <button class="flex items-center gap-3 p-1 rounded-full hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <div class="hidden sm:block text-right mr-1">
                                <p class="text-sm font-semibold text-gray-900 leading-none">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->roles->pluck('name')->filter(fn($name) => $name !== 'Employee')->first() ?? auth()->user()->roles->first()?->name ?? 'User' }}</p>
                            </div>
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->full_name }}" class="w-10 h-10 rounded-full object-cover shadow-sm ring-2 ring-white">
                            @else
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm ring-2 ring-white">
                                    {{ auth()->user()->initials }}
                                </div>
                            @endif
                        </button>
                    </x-slot:trigger>
                    <x-slot:content>
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('admin.profile.edit') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-red-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign out
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
