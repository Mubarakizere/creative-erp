@props([
    'href' => null,
    'type' => 'link', // 'link', 'button', 'form'
    'action' => null,
    'method' => 'POST',
    'danger' => false,
    'warning' => false,
    'variant' => null,
    'icon' => null,
])

@php
    if (!$href && $type === 'link') {
        $type = 'button';
    }

    if ($variant === 'danger') {
        $danger = true;
    } elseif ($variant === 'warning') {
        $warning = true;
    }

    $baseClasses = 'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 text-left cursor-pointer';
    
    if ($danger) {
        $colorClasses = 'text-rose-600 bg-rose-50/50 hover:bg-rose-100/80 hover:text-rose-700';
    } elseif ($warning) {
        $colorClasses = 'text-amber-700 bg-amber-50/50 hover:bg-amber-100/80 hover:text-amber-800';
    } else {
        $colorClasses = 'text-slate-700 hover:bg-slate-100 hover:text-slate-900';
    }

    $classes = "{$baseClasses} {$colorClasses}";

    $renderedIcon = null;
    if ($icon === 'delete' || $icon === 'trash') {
        $renderedIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
    } elseif ($icon === 'edit') {
        $renderedIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
    } elseif ($icon === 'view' || $icon === 'eye') {
        $renderedIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
    } elseif ($icon) {
        $renderedIcon = $icon;
    }

    $clickAction = 'open = false';
    if ($attributes->has('@click')) {
        $clickAction = 'open = false; ' . $attributes->get('@click');
    }
@endphp

@if($type === 'form' && $action)
    <form method="POST" action="{{ $action }}" class="w-full" @submit="open = false">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif
        <button type="submit" {{ $attributes->except('@click')->merge(['class' => $classes]) }} @click="{{ $clickAction }}">
            @if($renderedIcon)
                <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $renderedIcon !!}</span>
            @endif
            <span>{{ $slot }}</span>
        </button>
    </form>
@elseif($type === 'button')
    <button type="button" {{ $attributes->except('@click')->merge(['class' => $classes]) }} @click="{{ $clickAction }}">
        @if($renderedIcon)
            <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $renderedIcon !!}</span>
        @endif
        <span>{{ $slot }}</span>
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->except('@click')->merge(['class' => $classes]) }} @click="{{ $clickAction }}">
        @if($renderedIcon)
            <span class="w-4 h-4 shrink-0 flex items-center justify-center text-current">{!! $renderedIcon !!}</span>
        @endif
        <span>{{ $slot }}</span>
    </a>
@endif

