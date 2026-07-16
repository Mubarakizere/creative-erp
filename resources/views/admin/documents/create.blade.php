<x-layouts.admin title="Upload Document">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Documents', 'url' => route('admin.documents.index')],
                ['label' => 'Upload'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Upload Document</h1>
            <p class="mt-1 text-sm text-gray-500">Upload a new document to the system.</p>
        </div>

        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File (Max 100MB)</label>
                        <input type="file" name="file" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    {{-- For global upload, we use Alpine to fetch records dynamically --}}
                    @if(request('documentable_type') && request('documentable_id'))
                        <input type="hidden" name="documentable_type" value="{{ request('documentable_type') }}">
                        <input type="hidden" name="documentable_id" value="{{ request('documentable_id') }}">
                    @else
                        <div x-data="{
                            module: '',
                            records: [],
                            loading: false,
                            fetchRecords() {
                                if (!this.module) {
                                    this.records = [];
                                    return;
                                }
                                this.loading = true;
                                fetch('/admin/documents/records/' + encodeURIComponent(this.module))
                                    .then(res => res.json())
                                    .then(data => {
                                        this.records = data;
                                        this.loading = false;
                                    });
                            }
                        }" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label for="documentable_type" class="block text-sm font-medium text-gray-700 mb-1">Module <span class="text-red-500">*</span></label>
                                <select x-model="module" @change="fetchRecords" name="documentable_type" id="documentable_type" required class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3">
                                    <option value="">Select a Module</option>
                                    <option value="App\Models\Company">Company</option>
                                    <option value="App\Models\Project">Project</option>
                                    <option value="App\Models\Task">Task</option>
                                    <option value="App\Models\Milestone">Milestone</option>
                                    <option value="App\Models\Client">Client</option>
                                    <option value="App\Models\Branch">Branch</option>
                                    <option value="App\Models\Department">Department</option>
                                </select>
                            </div>

                            <div>
                                <label for="documentable_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Record <span class="text-red-500">*</span>
                                    <span x-show="loading" class="text-xs text-blue-500 ml-2">Loading...</span>
                                </label>
                                <select name="documentable_id" id="documentable_id" required class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3" :disabled="records.length === 0">
                                    <option value="">Select a Record</option>
                                    <template x-for="record in records" :key="record.id">
                                        <option :value="record.id" x-text="record.name"></option>
                                    </template>
                                </select>
                                @error('documentable_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <x-select name="category_id" label="Category" :options="$categories->pluck('name', 'id')" />
                    
                    <x-select name="visibility" label="Visibility" :options="['Private' => 'Private', 'Internal' => 'Internal', 'Public' => 'Public']" selected="Internal" />
                    
                    <div class="md:col-span-2">
                        <x-textarea name="description" label="Description" rows="3" placeholder="Optional description..." />
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <x-button type="ghost" href="{{ route('admin.documents.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Upload Document</x-button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.admin>
