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
        'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'border' => 'border-blue-100', 'ring' => 'ring-blue-500/10'],
        'emerald', 'green' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'border' => 'border-emerald-100', 'ring' => 'ring-emerald-500/10'],
        'teal' => ['bg' => 'bg-teal-50', 'icon' => 'text-teal-600', 'border' => 'border-teal-100', 'ring' => 'ring-teal-500/10'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'border' => 'border-purple-100', 'ring' => 'ring-purple-500/10'],
        'amber' => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'border' => 'border-amber-100', 'ring' => 'ring-amber-500/10'],
        'rose', 'red' => ['bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'border' => 'border-rose-100', 'ring' => 'ring-rose-500/10'],
        'cyan' => ['bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'border' => 'border-cyan-100', 'ring' => 'ring-cyan-500/10'],
        'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600', 'border' => 'border-indigo-100', 'ring' => 'ring-indigo-500/10'],
        'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'border' => 'border-orange-100', 'ring' => 'ring-orange-500/10'],
        'violet' => ['bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'border' => 'border-violet-100', 'ring' => 'ring-violet-500/10'],
        default => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'border' => 'border-blue-100', 'ring' => 'ring-blue-500/10'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-slate-300/80 transition-all duration-300 p-5 group overflow-hidden relative flex flex-col justify-between']) }}>
    @if($href)
        <a href="{{ $href }}" class="block">
    @endif

    <div>
        {{-- Top Row: Title + Icon Badge --}}
        <div class="flex items-center justify-between gap-3 mb-3">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate" title="{{ $title }}">
                {{ $title }}
            </span>
            <div class="shrink-0 {{ $colorClasses['bg'] }} {{ $colorClasses['icon'] }} p-2.5 rounded-xl border {{ $colorClasses['border'] }} ring-1 {{ $colorClasses['ring'] }} group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                @if($icon)
                    {!! $icon !!}
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>

        {{-- Value Row: Takes full width, never overflows or goes outside card --}}
        <div class="min-w-0">
            <p class="text-2xl sm:text-xl lg:text-2xl 2xl:text-2xl font-black text-slate-900 tracking-tight truncate select-all" title="{{ $value }}">
                {{ $value }}
            </p>
        </div>
    </div>

    @if($trend)
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center gap-1.5 text-xs">
            @if($trendUp)
                <span class="inline-flex items-center text-emerald-600 font-semibold gap-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    {{ $trend }}
                </span>
            @else
                <span class="inline-flex items-center text-rose-600 font-semibold gap-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    {{ $trend }}
                </span>
            @endif
            <span class="text-slate-400 font-medium text-[11px]">vs last month</span>
        </div>
    @endif

    @if($href)
        </a>
    @endif
</div>
