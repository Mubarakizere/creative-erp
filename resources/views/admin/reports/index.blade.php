<x-layouts.admin title="Reports & Analytics">
    <div x-data="{ searchQuery: '' }">
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                <p class="mt-1 text-sm text-gray-500">View executive summaries and generated reports.</p>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search reports...">
                </div>
                @can('report.create')
                <div>
                    <a href="{{ route('admin.reports.builder') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 whitespace-nowrap">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        New Custom Report
                    </a>
                </div>
                @endcan
            </div>
        </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Favorite Reports --}}
    @if($favoriteTemplates->count() > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Favorite Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($favoriteTemplates as $template)
                    <a href="{{ route('admin.reports.show', $template) }}" x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="bg-white rounded-xl border border-yellow-200 p-5 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-gray-900 group-hover:text-blue-600">{{ $template->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($template->description, 60) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- System Templates --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Standard Reports</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($systemTemplates as $template)
                <a href="{{ route('admin.reports.show', $template) }}" x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="flex justify-between items-start">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900 group-hover:text-blue-600">{{ $template->name }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ Str::limit($template->description, 60) }}</p>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Custom Templates --}}
    @if($userTemplates->count() > 0)
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">My Custom Reports</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($userTemplates as $template)
                <div x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="relative group">
                    <a href="{{ route('admin.reports.show', $template) }}" class="block bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow h-full">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-gray-900 group-hover:text-blue-600">{{ $template->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($template->description, 60) }}</p>
                    </a>
                    
                    <div class="absolute top-5 right-5 z-10">
                        <x-modal id="delete-modal-{{$template->id}}" maxWidth="md">
                            <x-slot name="trigger">
                                <button type="button" class="text-gray-400 hover:text-red-500 relative z-10 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </x-slot>

                            <x-slot name="header">
                                <div class="flex items-center text-red-600">
                                    <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Delete Report
                                </div>
                            </x-slot>

                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete the <strong>{{ $template->name }}</strong> report? This action cannot be undone.
                            </p>

                            <x-slot name="footer">
                                <button type="button" @click="open = false" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm transition-colors">
                                    Cancel
                                </button>
                                <form action="{{ route('admin.reports.destroy', $template) }}" method="POST" class="ml-3">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:text-sm transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </x-slot>
                        </x-modal>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    </div>
</x-layouts.admin>
