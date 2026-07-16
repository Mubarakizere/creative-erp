<x-layouts.admin title="Upcoming Events">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Calendar', 'url' => route('admin.calendar.index')], ['label' => 'Upcoming']]" />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Upcoming Events</h1>
                <p class="mt-1 text-sm text-gray-500">Next {{ $days }} days — {{ now()->format('M j') }} to {{ now()->addDays($days)->format('M j, Y') }}</p>
            </div>
            <a href="{{ route('admin.calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </a>
        </div>
    </div>

    @php
        $grouped = $events->groupBy(function($event) {
            return $event->start->format('Y-m-d');
        });
    @endphp

    @if($grouped->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 class="text-lg font-medium text-gray-900">All clear!</h3>
            <p class="text-sm text-gray-500">No upcoming events in the next {{ $days }} days.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($grouped as $dateKey => $dayEvents)
                @php $dateObj = \Illuminate\Support\Carbon::parse($dateKey); @endphp
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-50 rounded-xl flex flex-col items-center justify-center border border-blue-100 {{ $dateObj->isToday() ? 'ring-2 ring-blue-500' : '' }}">
                            <span class="text-[10px] font-bold text-blue-600 uppercase">{{ $dateObj->format('M') }}</span>
                            <span class="text-lg font-bold text-blue-700 leading-none">{{ $dateObj->format('j') }}</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $dateObj->format('l, F j') }}</h3>
                            <p class="text-xs text-gray-500">{{ $dayEvents->count() }} event{{ $dayEvents->count() > 1 ? 's' : '' }}{{ $dateObj->isToday() ? ' • Today' : '' }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 ml-[60px]">
                        @foreach($dayEvents as $event)
                            <a href="{{ $event->url }}" class="block bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 p-3 group" style="border-left: 3px solid {{ $event->color }};">
                                <div class="flex items-center justify-between gap-4">
                                    <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 truncate">{{ $event->title }}</h4>
                                    <span class="text-xs font-medium capitalize px-2 py-0.5 rounded-full" style="background-color: {{ $event->color }}15; color: {{ $event->color }};">{{ $event->type }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if(!$event->allDay)
                                        {{ $event->start->format('g:i A') }}{{ $event->end ? ' — ' . $event->end->format('g:i A') : '' }}
                                    @else
                                        All Day
                                    @endif
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
