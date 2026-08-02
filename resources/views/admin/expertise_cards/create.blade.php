<x-layouts.admin title="Create Expertise Card">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Website CMS'],
                ['label' => 'Expertise Cards', 'url' => route('admin.expertise-cards.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Expertise Card</h1>
            <p class="mt-1 text-sm text-gray-500">Add a new expertise section to the public website.</p>
        </div>
        <x-button type="ghost" href="{{ route('admin.expertise-cards.index') }}" size="sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </x-button>
    </div>

    <form method="POST" action="{{ route('admin.expertise-cards.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Card Details</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <x-input name="title" label="Title" value="{{ old('title') }}" required />
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 resize-none" required>{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input name="sort_order" type="number" label="Sort Order" value="{{ old('sort_order', 0) }}" />
                            
                            <div class="flex flex-col justify-end pb-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="block text-sm font-medium text-gray-900 mb-4 pb-2 border-b">Feature Image</label>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
                                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="relative flex items-center py-2">
                                <div class="flex-grow border-t border-gray-300"></div>
                                <span class="flex-shrink-0 mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider">OR</span>
                                <div class="flex-grow border-t border-gray-300"></div>
                            </div>
                            
                            <x-input name="image_url" type="url" label="Image URL" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/..." />
                        </div>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3 pb-6">
                <x-button type="ghost" href="{{ route('admin.expertise-cards.index') }}">Cancel</x-button>
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Card
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
