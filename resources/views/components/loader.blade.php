@props([
    'size' => 'md',
    'type' => 'spinner',
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12',
        default => 'w-6 h-6',
    };
@endphp

@if($type === 'spinner')
    <svg {{ $attributes->merge(['class' => "animate-spin {$sizeClasses}"]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
@elseif($type === 'dots')
    <div {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
    </div>
@elseif($type === 'skeleton')
    <div {{ $attributes->merge(['class' => 'animate-pulse bg-gray-200 rounded-lg']) }}></div>
@endif
