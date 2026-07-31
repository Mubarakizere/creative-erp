<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-pulse flex flex-col h-full']) }}>
    <div class="flex items-center justify-between mb-6">
        <div class="h-5 bg-gray-200 rounded-md w-1/3"></div>
        <div class="h-8 bg-gray-100 rounded-md w-24"></div>
    </div>
    <div class="flex-1 min-h-[250px] w-full bg-gray-50 rounded-lg border border-gray-100 flex items-end justify-between p-4 gap-2">
        <!-- Bars -->
        @for($i = 0; $i < 12; $i++)
            <div class="w-full bg-gray-200 rounded-t-sm" style="height: {{ rand(20, 90) }}%;"></div>
        @endfor
    </div>
</div>
