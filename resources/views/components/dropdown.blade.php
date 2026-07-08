@props([
    'align' => 'right',
    'width' => '48',
])

@php
    $alignClasses = match($align) {
        'left' => 'left-0 origin-top-left',
        'right' => 'right-0 origin-top-right',
        'center' => 'left-1/2 -translate-x-1/2 origin-top',
        default => 'right-0 origin-top-right',
    };

    $widthClasses = match($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        '72' => 'w-72',
        default => 'w-48',
    };
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative inline-block" {{ $attributes }}>
    {{-- Trigger --}}
    <div @click="open = !open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    {{-- Content --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $alignClasses }} {{ $widthClasses }} rounded-xl bg-white shadow-lg ring-1 ring-black/5 overflow-hidden"
        style="display: none;"
        @click="open = false"
    >
        {{ $content }}
    </div>
</div>
