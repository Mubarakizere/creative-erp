<x-layouts.admin title="Accounts">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Accounts']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Accounts</h1>
            <p class="mt-1 text-sm text-gray-500">Manage client organizations and companies.</p>
        </div>
        @can('create', App\Models\Account::class)
            <x-button type="primary" href="{{ route('admin.crm.accounts.create') }}">Create Account</x-button>
        @endcan
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.crm.accounts.index') }}" class="flex gap-4">
            <x-input name="search" placeholder="Search accounts..." :value="request('search')" class="w-64" />
            <x-button type="primary">Filter</x-button>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Industry</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Website</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-20">Actions</th>
        </x-slot:head>

        @forelse($accounts as $account)
            <tr>
                <td class="px-4 py-3 font-semibold text-gray-900">{{ $account->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $account->industry?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-blue-600"><a href="{{ $account->website }}" target="_blank">{{ $account->website }}</a></td>
                <td class="px-4 py-3 text-right text-sm">
                    <a href="{{ route('admin.crm.accounts.show', $account) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-12 text-center text-gray-500">No accounts found.</td></tr>
        @endforelse

        <x-slot:pagination>{{ $accounts->links('components.pagination') }}</x-slot:pagination>
    </x-table>
</x-layouts.admin>
