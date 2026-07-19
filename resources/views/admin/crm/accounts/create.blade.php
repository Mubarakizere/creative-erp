<x-layouts.admin title="Create Account">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Accounts', 'url' => route('admin.crm.accounts.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Create Account</h1></div>
        <x-button type="ghost" href="{{ route('admin.crm.accounts.index') }}" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.accounts.store') }}">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                @endif
                <x-input name="name" label="Account Name" required />
                <x-input name="website" label="Website" type="url" />
                <x-input name="email" label="Email" type="email" />
                <x-input name="phone" label="Phone" />
                <x-input name="address" label="Address" class="sm:col-span-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.accounts.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Create Account</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
