<div {{ $attributes->merge(['class' => 'animate-pulse space-y-8 w-full']) }}>
    <!-- Logo -->
    <div class="flex items-center gap-3 px-4 py-3">
        <div class="w-8 h-8 bg-gray-200 rounded-lg"></div>
        <div class="h-6 bg-gray-200 rounded-md w-24"></div>
    </div>

    <!-- Navigation Blocks -->
    @for($i = 0; $i < 3; $i++)
        <div class="space-y-3 px-2">
            <div class="h-3 bg-gray-200 rounded-md w-16 mb-4 ml-2"></div>
            @for($j = 0; $j < 4; $j++)
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-5 h-5 bg-gray-100 rounded"></div>
                    <div class="h-4 bg-gray-100 rounded-md w-3/4"></div>
                </div>
            @endfor
        </div>
    @endfor
</div>
