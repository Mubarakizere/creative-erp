@props([
    'title' => 'Select Action',
])

<div x-data="{ open: false }" class="inline-block text-left">
    <button @click="open = true" 
            type="button" 
            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all duration-150 border border-transparent hover:border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer"
            title="Actions">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
        </svg>
    </button>

    {{-- Teleported Modal Overlay --}}
    <template x-teleport="body">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
             style="display: none;"
             @keydown.escape.window="open = false">

            {{-- Modal Content Box --}}
            <div @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="w-full max-w-xs bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden text-left">
                
                {{-- Header --}}
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">{{ $title }}</span>
                    <button @click="open = false" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Action Items List --}}
                <div class="p-2 space-y-1">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
