<x-layouts.admin title="Create Contact">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Contacts', 'url' => route('admin.crm.contacts.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Contact::class)
    <div class="mb-8">
        <a href="{{ route('admin.crm.contacts.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Contacts
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Contact</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Add a new individual to your CRM.</p>
    </div>

    <form method="POST" action="{{ route('admin.crm.contacts.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Contact Information</h3>
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
                        <x-input name="last_name" label="Last Name" required />
                    </div>
                    <div>
                        <x-input name="email" label="Email" type="email" />
                    </div>
                    <div>
                        <x-input name="phone" label="Phone" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input name="position" label="Position / Job Title" />
                    </div>
                    <div class="sm:col-span-2 mt-2">
                        <x-input name="address" label="Address" />
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.crm.contacts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Create Contact</button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create contacts.</p>
        <div class="mt-6">
            <a href="{{ route('admin.crm.contacts.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Contacts</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
