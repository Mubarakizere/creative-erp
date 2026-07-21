<x-layouts.admin title="Payments">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Payments']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
            <p class="mt-1 text-sm text-gray-500">Record and manage received payments from clients.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('create', App\Models\Payment::class)
                <x-button type="primary" href="{{ route('admin.finance.payments.create') }}">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Record Payment
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
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Date</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.finance.payments.show', $payment) }}">{{ $payment->reference_number }}</a>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900">
                                {{ $payment->client->name ?? 'Unknown Client' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                ${{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
                                <x-badge type="success">Completed</x-badge>
                            </td>
                            <td class="py-4 px-4 text-sm text-right space-x-2">
                                <a href="{{ route('admin.finance.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by recording a new payment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $payments->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
