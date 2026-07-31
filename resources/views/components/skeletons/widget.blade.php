<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-5 animate-pulse']) }}>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 bg-gray-200 rounded-lg"></div>
        <div class="h-5 bg-gray-200 rounded-md w-32"></div>
    </div>
    <div class="space-y-3">
        @for($i = 0; $i < 3; $i++)
            <div class="h-3 bg-gray-100 rounded-md w-full"></div>
        @endfor
        <div class="h-3 bg-gray-100 rounded-md w-2/3"></div>
    </div>
</div>
