<x-layouts.admin title="Create Lead">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Leads', 'url' => route('admin.crm.leads.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Lead::class)
    <div class="mb-8">
        <a href="{{ route('admin.crm.leads.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Leads
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Lead</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Register a new lead in the CRM pipeline.</p>
    </div>

    <form method="POST" action="{{ route('admin.crm.leads.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Lead Information</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                    @if(is_null(auth()->user()->company_id))
                        <div class="sm:col-span-2">
                            <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                        </div>
                    @endif
                    <div>
                        <x-input name="first_name" label="First Name" required />
                    </div>
                    <div>
                        <x-input name="last_name" label="Last Name" />
                    </div>
                    <div>
                        <x-input name="email" label="Email" type="email" />
                    </div>
                    <div>
                        <x-input name="phone" label="Phone" />
                    </div>
                    <div>
                        <x-input name="company_name" label="Company Name" list="accounts-list" />
                        <datalist id="accounts-list">
                            @foreach($accounts as $accountName)
                                <option value="{{ $accountName }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <x-input name="title" label="Job Title" />
                    </div>
                    
                    <div class="sm:col-span-2 border-t border-gray-100 my-2 pt-6">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 tracking-tight">Status & Pipeline Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                            <div>
                                <x-select name="status" label="Status" :options="['New' => 'New', 'Contacted' => 'Contacted', 'Qualified' => 'Qualified', 'Lost' => 'Lost']" />
                            </div>
                            <div>
                                <x-select name="rating" label="Rating" :options="['Hot' => 'Hot', 'Warm' => 'Warm', 'Cold' => 'Cold']" />
                            </div>
                            <div>
                                <x-input name="expected_value" label="Expected Value (RWF)" type="number" step="0.01" />
                            </div>
                            <div>
                                <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" value="0" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.crm.leads.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Create Lead</button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create leads.</p>
        <div class="mt-6">
            <a href="{{ route('admin.crm.leads.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Leads</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
