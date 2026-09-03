@php
    $sidebarNav = config('sidebar');
    
    $initialActiveGroup = null;
    if ($sidebarNav) {
        foreach ($sidebarNav as $groupName => $items) {
            foreach ($items as $item) {
                if (request()->routeIs($item['active'])) {
                    $initialActiveGroup = $groupName;
                    break 2;
                }
            }
        }
    }

    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
        'target' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'funnel' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'folder' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
        'check-list' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        'flag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>',
        'team' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        'cart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>',
        'truck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'box' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'warehouse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>',
        'adjust' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>',
        'arrows' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'tool' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L5.121 5.121m0 0A3 3 0 005 9.354M5.121 5.12A3 3 0 019.354 5M14.121 14.121A3 3 0 0018.354 19M14.121 14.121A3 3 0 0119 18.354"/>',
        'swap' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>',
        'tag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
        'wallet' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'receipt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>',
        'credit-card' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        'banknote' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'minus-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'list' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        'file' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
        'folder-open' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>',
        'bar-chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
        'video' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
        'check-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'megaphone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'help' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'office' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'sitemap' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM9 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2zM12 7v4m0 2v4M7 7v4h10V7"/>',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'git-branch' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 3v12m0 0a3 3 0 103 3m-3-3a3 3 0 01-3 3m12-6a3 3 0 100-6 3 3 0 000 6zm0 0l-6 6"/>',
        'globe' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    ];

    $groupIcons = [
        'Dashboard' => 'home',
        'Sales' => 'target',
        'Projects' => 'folder',
        'Materials' => 'cart',
        'Inventory' => 'box',
        'Equipment' => 'wrench',
        'Finance' => 'wallet',
        'Documents' => 'file',
        'Workspace' => 'clock',
        'Administration' => 'shield',
    ];
@endphp

<aside
    :class="{
        'translate-x-0': mobileMenuOpen,
        '-translate-x-full': !mobileMenuOpen,
        'lg:translate-x-0 lg:w-[260px]': sidebarOpen,
        'lg:translate-x-0 lg:w-[68px]': !sidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-50 w-[260px] bg-sidebar text-white transition-all duration-300 ease-in-out flex flex-col overflow-hidden"
