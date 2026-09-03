<x-layouts.admin title="Calendar">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Calendar']]" />
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Calendar</h1>
                <p class="mt-1 text-sm text-gray-500">Unified schedule meetings, tasks, milestones & deadlines.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.calendar.agenda') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Agenda
                </a>
                @can('create', App\Models\Meeting::class)
                <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New Meeting
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8" x-data="calendarApp({{ $year }}, {{ $month }})">
        {{-- Calendar Grid --}}
        <div class="flex-1 w-full lg:w-auto">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                {{-- Calendar Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight" x-text="monthNames[currentMonth - 1] + ' ' + currentYear"></h2>
                        <span class="px-2.5 py-1 rounded-full bg-white border border-gray-200 text-xs font-medium text-gray-600 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors" @click="goToToday()">Today</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="prevMonth()" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="nextMonth()" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Day Headers --}}
                <div class="grid grid-cols-7 border-b border-gray-100 bg-white">
                    <template x-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']">
                        <div class="px-2 py-3.5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest" x-text="day"></div>
                    </template>
                </div>

                {{-- Calendar Days Grid --}}
                <div class="grid grid-cols-7 bg-gray-100 gap-px border-b border-gray-100">
                    <template x-for="(day, index) in calendarDays" :key="index">
                        <div
                            class="min-h-[100px] sm:min-h-[120px] bg-white p-1 sm:p-2 transition-all cursor-pointer hover:bg-blue-50/30 group relative"
                            :class="{
                                'opacity-50': !day.currentMonth,
                            }"
                            @click="selectDate(day.date)"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-sm font-semibold w-8 h-8 flex items-center justify-center rounded-full transition-all"
                                    :class="{
                                        'bg-blue-600 text-white shadow-md shadow-blue-500/30': day.isToday,
                                        'text-gray-700 group-hover:text-blue-600': day.currentMonth && !day.isToday,
                                        'text-gray-400': !day.currentMonth
                                    }"
                                    x-text="day.dayNumber"
                                ></span>
                            </div>
                            <div class="space-y-1">
                                <template x-for="event in day.events.slice(0, 3)" :key="event.id">
                                    <a
                                        :href="event.url"
                                        class="block px-2 py-1 text-[11px] font-semibold rounded-md truncate transition-all hover:opacity-90 hover:scale-[1.02]"
                                        :style="'background-color: ' + event.color + '15; color: ' + event.color + '; border-left: 3px solid ' + event.color"
                                        x-text="event.title"
                                        @click.stop
                                    ></a>
                                </template>
                                <div x-show="day.events.length > 3" class="text-[10px] text-gray-500 font-semibold px-2 py-0.5" x-text="'+' + (day.events.length - 3) + ' more'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="w-full lg:w-80 flex-shrink-0 space-y-6">
            {{-- Today's Schedule --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">Today's Schedule</h3>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($todaysSchedule as $event)
                        <a href="{{ $event->url }}" class="block p-3 rounded-xl hover:bg-gray-50 transition-all border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200" style="border-left: 4px solid {{ $event->color }};">
                            <p class="text-sm font-semibold text-gray-900">{{ $event->title }}</p>
                            <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 font-medium">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @if(!$event->allDay)
                                    {{ format_time($event->start) }} to {{ $event->end ? format_time($event->end) : '' }}
                                @else
                                    All Day
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">You're all clear today!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">Upcoming</h3>
                    <a href="{{ route('admin.calendar.upcoming') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-wider">View All</a>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($upcomingEvents as $event)
                        <a href="{{ $event->url }}" class="flex items-start gap-3 group">
                            <div class="mt-1 w-2.5 h-2.5 rounded-full flex-shrink-0 shadow-sm" style="background-color: {{ $event->color }};"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ format_datetime($event->start) }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4 font-medium">No upcoming events.</p>
                    @endforelse
                </div>
            </div>

            {{-- Legend --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">Event Types</h3>
                </div>
                <div class="p-5 flex flex-wrap gap-3">
                    @foreach($legend as $item)
                        <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-gray-100 bg-gray-50/50">
                            <div class="w-2.5 h-2.5 rounded-full shadow-sm" style="background-color: {{ $item['color'] }};"></div>
                            <span class="text-xs font-semibold text-gray-700">{{ $item['label'] }}</span>
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

            goToToday() {
                const today = new Date();
                this.currentMonth = today.getMonth() + 1;
                this.currentYear = today.getFullYear();
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
