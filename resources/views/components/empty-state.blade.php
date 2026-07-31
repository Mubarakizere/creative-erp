@props([
    'title' => 'No records found',
    'description' => 'Get started by creating a new record.',
    'icon' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 sm:p-12 text-center bg-white border border-gray-100 border-dashed rounded-xl']) }}>
    
    <div class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-gray-50 text-gray-400">
        @if($icon)
            {!! $icon !!}
        @else
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        @endif
    </div>

    <h3 class="mb-2 text-lg font-bold text-gray-900 tracking-tight">
        {{ $title }}
    </h3>
    
    <p class="mb-6 max-w-sm text-sm text-gray-500 leading-relaxed">
        {{ $description }}
    </p>

    @if(isset($action))
        <div class="flex flex-col sm:flex-row gap-3">
            {{ $action }}
        </div>
    @endif
</div>
