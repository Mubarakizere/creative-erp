<x-layouts.admin title="Account Details">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Accounts', 'url' => route('admin.crm.accounts.index')], ['label' => $account->name]]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">{{ $account->name }}</h1></div>
        <div class="flex items-center gap-2">
            @can('create', App\Models\Quotation::class)
                <x-button type="primary" href="{{ route('admin.crm.quotations.create', ['account_id' => $account->id]) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Quotation
                </x-button>
            @endcan
            @can('update', $account)
                <x-button type="ghost" href="{{ route('admin.crm.accounts.edit', $account) }}">Edit</x-button>
            @endcan
            <x-button type="secondary" href="{{ route('admin.meetings.create', ['meetingable_type' => get_class($account), 'meetingable_id' => $account->id]) }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Schedule Meeting
            </x-button>
            <x-button type="secondary" href="{{ route('admin.documents.create', ['documentable_type' => get_class($account), 'documentable_id' => $account->id]) }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Document
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500 block">Name</span><span class="font-medium text-gray-900">{{ $account->name }}</span></div>
            <div><span class="text-gray-500 block">Website</span><span class="font-medium text-gray-900">{{ $account->website ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500 block">Email</span><span class="font-medium text-gray-900">{{ $account->email ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500 block">Phone</span><span class="font-medium text-gray-900">{{ $account->phone ?? 'N/A' }}</span></div>
            <div class="sm:col-span-2"><span class="text-gray-500 block">Address</span><span class="font-medium text-gray-900">{{ $account->address ?? 'N/A' }}</span></div>
        </div>
    </x-card>
</x-layouts.admin>
