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
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</p>
        </div>

        {{-- Roles --}}
        <a href="{{ route('admin.roles.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.roles.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.roles.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Roles</span>
        </a>

        {{-- Permissions --}}
        <a href="{{ route('admin.permissions.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.permissions.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.permissions.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Permissions</span>
        </a>

        {{-- Users --}}
        <a href="{{ route('admin.users.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.users.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.users.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Users</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
        </div>

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

        {{-- Projects --}}
        <a href="{{ route('admin.projects.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Projects</span>
        </a>

        {{-- Project Teams --}}
        <a href="{{ route('admin.projects.team.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.team.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.team.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Project Teams</span>
        </a>

        {{-- Tasks --}}
        <a href="{{ route('admin.projects.tasks.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.tasks.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.tasks.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Tasks</span>
        </a>

        {{-- Milestones --}}
        <a href="{{ route('admin.milestones.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.milestones.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.milestones.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Milestones</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documents</p>
        </div>

        {{-- Document Categories --}}
        <a href="{{ route('admin.document-categories.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.document-categories.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.document-categories.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Doc Categories</span>
        </a>

        {{-- Documents --}}
        <a href="{{ route('admin.documents.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.documents.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.documents.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Documents</span>
        </a>

        {{-- Discussions --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Discussions</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Resources</p>
        </div>

        {{-- Inventory --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Inventory</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        {{-- Finance --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Finance</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        {{-- Reports --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Reports</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        {{-- Settings --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Settings</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
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
        <a href="{{ route('admin.departments.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.departments.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.departments.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Departments
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</p>
        </div>

        <a href="{{ route('admin.roles.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.roles.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.roles.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Roles
        </a>

        <a href="{{ route('admin.permissions.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.permissions.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.permissions.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Permissions
        </a>

        <a href="{{ route('admin.users.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.users.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.users.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Users
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
        </div>

        <a href="{{ route('admin.clients.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.clients.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.clients.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Clients
        </a>

        <a href="{{ route('admin.projects.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.projects.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Projects
        </a>

        <a href="{{ route('admin.projects.team.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.projects.team.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.team.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Project Teams
        </a>

        <a href="{{ route('admin.projects.tasks.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.tasks.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.tasks.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Tasks</span>
        </a>

        {{-- Milestones --}}
        <a href="{{ route('admin.milestones.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.milestones.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.milestones.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Milestones</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documents</p>
        </div>

        <a href="{{ route('admin.document-categories.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.document-categories.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.document-categories.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Doc Categories</span>
        </a>

        <a href="{{ route('admin.documents.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.documents.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.documents.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="ml-3 whitespace-nowrap">Documents</span>
        </a>

        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="ml-3 whitespace-nowrap">Discussions</span>
            </div>
            <span class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>
    </nav>
</aside>
