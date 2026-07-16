<x-layouts.admin title="{{ $document->original_name }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Documents', 'url' => route('admin.documents.index')],
                ['label' => Str::limit($document->original_name, 30)],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $document->original_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Version {{ $document->version }}</p>
        </div>
        <div class="flex gap-2">
            @can('download', $document)
                <x-button type="primary" href="{{ route('admin.documents.download', $document) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </x-button>
            @endcan
            @can('update', $document)
                <x-button type="ghost" href="{{ route('admin.documents.edit', $document) }}">
                    Edit / Replace
                </x-button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- Preview section (Images, PDFs) --}}
            @if(in_array(strtolower($document->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                <x-card>
                    <div class="flex justify-center bg-gray-50 rounded-lg overflow-hidden">
                        <img src="{{ $document->url }}" alt="{{ $document->original_name }}" class="max-h-[500px] object-contain">
                    </div>
                </x-card>
            @elseif(strtolower($document->extension) === 'pdf')
                <x-card>
                    <div class="aspect-[4/3] bg-gray-50 rounded-lg overflow-hidden">
                        <iframe src="{{ $document->url }}" class="w-full h-full border-0"></iframe>
                    </div>
                </x-card>
            @else
                <x-card>
                    <div class="py-12 flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm">Preview not available for this file type.</p>
                        <p class="text-xs mt-1 text-gray-400">{{ strtoupper($document->extension) }} files must be downloaded to be viewed.</p>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                </x-slot:header>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->description ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Related Record</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($document->documentable)
                                {{ class_basename($document->documentable_type) }} #{{ $document->documentable_id }}
                                ({{ $document->documentable->name ?? $document->documentable->title ?? 'Record' }})
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-card>
            
            <div class="mt-6">
                <x-discussions :model="$document" />
            </div>
        </div>

        <div class="space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Information</h3>
                </x-slot:header>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">File Size</dt>
                        <dd class="font-medium text-gray-900">{{ $document->formatted_size }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Extension</dt>
                        <dd class="font-medium text-gray-900">{{ strtoupper($document->extension) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-medium text-gray-900">{{ $document->category->name ?? 'Uncategorized' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Visibility</dt>
                        <dd class="font-medium">
                            <x-badge :type="match($document->visibility) { 'Public' => 'success', 'Internal' => 'info', 'Private' => 'warning', default => 'default' }">
                                {{ $document->visibility }}
                            </x-badge>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Uploaded By</dt>
                        <dd class="font-medium text-gray-900">{{ $document->uploader->name ?? 'System' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Uploaded At</dt>
                        <dd class="font-medium text-gray-900">{{ $document->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-4">
                        <dt class="text-gray-500">Last Modified</dt>
                        <dd class="font-medium text-gray-900">{{ $document->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
