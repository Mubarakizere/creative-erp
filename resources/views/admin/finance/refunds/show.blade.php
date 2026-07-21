<x-layouts.admin title="Refund {{ $refund->refund_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Refunds', 'url' => route('admin.finance.refunds.index')],
                ['label' => $refund->refund_number]
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Refund: {{ $refund->refund_number }}</h1>
                @php
                    $statusType = match($refund->status) {
                        'Pending Approval' => 'warning',
                        'Approved' => 'primary',
                        'Processed' => 'success',
                        'Rejected' => 'danger',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType">{{ $refund->status }}</x-badge>
            </div>
            @if($refund->client)
                <p class="mt-1 text-sm text-gray-500">Refunded to: {{ $refund->client->name }} on {{ $refund->refund_date->format('M d, Y') }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-2 print-hide">
            <x-button type="ghost" size="sm" onclick="window.print()">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </x-button>
        </div>
    </div>

    @if($refund->approval)
        <div class="mb-6 print-hide">
            <x-card class="bg-blue-50 border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-900">Approval Workflow</h3>
                            <p class="text-sm text-blue-700 mt-1">This refund is currently: <strong>{{ $refund->approval->status }}</strong>.</p>
                        </div>
                    </div>
                    <x-button type="primary" size="sm" href="{{ route('admin.approvals.show', $refund->approval) }}">
                        View Approval Details
                    </x-button>
                </div>
            </x-card>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-2 space-y-6">
            <x-card class="print-friendly p-8">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">REFUND RECEIPT</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ $refund->refund_number }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $refund->company->name ?? 'Creative ERP' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-12 mb-12">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Refunded To:</h3>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $refund->client->name ?? 'Unknown Client' }}
                        </p>
                    </div>

                    <div>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Date</span>
                                <span class="font-medium text-gray-900">{{ $refund->refund_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Method</span>
                                <span class="font-medium text-gray-900">{{ $refund->refund_method }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Refund Amount</span>
                                <span class="text-2xl font-black text-blue-900">${{ number_format($refund->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="mb-12">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Reason for Refund:</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! nl2br(e($refund->reason)) !!}
                    </p>
                </div>
            </x-card>
        </div>

        {{-- Sidebar: Info & Actions --}}
        <div class="space-y-6 print-hide">
            <x-card>
                <x-slot:header>Information</x-slot:header>
                <div class="space-y-4">
                    @if($refund->payment_id)
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Related Payment</span>
                            <a href="{{ route('admin.finance.payments.show', $refund->payment_id) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                {{ $refund->payment->reference_number ?? 'View Payment' }}
                            </a>
                        </div>
                    @endif
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Processed On</span>
                        <span class="text-sm text-gray-900">{{ $refund->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <style>
        @media print {
            aside, nav, .breadcrumbs, .print-hide, form {
                display: none !important;
            }
            .print-friendly {
                box-shadow: none !important;
                border: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
            }
        }
    </style>
</x-layouts.admin>
