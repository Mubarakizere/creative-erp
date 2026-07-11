{{-- Admin Sidebar Component --}}
<aside
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="fixed inset-y-0 left-0 z-40 bg-sidebar text-white transition-all duration-300 hidden lg:flex lg:flex-col"
>
    {{-- Logo --}}
    <div class="flex items-center h-16 px-4 border-b border-white/10">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <span x-show="sidebarOpen" x-transition class="ml-3 text-lg font-bold whitespace-nowrap">
            Creative <span class="text-blue-400">ERP</span>
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3 space-y-1">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.dashboard'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.dashboard'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Dashboard</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Organization</p>
        </div>

        {{-- Companies --}}
        <a href="{{ route('admin.companies.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.companies.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.companies.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Companies</span>
        </a>

        {{-- Branches --}}
        <a href="{{ route('admin.branches.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.branches.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.branches.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Branches</span>
        </a>

        {{-- Departments --}}
        <a href="{{ route('admin.departments.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.departments.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.departments.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Departments</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
        </div>

        {{-- Projects --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Projects</span>
        </a>

        {{-- Employees --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Employees</span>
        </a>

        {{-- Clients --}}
        <a href="{{ route('admin.clients.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.clients.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.clients.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Clients</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Resources</p>
        </div>

        {{-- Inventory --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Inventory</span>
        </a>

        {{-- Materials --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Materials</span>
        </a>

        {{-- Equipment --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Equipment</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</p>
        </div>

        {{-- Finance --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Finance</span>
        </a>
    </nav>

    {{-- Collapse Toggle --}}
    <div class="border-t border-white/10 p-3">
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="flex items-center justify-center w-full px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-sidebar-hover transition-all duration-200"
        >
            <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<aside
    x-show="mobileMenuOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white lg:hidden flex flex-col"
    style="display: none;"
>
    {{-- Mobile Logo --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="ml-3 text-lg font-bold">Creative <span class="text-blue-400">ERP</span></span>
        </div>
        <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Navigation (same links) --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.dashboard'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.dashboard'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('admin.companies.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.companies.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.companies.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Companies
        </a>
        <a href="{{ route('admin.branches.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.branches.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.branches.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Branches
        </a>

        {{-- Departments --}}
        <a href="{{ route('admin.departments.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.departments.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.departments.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Departments</span>
        </a>
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Projects
        </a>
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Employees
        </a>
        <a href="{{ route('admin.clients.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.clients.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.clients.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Clients
        </a>
    </nav>
</aside>
