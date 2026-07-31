@props(['items' => 4])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-4']) }}>
    @for($i = 0; $i < $items; $i++)
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-gray-200 rounded-full shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded-md w-3/4"></div>
                <div class="h-3 bg-gray-100 rounded-md w-1/2"></div>
            </div>
            <div class="h-8 bg-gray-100 rounded-md w-16"></div>
        </div>
    @endfor
</div>
