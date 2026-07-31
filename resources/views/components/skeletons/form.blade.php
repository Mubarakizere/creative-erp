@props(['fields' => 4])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-6']) }}>
    @for($i = 0; $i < $fields; $i++)
        <div class="space-y-2">
            <div class="h-4 bg-gray-200 rounded-md w-1/4"></div>
            <div class="h-10 bg-gray-100 rounded-lg w-full border border-gray-100"></div>
        </div>
    @endfor
    <div class="pt-4 flex items-center justify-end gap-3">
        <div class="h-10 bg-gray-200 rounded-lg w-24"></div>
        <div class="h-10 bg-blue-200 rounded-lg w-32"></div>
    </div>
</div>
