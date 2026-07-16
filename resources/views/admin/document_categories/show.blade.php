<x-layouts.admin title="{{ $documentCategory->name }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Document Categories', 'url' => route('admin.document-categories.index')],
                ['label' => $documentCategory->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $documentCategory->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Category details</p>
        </div>
        <div class="flex gap-2">
            @can('update', $documentCategory)
                <x-button type="ghost" href="{{ route('admin.document-categories.edit', $documentCategory) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </x-button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Information</h3>
                </x-slot:header>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentCategory->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentCategory->slug }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentCategory->description ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <x-badge :type="$documentCategory->is_active ? 'success' : 'default'">
                                {{ $documentCategory->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentCategory->sort_order }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
