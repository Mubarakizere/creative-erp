<x-layouts.admin title="Create Website Project">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Website CMS'],
                ['label' => 'Website Projects', 'url' => route('admin.website-projects.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="max-w-5xl mx-auto pb-12">
        <!-- Header -->
        <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        Create Website Project
                    </span>
                </h1>
                <p class="mt-2 text-sm text-gray-500 font-medium">Add a new expertise section to the public website with a stunning presentation.</p>
            </div>
            <x-button type="secondary" href="{{ route('admin.website-projects.index') }}" size="sm" class="group transition-all hover:bg-white hover:shadow-md border border-transparent hover:border-gray-200">
                <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </x-button>
        </div>

        <form method="POST" action="{{ route('admin.website-projects.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content Column -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- General Information Card -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100/80 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-32 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-50 -z-10 group-hover:opacity-70 transition-opacity"></div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            General Information
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Project Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="block w-full rounded-xl border-gray-200 bg-gray-50/50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all sm:text-sm">
                                @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all sm:text-sm">
                                @error('category') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                                <textarea name="description" id="description" rows="6" required class="block w-full rounded-xl border-gray-200 bg-gray-50/50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all sm:text-sm resize-none">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Media Card -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100/80 hover:shadow-md transition-shadow">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Project Media
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Image</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-blue-400 hover:bg-blue-50/50 transition-all group cursor-pointer relative">
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-blue-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center mt-2">
                                            <span class="relative bg-transparent rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                Upload a file
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                                    </div>
                                    <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                                </div>
                                @error('image') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="relative flex items-center py-2">
                                <div class="flex-grow border-t border-gray-200"></div>
                                <span class="flex-shrink-0 mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider">OR URL</span>
                                <div class="flex-grow border-t border-gray-200"></div>
                            </div>
                            
                            <div>
                                <label for="image_url" class="block text-sm font-semibold text-gray-700 mb-1.5">Image URL</label>
                                <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}" placeholder="https://..." class="block w-full rounded-xl border-gray-200 bg-gray-50/50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all sm:text-sm">
                                @error('image_url') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Settings Card -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100/80 hover:shadow-md transition-shadow">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Visibility & Order
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1.5">Sort Order</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all sm:text-sm">
                                @error('sort_order') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">Active Status</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Show on the live website</p>
                                </div>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer for Actions -->
            <div class="mt-8 pt-5 pb-5 border-t border-gray-200/60 flex items-center justify-end gap-4 sticky bottom-0 bg-gray-50/90 backdrop-blur-md px-6 rounded-t-2xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] border border-gray-200/50 z-20">
                <a href="{{ route('admin.website-projects.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md shadow-blue-500/25 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Publish Project
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
