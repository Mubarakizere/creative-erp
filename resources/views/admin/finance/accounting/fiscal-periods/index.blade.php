<x-layouts.admin title="Fiscal Settings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Fiscal Settings']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fiscal Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Manage fiscal years and accounting periods.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <x-button type="primary" x-data @click="$dispatch('open-modal', 'create-fiscal-year')">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Fiscal Year
            </x-button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-8">
        @forelse($fiscalYears as $year)
            <x-card>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            {{ $year->name }}
                            @if($year->is_closed)
                                <x-badge type="default" class="ml-3">Closed</x-badge>
                            @else
                                <x-badge type="success" class="ml-3">Open</x-badge>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500">{{ $year->start_date->format('M d, Y') }} - {{ $year->end_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                        @if(!$year->is_closed)
                            <x-button type="default" size="sm" x-data @click="$dispatch('open-modal', 'create-period-{{ $year->id }}')">
                                Add Period
                            </x-button>
                            <form action="{{ route('admin.finance.accounting.fiscal-periods.years.close', $year) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this fiscal year? This action cannot be undone.');">
                                @csrf
                                @method('PATCH')
                                <x-button type="warning" size="sm" submit>Close Year</x-button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Period Name</th>
                                <th class="py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date Range</th>
                                <th class="py-2 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($year->periods as $period)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $period->name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-500">{{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}</td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        @if($period->status === 'Closed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Closed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Open</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right">
                                        @if($period->status === 'Open' && !$year->is_closed)
                                            <form action="{{ route('admin.finance.accounting.fiscal-periods.periods.close', $period) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this period? No more journal entries can be posted.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 font-medium">Close</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-sm text-gray-500 text-center">No accounting periods created for this year.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Create Period Modal for this year --}}
            <x-modal id="create-period-{{ $year->id }}" maxWidth="md">
                <x-slot:header>Create Accounting Period</x-slot:header>
                <form action="{{ route('admin.finance.accounting.fiscal-periods.periods.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fiscal_year_id" value="{{ $year->id }}">
                    <div class="space-y-4 py-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Period Name (e.g. Q1 2023, Jan 2023)</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" name="start_date" required min="{{ $year->start_date->format('Y-m-d') }}" max="{{ $year->end_date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" name="end_date" required min="{{ $year->start_date->format('Y-m-d') }}" max="{{ $year->end_date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <x-button type="ghost" @click="$dispatch('close')">Cancel</x-button>
                        <x-button type="primary" submit>Create Period</x-button>
                    </div>
                </form>
            </x-modal>
        @empty
            <x-card>
                <div class="py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Fiscal Years found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new fiscal year.</p>
                </div>
            </x-card>
        @endforelse
    </div>

    {{-- Create Fiscal Year Modal --}}
    <x-modal id="create-fiscal-year" maxWidth="md">
        <x-slot:header>Create Fiscal Year</x-slot:header>
        <form action="{{ route('admin.finance.accounting.fiscal-periods.years.store') }}" method="POST">
            @csrf
            <div class="space-y-4 py-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Year Name (e.g. FY2023)</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-button type="ghost" @click="$dispatch('close')">Cancel</x-button>
                <x-button type="primary" submit>Create Year</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.admin>
