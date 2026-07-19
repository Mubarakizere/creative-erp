<x-layouts.admin title="Create Contact">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Contacts', 'url' => route('admin.crm.contacts.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Create Contact</h1></div>
        <x-button type="ghost" href="{{ route('admin.crm.contacts.index') }}" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.contacts.store') }}">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                @endif
                <x-input name="first_name" label="First Name" required />
                <x-input name="last_name" label="Last Name" required />
                <x-input name="email" label="Email" type="email" />
                <x-input name="phone" label="Phone" />
                <x-input name="position" label="Position / Job Title" />
                <x-input name="address" label="Address" class="sm:col-span-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.contacts.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Create Contact</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
