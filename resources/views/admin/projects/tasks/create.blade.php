<x-layouts.admin title="Create Task">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Tasks', 'url' => route('admin.projects.tasks.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Task</h1>
                <p class="mt-1 text-sm text-gray-500">Register a new task in the system.</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.projects.tasks.index') }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.projects.tasks.store') }}">
        @csrf
        @include('admin.projects.tasks.partials.form', ['task' => null])
    </form>
</x-layouts.admin>
