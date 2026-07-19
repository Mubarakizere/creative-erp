<x-layouts.admin title="Contacts">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Contacts']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contacts</h1>
            <p class="mt-1 text-sm text-gray-500">Manage individuals associated with your accounts.</p>
        </div>
        @can('create', App\Models\Contact::class)
            <x-button type="primary" href="{{ route('admin.crm.contacts.create') }}">Create Contact</x-button>
        @endcan
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.crm.contacts.index') }}" class="flex gap-4">
            <x-input name="search" placeholder="Search contacts..." :value="request('search')" class="w-64" />
            <x-button type="primary">Filter</x-button>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Account</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-20">Actions</th>
        </x-slot:head>

        @forelse($contacts as $contact)
            <tr>
                <td class="px-4 py-3 font-semibold text-gray-900">{{ $contact->first_name }} {{ $contact->last_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contact->email }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contact->phone ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contact->account?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-right text-sm">
                    <a href="{{ route('admin.crm.contacts.show', $contact) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No contacts found.</td></tr>
        @endforelse

        <x-slot:pagination>{{ $contacts->links('components.pagination') }}</x-slot:pagination>
    </x-table>
</x-layouts.admin>
