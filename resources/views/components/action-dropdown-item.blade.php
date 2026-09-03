@props([
    'href' => null,
    'type' => 'link', // 'link', 'button', 'form'
    'action' => null,
    'method' => 'POST',
    'danger' => false,
    'warning' => false,
])

@php
    $baseClasses = 'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 text-left cursor-pointer';
    
    if ($danger) {
        $colorClasses = 'text-rose-600 bg-rose-50/50 hover:bg-rose-100/80 hover:text-rose-700';
    } elseif ($warning) {
        $colorClasses = 'text-amber-700 bg-amber-50/50 hover:bg-amber-100/80 hover:text-amber-800';
    } else {
        $colorClasses = 'text-slate-700 hover:bg-slate-100 hover:text-slate-900';
    }

    $classes = "{$baseClasses} {$colorClasses}";
@endphp

@if($type === 'form' && $action)
    <form method="POST" action="{{ $action }}" class="w-full" @submit="open = false">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif
        <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
            @if(isset($icon))
                <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $icon !!}</span>
            @endif
            <span>{{ $slot }}</span>
        </button>
    </form>
@elseif($type === 'button')
    <button type="button" @click="open = false" {{ $attributes->merge(['class' => $classes]) }}>
        @if(isset($icon))
            <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $icon !!}</span>
        @endif
        <span>{{ $slot }}</span>
    </button>
@else
    <a href="{{ $href }}" @click="open = false" {{ $attributes->merge(['class' => $classes]) }}>
        @if(isset($icon))
            <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $icon !!}</span>
        @endif
        <span>{{ $slot }}</span>
    </a>
@endif
