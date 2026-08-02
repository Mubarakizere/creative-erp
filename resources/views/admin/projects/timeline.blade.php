<x-layouts.admin title="Project Timeline">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $project->name, 'url' => route('admin.projects.show', $project)],
                ['label' => 'Timeline'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Project Timeline</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $project->name }} ({{ $project->project_code }})</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.projects.show', $project) }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Profile
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="relative border-l-2 border-gray-200 ml-4 max-w-4xl mx-auto">
            @forelse($events as $event)
                <div class="mb-8 pl-8 relative group">
                    <div class="absolute w-10 h-10 rounded-xl -left-[21px] bg-white border-2 flex items-center justify-center shadow-sm transition-transform group-hover:scale-110
                        {{ $event['type'] == 'created' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : '' }}
                        {{ $event['type'] == 'closed' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50' : '' }}
                        {{ $event['type'] == 'success' ? 'border-green-500 text-green-600 bg-green-50/50' : '' }}
                        {{ $event['type'] == 'info' ? 'border-purple-500 text-purple-600 bg-purple-50/50' : 'border-gray-200 text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($event['icon'] == 'plus')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            @elseif($event['icon'] == 'check-circle' || $event['icon'] == 'check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            @elseif($event['icon'] == 'flag')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            @elseif($event['icon'] == 'clipboard-list')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            @elseif($event['icon'] == 'document')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                    </div>
                    
                    <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-2">
                            <h3 class="font-bold text-gray-900 text-base">{{ $event['title'] }}</h3>
                            <span class="text-xs font-bold text-gray-500 whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-gray-200/60">{{ $event['date']->format('F j, Y h:i A') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 font-medium">{{ $event['description'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-sm font-medium text-gray-500">No events recorded yet.</p>
                </div>
            @endforelse
        </div>
    </x-card>
</x-layouts.admin>
