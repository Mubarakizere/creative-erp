<x-layouts.admin title="Edit Client">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Clients', 'url' => route('admin.clients.index')],
                ['label' => $client->display_name, 'url' => route('admin.clients.show', $client)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Client</h1>
                <p class="mt-1 text-sm text-gray-500">Update information for {{ $client->display_name }}.</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.clients.show', $client) }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Profile
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.clients.update', $client) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.clients.partials.form', ['client' => $client])
    </form>
</x-layouts.admin>
