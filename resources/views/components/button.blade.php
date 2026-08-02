@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'submit' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 min-h-[38px] text-sm',
        'md' => 'px-4 py-2.5 min-h-[42px] text-sm',
        'lg' => 'px-5 py-3 min-h-[48px] text-base',
        'xl' => 'px-6 py-3.5 text-base',
    };

    $typeClasses = match($type) {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm shadow-blue-500/20',
        'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500 shadow-sm',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
        'ghost' => 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-500',
        default => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$typeClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @click="Alpine.store('loading').start()">
        @if($loading)
            <x-loader size="sm" class="mr-2" />
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        x-data="{ submitting: {{ $loading ? 'true' : 'false' }} }"
        @if($submit)
            @click="if($el.closest('form') && $el.closest('form').checkValidity()) { setTimeout(() => { submitting = true; Alpine.store('loading').start(); }, 0); }"
        @endif
        x-bind:disabled="submitting || {{ $disabled ? 'true' : 'false' }}"
        x-bind:class="{ 'opacity-75 cursor-wait': submitting }"
        {{ $attributes->merge(['class' => $classes, 'type' => $submit ? 'submit' : 'button', 'disabled' => $disabled || $loading]) }}
    >
        <template x-if="submitting">
            <x-loader size="sm" class="mr-2" />
        </template>
        @if($loading)
            <x-loader size="sm" class="mr-2" x-show="!submitting" />
        @endif
        {{ $slot }}
    </button>
@endif
