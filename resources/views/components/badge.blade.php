@props([
    'type' => 'default',
    'size' => 'md',
])

@php
    $typeClasses = match($type) {
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/20',
        'info' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'default' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
        'primary' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
        default => 'px-2.5 py-1 text-xs',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full ring-1 ring-inset {$typeClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
