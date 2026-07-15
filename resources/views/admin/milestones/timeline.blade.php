<x-layouts.admin title="Milestone Timeline">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Milestones', 'url' => route('admin.milestones.index')],
                ['label' => $milestone->name, 'url' => route('admin.milestones.show', $milestone)],
                ['label' => 'Timeline'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $milestone->name }} - Timeline</h1>
                <p class="mt-1 text-sm text-gray-500">Timeline view for this milestone.</p>
            </div>
            <div class="flex gap-2">
                <x-button type="ghost" href="{{ route('admin.milestones.show', $milestone) }}" size="sm">
                    Back to Milestone
                </x-button>
            </div>
        </div>
    </div>

    <x-card>
        <div class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900">Milestone Timeline</h3>
            <p class="mt-1 text-sm text-gray-500">Timeline feature implementation will be enhanced in the upcoming sprints.</p>
        </div>
    </x-card>
</x-layouts.admin>
