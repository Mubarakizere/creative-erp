<x-layouts.admin title="Calendar">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Calendar']]" />
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Calendar</h1>
                <p class="mt-1 text-sm text-gray-500">Unified schedule — meetings, tasks, milestones & deadlines.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.calendar.agenda') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Agenda
                </a>
                <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New Meeting
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="calendarApp({{ $year }}, {{ $month }})">
        {{-- Calendar Grid --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Calendar Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <button @click="prevMonth()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-900" x-text="monthNames[currentMonth - 1] + ' ' + currentYear"></h2>
                    <button @click="nextMonth()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                {{-- Day Headers --}}
                <div class="grid grid-cols-7 border-b border-gray-200">
                    <template x-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']">
                        <div class="px-2 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" x-text="day"></div>
                    </template>
                </div>

                {{-- Calendar Days Grid --}}
                <div class="grid grid-cols-7">
                    <template x-for="(day, index) in calendarDays" :key="index">
                        <div
                            class="min-h-[100px] p-1.5 border-b border-r border-gray-100 transition-colors cursor-pointer hover:bg-blue-50/50"
                            :class="{
                                'bg-blue-50/70': day.isToday,
                                'bg-gray-50/50': !day.currentMonth
                            }"
                            @click="selectDate(day.date)"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full"
                                    :class="{
                                        'bg-blue-600 text-white': day.isToday,
                                        'text-gray-900': day.currentMonth && !day.isToday,
                                        'text-gray-400': !day.currentMonth
                                    }"
                                    x-text="day.dayNumber"
                                ></span>
                                <span
                                    x-show="day.events.length > 0"
                                    class="text-[10px] font-bold text-gray-400"
                                    x-text="day.events.length + ' event' + (day.events.length > 1 ? 's' : '')"
                                ></span>
                            </div>
                            <div class="space-y-0.5">
                                <template x-for="event in day.events.slice(0, 3)" :key="event.id">
                                    <a
                                        :href="event.url"
                                        class="block px-1.5 py-0.5 text-[11px] font-medium rounded truncate transition-opacity hover:opacity-80"
                                        :style="'background-color: ' + event.color + '20; color: ' + event.color + '; border-left: 2px solid ' + event.color"
                                        x-text="event.title"
                                        @click.stop
                                    ></a>
                                </template>
                                <div x-show="day.events.length > 3" class="text-[10px] text-gray-500 font-medium px-1.5" x-text="'+' + (day.events.length - 3) + ' more'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Today's Schedule --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Today's Schedule</h3>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($todaysSchedule as $event)
                        <a href="{{ $event->url }}" class="block p-2.5 rounded-lg hover:bg-gray-50 transition-colors border-l-3" style="border-left: 3px solid {{ $event->color }};">
                            <p class="text-sm font-medium text-gray-900">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if(!$event->allDay)
                                    {{ $event->start->format('g:i A') }} — {{ $event->end?->format('g:i A') }}
                                @else
                                    All Day
                                @endif
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Nothing scheduled today.</p>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Upcoming</h3>
                    <a href="{{ route('admin.calendar.upcoming') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View All</a>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($upcomingEvents as $event)
                        <a href="{{ $event->url }}" class="block p-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $event->color }};"></div>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 ml-4">{{ $event->start->format('M j, g:i A') }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming events.</p>
                    @endforelse
                </div>
            </div>

            {{-- Legend --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Event Types</h3>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($legend as $item)
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $item['color'] }};"></div>
                            <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function calendarApp(initialYear, initialMonth) {
        return {
            currentYear: initialYear,
            currentMonth: initialMonth,
            events: @json($events->map->toArray()->values()),
            calendarDays: [],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

            init() {
                this.buildCalendar();
            },

            buildCalendar() {
                const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth, 0);
                let startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Monday start

                const days = [];
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Previous month days
                const prevMonthLast = new Date(this.currentYear, this.currentMonth - 1, 0);
                for (let i = startDay - 1; i >= 0; i--) {
                    const d = prevMonthLast.getDate() - i;
                    const dateStr = this.formatDate(this.currentYear, this.currentMonth - 1, d);
                    days.push({
                        dayNumber: d,
                        date: dateStr,
                        currentMonth: false,
                        isToday: false,
                        events: this.getEventsForDate(dateStr)
                    });
                }

                // Current month days
                for (let d = 1; d <= lastDay.getDate(); d++) {
                    const dateObj = new Date(this.currentYear, this.currentMonth - 1, d);
                    const dateStr = this.formatDate(this.currentYear, this.currentMonth, d);
                    days.push({
                        dayNumber: d,
                        date: dateStr,
                        currentMonth: true,
                        isToday: dateObj.getTime() === today.getTime(),
                        events: this.getEventsForDate(dateStr)
                    });
                }

                // Fill remaining cells
                const remaining = 42 - days.length;
                for (let d = 1; d <= remaining; d++) {
                    const dateStr = this.formatDate(this.currentYear, this.currentMonth + 1, d);
                    days.push({
                        dayNumber: d,
                        date: dateStr,
                        currentMonth: false,
                        isToday: false,
                        events: this.getEventsForDate(dateStr)
                    });
                }

                this.calendarDays = days;
            },

            getEventsForDate(dateStr) {
                return this.events.filter(e => {
                    const eStart = e.start.substring(0, 10);
                    const eEnd = e.end ? e.end.substring(0, 10) : eStart;
                    return dateStr >= eStart && dateStr <= eEnd;
                });
            },

            formatDate(year, month, day) {
                // Handle overflow
                const d = new Date(year, month - 1, day);
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            },

            prevMonth() {
                if (this.currentMonth === 1) {
                    this.currentMonth = 12;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.fetchEvents();
            },

            nextMonth() {
                if (this.currentMonth === 12) {
                    this.currentMonth = 1;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.fetchEvents();
            },

            selectDate(dateStr) {
                window.location.href = '{{ route("admin.calendar.agenda") }}?date=' + dateStr;
            },

            async fetchEvents() {
                const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
                const startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
                const start = new Date(this.currentYear, this.currentMonth - 1, 1 - startDay);
                const end = new Date(this.currentYear, this.currentMonth, 7);

                const startStr = start.toISOString();
                const endStr = end.toISOString();

                try {
                    const response = await fetch(`{{ route('admin.calendar.events') }}?start=${startStr}&end=${endStr}`);
                    const data = await response.json();
                    this.events = data.events;
                    this.buildCalendar();
                } catch (err) {
                    console.error('Failed to fetch calendar events:', err);
                    this.buildCalendar();
                }
            }
        };
    }
    </script>
    @endpush
</x-layouts.admin>
