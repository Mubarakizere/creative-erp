@php
    $sidebarNav = config('sidebar');
    
    // Determine the initially active group based on current route
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
@endphp

<aside
    :class="{
        'translate-x-0': mobileMenuOpen,
        '-translate-x-full': !mobileMenuOpen,
        'lg:translate-x-0': sidebarOpen,
        'lg:-translate-x-full': !sidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white transition-transform duration-300 flex flex-col"
>
    {{-- Header / Logo --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center">
            <span class="text-lg font-bold whitespace-nowrap">
                Creative <span class="text-blue-400">MS</span>
            </span>
        </div>
        <button @click="mobileMenuOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
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
                            @endphp
                            @if($canView)
                                <a href="{{ route($item['route']) }}"
                                   @class([
                                       'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group mb-4',
                                       'bg-sidebar-active text-white' => request()->routeIs($item['active']),
                                       'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs($item['active']),
                                   ])>
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span class="ml-3 whitespace-nowrap">{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    @else
                        <div class="pt-2 pb-1">
                            <button 
                                @click="openGroup = openGroup === '{{ $groupName }}' ? null : '{{ $groupName }}'"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors group"
                                :class="openGroup === '{{ $groupName }}' ? 'bg-sidebar-hover' : 'hover:bg-sidebar-hover'"
                            >
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider group-hover:text-white transition-colors"
                                      :class="openGroup === '{{ $groupName }}' ? 'text-white' : ''">
                                    {{ $groupName }}
                                </span>
                                <svg class="w-4 h-4 text-gray-500 group-hover:text-white transition-transform duration-300" 
                                     :class="{'rotate-180': openGroup === '{{ $groupName }}'}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="openGroup === '{{ $groupName }}'"
                                 x-collapse
                                 class="mt-1 space-y-1">
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
                                    @endphp
                                    @if($canView)
                                        <a href="{{ route($item['route']) }}"
                                           @class([
                                               'flex items-center pl-6 pr-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 group',
                                               'bg-sidebar-active text-white' => request()->routeIs($item['active']),
                                               'text-gray-400 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs($item['active']),
                                           ])>
                                            <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif
        
        <div class="pt-4 pb-2">
            <a href="#" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 cursor-not-allowed group">
                <span class="whitespace-nowrap text-xs font-semibold uppercase tracking-wider">Settings</span>
                <span class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
            </a>
        </div>
    </nav>
    
    {{-- Collapse Toggle (Desktop only) --}}
    <div class="border-t border-white/10 p-3 hidden lg:block">
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="flex items-center justify-center w-full px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-sidebar-hover transition-all duration-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>
</aside>
