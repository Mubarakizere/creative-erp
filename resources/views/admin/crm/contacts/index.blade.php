<x-layouts.admin title="Contacts">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Contacts']]; @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Contact::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Contacts</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage individuals associated with your accounts.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\Contact::class)
                <a href="{{ route('admin.crm.contacts.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Contact
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.crm.contacts.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search contacts..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </div>
            <div class="flex items-center gap-2 justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('admin.crm.contacts.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Name</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Email</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Phone</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Account</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.crm.contacts.show', $contact) }}" class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $contact->email }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $contact->phone ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $contact->account?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-action-dropdown>
                                    @can('view', $contact)
                                        <x-action-dropdown-item href="{{ route('admin.crm.contacts.show', $contact) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('update', $contact)
                                        <x-action-dropdown-item href="{{ route('admin.crm.contacts.edit', $contact) }}" icon="edit">
                                            Edit Contact
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('delete', $contact)
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-contact-{{ $contact->id }}')" icon="delete" variant="danger">
                                            Delete Contact
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>

                                @can('delete', $contact)
                                    <x-modal id="delete-contact-{{ $contact->id }}" maxWidth="md">
                                        <x-slot:header>Delete Contact</x-slot:header>
                                        <div class="text-center py-4">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Delete Contact?</h3>
                                            <p class="text-sm text-gray-500 font-medium">Are you sure you want to delete <strong>{{ $contact->first_name }} {{ $contact->last_name }}</strong>? This action cannot be undone.</p>
                                        </div>
                                        <x-slot:footer>
                                            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                            <form method="POST" action="{{ route('admin.crm.contacts.destroy', $contact) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete</button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No contacts found</h3>
                                <p class="text-sm text-gray-500 font-medium">Get started by creating a new contact.</p>
                                @can('create', App\Models\Contact::class)
                                    <a href="{{ route('admin.crm.contacts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        Create Contact
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($contacts, 'hasPages') && $contacts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $contacts->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view contacts.</p>
    </div>
    @endcan
</x-layouts.admin>
