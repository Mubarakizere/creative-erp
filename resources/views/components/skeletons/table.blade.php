@props(['rows' => 5, 'cols' => 4])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-pulse']) }}>
    <!-- Header -->
    <div class="bg-gray-50 border-b border-gray-100 px-6 py-4 flex items-center justify-between gap-4">
        @for($i = 0; $i < $cols; $i++)
            <div class="h-4 bg-gray-200 rounded-md flex-1"></div>
        @endfor
    </div>
    
    <!-- Rows -->
    <div class="divide-y divide-gray-100">
        @for($r = 0; $r < $rows; $r++)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                @for($c = 0; $c < $cols; $c++)
                    <div class="h-4 bg-gray-100 rounded-md flex-1 {{ $c === 0 ? 'w-3/4' : ($c === $cols - 1 ? 'w-1/4' : 'w-1/2') }}"></div>
                @endfor
            </div>
        @endfor
    </div>
</div>
