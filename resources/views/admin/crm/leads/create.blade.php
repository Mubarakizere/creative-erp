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

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Lead</h1>
                <p class="mt-1 text-sm text-gray-500">Register a new lead in the CRM.</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.crm.leads.index') }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Leads
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.crm.leads.store') }}">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                @endif
                <x-input name="first_name" label="First Name" required />
                <x-input name="last_name" label="Last Name" />
                <x-input name="email" label="Email" type="email" />
                <x-input name="phone" label="Phone" />
                <x-input name="company_name" label="Company Name" />
                <x-input name="title" label="Job Title" />
                
                <x-select name="status" label="Status" :options="['New' => 'New', 'Contacted' => 'Contacted', 'Qualified' => 'Qualified', 'Lost' => 'Lost']" />
                <x-select name="rating" label="Rating" :options="['Hot' => 'Hot', 'Warm' => 'Warm', 'Cold' => 'Cold']" />
                
                <x-input name="expected_value" label="Expected Value ($)" type="number" step="0.01" />
                <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" value="0" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.leads.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Create Lead</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
