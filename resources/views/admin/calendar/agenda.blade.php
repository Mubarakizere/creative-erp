<x-layouts.admin title="Agenda">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Calendar', 'url' => route('admin.calendar.index')], ['label' => 'Agenda']]" />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Agenda</h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">{{ format_date($date, 'l, F j, Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2 mr-2 text-sm font-medium text-gray-700 bg-white border border-gray-200/60 rounded-xl hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendar
                </a>
                <a href="{{ route('admin.calendar.agenda', ['date' => $date->copy()->subDay()->toDateString()]) }}" class="p-2 rounded-xl bg-white border border-gray-200/60 text-gray-600 hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ route('admin.calendar.agenda', ['date' => now()->toDateString()]) }}" class="px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-200/60 rounded-xl hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">Today</a>
                <a href="{{ route('admin.calendar.agenda', ['date' => $date->copy()->addDay()->toDateString()]) }}" class="p-2 rounded-xl bg-white border border-gray-200/60 text-gray-600 hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
        {{-- Main Agenda Column --}}
        <div class="flex-1 w-full lg:w-auto">
            @if($events->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No events scheduled</h3>
                    <p class="text-sm text-gray-500 mb-6">Nothing is scheduled for {{ format_date($date, 'F j, Y') }}.</p>
                    <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Schedule Meeting
                    </a>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-sm font-bold text-gray-900 tracking-tight">Events for {{ format_date($date, 'l') }}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($events as $event)
                            <a href="{{ $event->url }}" class="block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-200 p-5 group">
                                <div class="flex items-start gap-4">
                                    <div class="w-1.5 h-full min-h-[44px] rounded-full flex-shrink-0 shadow-sm" style="background-color: {{ $event->color }};"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-4">
                                            <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors truncate">{{ $event->title }}</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold capitalize shadow-sm border border-transparent" style="background-color: {{ $event->color }}15; color: {{ $event->color }}; border-color: {{ $event->color }}30;">{{ $event->type }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 mt-2.5 text-xs text-gray-500 font-medium">
                                            @if(!$event->allDay)
                                                <span class="flex items-center gap-1.5 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ format_time($event->start) }} to {{ $event->end ? format_time($event->end) : '' }}
                                                </span>
                                            @else
                                                <span class="flex items-center gap-1.5 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    All Day
                                                </span>
                                            @endif
                                            @if(!empty($event->meta['location']))
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                    {{ $event->meta['location'] }}
                                                </span>
                                            @endif
                                            @if(!empty($event->meta['project']))
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    {{ $event->meta['project'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Week Events Sidebar --}}
        <div class="w-full lg:w-96 flex-shrink-0 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">This Week</h3>
                    <a href="{{ route('admin.calendar.upcoming') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-wider">All</a>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($weekEvents ?? [] as $event)
                        <a href="{{ $event->url }}" class="flex items-start gap-3 group">
                            <div class="mt-1 w-2.5 h-2.5 rounded-full flex-shrink-0 shadow-sm" style="background-color: {{ $event->color }};"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors truncate">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ format_date($event->start, 'l') }} &bull; {{ format_time($event->start) }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-sm font-medium text-gray-500">No events scheduled this week.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
