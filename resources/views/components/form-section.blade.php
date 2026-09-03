@props([
    'title',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <div class="md:col-span-1">
        <div class="px-1 sm:px-0">
            <div class="flex items-center gap-2.5">
                @if($icon)
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        {!! $icon !!}
                    </div>
                @endif
                <h3 class="text-base font-bold text-gray-900 tracking-tight">{{ $title }}</h3>
            </div>
            @if($description)
                <p class="mt-1.5 text-xs text-gray-500 leading-relaxed">
                    {{ $description }}
                </p>
            @endif
        </div>
    </div>
    <div class="mt-4 md:mt-0 md:col-span-2">
        {{ $slot }}
    </div>
</div>
