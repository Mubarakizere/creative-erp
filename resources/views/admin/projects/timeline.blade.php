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
        <div class="relative border-l-2 border-gray-200 ml-3">
            @foreach($events as $event)
                <div class="mb-8 pl-8 relative">
                    <div class="absolute w-8 h-8 rounded-full -left-4 bg-white border-2 border-gray-200 flex items-center justify-center
                        {{ $event['type'] == 'created' ? 'border-blue-500 text-blue-500' : '' }}
                        {{ $event['type'] == 'closed' ? 'border-emerald-500 text-emerald-500' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($event['icon'] == 'plus')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            @elseif($event['icon'] == 'check-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-900">{{ $event['title'] }}</h3>
                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $event['date']->format('F j, Y h:i A') }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $event['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
</x-layouts.admin>
