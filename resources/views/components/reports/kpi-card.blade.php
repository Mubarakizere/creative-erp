@props(['title', 'value', 'icon' => null, 'color' => 'blue', 'trend' => null, 'trendUp' => true])

<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</h3>
        </div>
        @if($icon)
            <div class="flex-shrink-0 w-12 h-12 bg-{{ $color }}-50 rounded-xl flex items-center justify-center text-{{ $color }}-600">
                {!! $icon !!}
            </div>
        @endif
    </div>
    @if($trend)
        <div class="mt-4 flex items-center text-sm">
            @if($trendUp)
                <span class="text-emerald-600 flex items-center font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    {{ $trend }}
                </span>
            @else
                <span class="text-rose-600 flex items-center font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    {{ $trend }}
                </span>
            @endif
            <span class="text-gray-400 ml-2">vs last period</span>
        </div>
    @endif
</div>
