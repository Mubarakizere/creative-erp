@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-100">
            {{ $header }}
        </div>
    @endif

    <div @class([$padding ? 'p-6' : 'p-0'])>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
