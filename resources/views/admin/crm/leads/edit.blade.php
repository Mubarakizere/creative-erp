<x-layouts.admin title="Edit Lead">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Leads', 'url' => route('admin.crm.leads.index')],
                ['label' => $lead->first_name, 'url' => route('admin.crm.leads.show', $lead)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Lead</h1>
    </div>

    <form method="POST" action="{{ route('admin.crm.leads.update', $lead) }}">
        @csrf
        @method('PUT')
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" :selected="$lead->company_id" required />
                @endif
                <x-input name="first_name" label="First Name" :value="$lead->first_name" required />
                <x-input name="last_name" label="Last Name" :value="$lead->last_name" />
                <x-input name="email" label="Email" type="email" :value="$lead->email" />
                <x-input name="phone" label="Phone" :value="$lead->phone" />
                <x-input name="company_name" label="Company Name" :value="$lead->company_name" />
                <x-input name="title" label="Job Title" :value="$lead->title" />
                
                <x-select name="status" label="Status" :options="['New' => 'New', 'Contacted' => 'Contacted', 'Qualified' => 'Qualified', 'Lost' => 'Lost', 'Converted' => 'Converted']" :selected="$lead->status" />
                <x-select name="rating" label="Rating" :options="['Hot' => 'Hot', 'Warm' => 'Warm', 'Cold' => 'Cold']" :selected="$lead->rating" />
                
                <x-input name="expected_value" label="Expected Value ($)" type="number" step="0.01" :value="$lead->expected_value" />
                <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" :value="$lead->probability" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.leads.show', $lead) }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Update Lead</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
