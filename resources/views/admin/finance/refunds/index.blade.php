<x-layouts.admin title="Refunds">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Refunds']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Refunds</h1>
            <p class="mt-1 text-sm text-gray-500">Manage processed refunds to clients.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('create', App\Models\Refund::class)
                <x-button type="primary" href="{{ route('admin.finance.refunds.create') }}">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Process Refund
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ref #</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($refunds as $refund)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.finance.refunds.show', $refund) }}">{{ $refund->refund_number }}</a>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900">
                                {{ $refund->client->name ?? 'Unknown' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $refund->refund_date->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $refund->refund_method }}</td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                ${{ number_format($refund->amount, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm text-right space-x-2">
                                <a href="{{ route('admin.finance.refunds.show', $refund) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No refunds found</h3>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($refunds->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $refunds->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
