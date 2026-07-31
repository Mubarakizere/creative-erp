<x-layouts.admin title="Edit Project">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $project->name, 'url' => route('admin.projects.show', $project)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $project)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Profile
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Project</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Update details for {{ $project->name }}.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.projects.update', $project) }}" id="project-form">
        @csrf
        @method('PUT')
        @include('admin.projects.partials.form', ['project' => $project])
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to edit this project.</p>
        <div class="mt-6">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Project</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
