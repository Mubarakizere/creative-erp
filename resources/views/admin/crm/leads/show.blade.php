<x-layouts.admin title="Lead Details">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Leads', 'url' => route('admin.crm.leads.index')],
                ['label' => $lead->first_name . ' ' . $lead->last_name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $lead->company_name ?? 'No Company' }} | {{ $lead->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $lead)
                    <x-button type="ghost" href="{{ route('admin.crm.leads.edit', $lead) }}">Edit</x-button>
                @endcan
                <x-button type="secondary" href="{{ route('admin.meetings.create', ['meetingable_type' => get_class($lead), 'meetingable_id' => $lead->id]) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Schedule Meeting
                </x-button>
                <x-button type="secondary" href="{{ route('admin.documents.create', ['documentable_type' => get_class($lead), 'documentable_id' => $lead->id]) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Document
                </x-button>
                @if(in_array($lead->status, ['New', 'Contacted', 'Qualified']))
                    @can('convert', $lead)
                        <form method="POST" action="{{ route('admin.crm.leads.convert', $lead) }}">
                            @csrf
                            <input type="hidden" name="create_account" value="1">
                            <input type="hidden" name="create_contact" value="1">
                            <input type="hidden" name="create_opportunity" value="1">
                            <input type="hidden" name="opportunity_name" value="{{ $lead->company_name ?? $lead->last_name . ' Deal' }}">
                            <x-button type="primary" submit>Convert Lead</x-button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Lead Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Name</span>
                        <span class="font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Company Name</span>
                        <span class="font-medium text-gray-900">{{ $lead->company_name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Email</span>
                        <span class="font-medium text-gray-900">{{ $lead->email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Phone</span>
                        <span class="font-medium text-gray-900">{{ $lead->phone ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Expected Value</span>
                        <span class="font-medium text-gray-900">{{ $lead->expected_value ? '$' . number_format($lead->expected_value, 2) : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Probability</span>
                        <span class="font-medium text-gray-900">{{ $lead->probability }}%</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Timeline</h2>
                </div>
                <div x-data="{ html: '<div class=\'py-8 flex justify-center\'><svg class=\'animate-spin h-5 w-5 text-gray-500\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg></div>' }"
                     x-init="fetch('{{ route('admin.crm.leads.timeline', $lead) }}').then(response => response.text()).then(data => html = data)"
                     x-html="html">
                </div>
            </x-card>

            <x-card>
                <x-discussions :model="$lead" />
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <h3 class="text-md font-semibold text-gray-900 mb-3">Status</h3>
                @php
                    $statusType = match($lead->status) {
                        'New' => 'primary',
                        'Contacted' => 'info',
                        'Qualified' => 'warning',
                        'Converted' => 'success',
                        'Lost' => 'danger',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType" class="text-sm px-3 py-1">{{ $lead->status }}</x-badge>
            </x-card>

            <x-card>
                <h3 class="text-md font-semibold text-gray-900 mb-3">Owner</h3>
                @if($lead->owner)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($lead->owner->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $lead->owner->name }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Unassigned</p>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.admin>
