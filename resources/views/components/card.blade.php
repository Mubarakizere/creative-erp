@props([
    'padding' => true,
    'title' => null,
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-200/70 shadow-xs hover:shadow-sm transition-all duration-200 overflow-hidden']) }}>
    @if(isset($header) || $title)
        <div class="px-6 py-4 border-b border-gray-100/80 bg-gray-50/40 flex items-center justify-between">
            @if(isset($header))
                {{ $header }}
            @else
                <div class="flex items-center gap-3">
                    @if($icon)
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            {!! $icon !!}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 tracking-tight">{{ $title }}</h3>
                        @if($description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div @class([$padding ? 'p-6' : 'p-0'])>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
    @endif
</div>