>
    {{-- Header / Logo --}}
    <div class="flex items-center h-16 px-4 border-b border-white/10 flex-shrink-0"
         :class="sidebarOpen ? 'justify-between' : 'justify-center'">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/logo.png') }}" alt="{{ system_name() }}" class="h-8 w-8 flex-shrink-0 object-contain">
            <span class="text-sm font-bold text-white tracking-tight truncate transition-opacity duration-200"
                  :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 overflow-hidden lg:hidden'">
                {{ system_name() }}
            </span>
        </div>
        <button @click="mobileMenuOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-thumb]:bg-white/10 [&::-webkit-scrollbar-thumb]:rounded-full"
         :class="sidebarOpen ? 'px-3' : 'px-2'"
         x-data="{ openGroup: '{{ $initialActiveGroup }}' }">
        
        @if($sidebarNav)
            @foreach($sidebarNav as $groupName => $items)
                @php
                    $canSeeGroup = false;
                    foreach($items as $item) {
                        if(isset($item['permission']) && isset($item['model'])) {
                            if(auth()->user()->can($item['permission'], $item['model'])) {
                                $canSeeGroup = true; break;
                            }
                        } elseif(isset($item['permission'])) {
                            if(auth()->user()->can($item['permission'])) {
                                $canSeeGroup = true; break;
                            }
                        } elseif(isset($item['model'])) {
                            if(auth()->user()->can('viewAny', $item['model'])) {
                                $canSeeGroup = true; break;
                            }
                        } else {
                            $canSeeGroup = true; break;
                        }
                    }
                @endphp
                
                @if($canSeeGroup)
                    @if($groupName === 'Dashboard')
                        {{-- Dashboard items at top --}}
                        <div class="mb-1">
                            @foreach($items as $item)
                                @php
                                    $canView = true;
                                    if(isset($item['permission']) && isset($item['model'])) {
                                        $canView = auth()->user()->can($item['permission'], $item['model']);
                                    } elseif(isset($item['permission'])) {
                                        $canView = auth()->user()->can($item['permission']);
                                    } elseif(isset($item['model'])) {
                                        $canView = auth()->user()->can('viewAny', $item['model']);
                                    }
                                    $isActive = request()->routeIs($item['active']);
                                    $iconKey = $item['icon'] ?? 'folder';
                                    $iconSvg = $icons[$iconKey] ?? $icons['folder'];
                                @endphp
                                @if($canView)
                                    <a href="{{ route($item['route']) }}"
                                       title="{{ $item['label'] }}"
                                       @class([
                                           'flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-200',
                                           'bg-sidebar-active text-white shadow-sm shadow-blue-500/20' => $isActive,
                                           'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !$isActive,
                                       ])>
                                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                                        <span class="truncate transition-opacity duration-200"
                                              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden lg:hidden'">{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <div class="my-2 border-t border-white/10"
                             :class="sidebarOpen ? 'mx-3' : 'mx-1'"></div>
                    @else
                        {{-- Collapsible group --}}
                        <div class="mb-0.5">
                            <button 
                                @click="sidebarOpen ? (openGroup = openGroup === '{{ $groupName }}' ? null : '{{ $groupName }}') : (sidebarOpen = true, openGroup = '{{ $groupName }}')"
                                title="{{ $groupName }}"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl transition-all duration-200 group"
                                :class="openGroup === '{{ $groupName }}' && sidebarOpen ? 'bg-sidebar-hover' : 'hover:bg-sidebar-hover'"
                            >
                                @php
                                    $gIcon = $groupIcons[$groupName] ?? 'folder';
                                    $gIconSvg = $icons[$gIcon] ?? $icons['folder'];
                                @endphp
                                <svg class="w-[16px] h-[16px] flex-shrink-0 transition-colors"
                                     :class="openGroup === '{{ $groupName }}' ? 'text-blue-400' : 'text-gray-500 group-hover:text-gray-300'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $gIconSvg !!}</svg>

                                <template x-if="sidebarOpen">
                                    <div class="flex items-center justify-between flex-1 min-w-0">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider transition-colors truncate"
                                              :class="openGroup === '{{ $groupName }}' ? 'text-white' : 'text-gray-500 group-hover:text-gray-300'">
                                            {{ $groupName }}
                                        </span>
                                        <svg class="w-3 h-3 flex-shrink-0 transition-all duration-300"
                                             :class="{
                                                 'rotate-180 text-gray-400': openGroup === '{{ $groupName }}',
                                                 'text-gray-600 group-hover:text-gray-400': openGroup !== '{{ $groupName }}'
                                             }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </template>
                            </button>
                            
                            <div x-show="openGroup === '{{ $groupName }}' && sidebarOpen"
                                 x-collapse
                                 class="mt-0.5 ml-[18px] pl-3 border-l border-white/10 space-y-0.5">
                                @foreach($items as $item)
                                    @php
                                        $canView = true;
                                        if(isset($item['permission']) && isset($item['model'])) {
                                            $canView = auth()->user()->can($item['permission'], $item['model']);
                                        } elseif(isset($item['permission'])) {
                                            $canView = auth()->user()->can($item['permission']);
                                        } elseif(isset($item['model'])) {
                                            $canView = auth()->user()->can('viewAny', $item['model']);
                                        }
                                        $isActive = request()->routeIs($item['active']);
                                        $iconKey = $item['icon'] ?? 'folder';
                                        $iconSvg = $icons[$iconKey] ?? $icons['folder'];
                                    @endphp
                                    @if($canView)
                                        <a href="{{ route($item['route']) }}"
                                           @class([
                                               'flex items-center gap-2.5 px-3 py-[7px] rounded-lg text-[13px] font-medium transition-all duration-200',
                                               'bg-sidebar-active text-white shadow-sm shadow-blue-500/20' => $isActive,
                                               'text-gray-400 hover:bg-sidebar-hover hover:text-white' => !$isActive,
                                           ])>
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                                            <span class="truncate">{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif

        {{-- Settings --}}
        @can('settings.view')
        <div class="mt-2 pt-2 border-t border-white/10">
            <a href="{{ route('admin.settings.index') }}"
               title="Settings"
               @class([
                   'flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-200',
                   'bg-sidebar-active text-white shadow-sm shadow-blue-500/20' => request()->routeIs('admin.settings.*'),
                   'text-gray-400 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.settings.*'),
               ])>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons['settings'] !!}</svg>
                <span class="truncate transition-opacity duration-200"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden lg:hidden'">Settings</span>
            </a>
        </div>
        @endcan
    </nav>
    
    {{-- Collapse Toggle (Desktop only) --}}
    <div class="border-t border-white/10 p-2 hidden lg:block flex-shrink-0">
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="flex items-center justify-center w-full px-3 py-2 rounded-xl text-gray-500 hover:text-white hover:bg-sidebar-hover transition-all duration-200"
        >
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': !sidebarOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>
</aside>
