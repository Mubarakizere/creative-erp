@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'blue',
    'href' => null,
])

@php
    $colorClasses = match($color) {
        'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'ring' => 'ring-blue-500/20'],
        'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'ring' => 'ring-emerald-500/20'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'ring' => 'ring-purple-500/20'],
        'amber' => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'ring' => 'ring-amber-500/20'],
        'rose' => ['bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'ring' => 'ring-rose-500/20'],
        'cyan' => ['bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'ring' => 'ring-cyan-500/20'],
        'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600', 'ring' => 'ring-indigo-500/20'],
        'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'ring' => 'ring-orange-500/20'],
        default => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'ring' => 'ring-blue-500/20'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-6 group']) }}>
    @if($href)
        <a href="{{ $href }}" class="block">
    @endif

    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>

            @if($trend)
                <div class="mt-2 flex items-center gap-1">
                    @if($trendUp)
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-emerald-600">{{ $trend }}</span>
                    @else
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-red-600">{{ $trend }}</span>
                    @endif
                    <span class="text-xs text-gray-400">vs last month</span>
                </div>
            @endif
        </div>

        {{-- Icon --}}
        <div class="flex-shrink-0 {{ $colorClasses['bg'] }} {{ $colorClasses['icon'] }} p-3 rounded-xl ring-1 {{ $colorClasses['ring'] }} group-hover:scale-110 transition-transform duration-300">
            @if($icon)
                {!! $icon !!}
            @else
                {{ $slot }}
            @endif
        </div>
    </div>

    @if($href)
        </a>
    @endif
</div>
