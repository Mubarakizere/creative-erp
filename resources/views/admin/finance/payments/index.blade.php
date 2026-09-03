<x-layouts.admin title="Payments">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Payments']
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Payment::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payments</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Record and manage received payments from clients.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @can('create', App\Models\Payment::class)
                <a href="{{ route('admin.finance.payments.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Record Payment
                </a>
            @endcan
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Ref #</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Client</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Payment Date</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Method</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Amount</th>
                        <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.finance.payments.show', $payment) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors">{{ $payment->reference_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $payment->client->name ?? 'Unknown Client' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">{{ $payment->paymentMethod->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">RWF {{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold leading-5 bg-green-100 text-green-800">
                                    Completed
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-action-dropdown>
                                    @can('view', $payment)
                                        <x-action-dropdown-item href="{{ route('admin.finance.payments.show', $payment) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('delete', $payment)
                                        <form action="{{ route('admin.finance.payments.destroy', $payment) }}" method="POST" id="delete-payment-form-{{ $payment->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-action-dropdown-item onclick="document.getElementById('delete-payment-form-{{ $payment->id }}').submit()" icon="delete" variant="danger">
                                            Delete Payment
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No payments found</h3>
                                <p class="text-sm text-gray-500 font-medium">Get started by recording a new payment.</p>
                                @can('create', App\Models\Payment::class)
                                    <a href="{{ route('admin.finance.payments.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        Record Payment
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($payments, 'hasPages') && $payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $payments->links('components.pagination') }}
            </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view payments.</p>
    </div>
    @endcan
</x-layouts.admin>
