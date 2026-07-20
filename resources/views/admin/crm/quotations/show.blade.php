<x-layouts.admin title="Quotation {{ $quotation->quotation_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Quotations', 'url' => route('admin.crm.quotations.index')],
                ['label' => $quotation->quotation_number],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header & Actions --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $quotation->quotation_number }}</h1>
                @php
                    $statusType = match($quotation->status) {
                        'Draft' => 'default',
                        'Pending Approval' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        'Sent' => 'info',
                        'Accepted' => 'success',
                        'Declined' => 'danger',
                        'Expired' => 'default',
                        'Converted' => 'primary',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType">{{ $quotation->status ?? 'Draft' }}</x-badge>
            </div>
            @if($quotation->reference)
                <p class="mt-1 text-sm text-gray-500">Reference: {{ $quotation->reference }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            @if($quotation->status === 'Draft')
                @can('update', $quotation)
                    <form method="POST" action="{{ route('admin.crm.quotations.submit', $quotation) }}" class="inline">
                        @csrf
                        <x-button type="primary" size="sm" submit>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Submit for Approval
                        </x-button>
                    </form>
                @endcan
            @endif

            @if($quotation->status === 'Pending Approval')
                @can('approve', $quotation)
                    <form method="POST" action="{{ route('admin.crm.quotations.approve', $quotation) }}" class="inline">
                        @csrf
                        <x-button type="success" size="sm" submit>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve
                        </x-button>
                    </form>
                    <form method="POST" action="{{ route('admin.crm.quotations.reject', $quotation) }}" class="inline">
                        @csrf
                        <x-button type="danger" size="sm" submit>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </x-button>
                    </form>
                @endcan
            @endif

            @can('export', $quotation)
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <x-button type="ghost" size="sm" @click="open = !open" @click.away="open = false">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </x-button>
                    <div x-show="open" x-transition style="display: none;" class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 focus:outline-none">
                        <div class="py-1">
                            <form method="POST" action="{{ route('admin.crm.quotations.export', $quotation) }}">
                                @csrf
                                <input type="hidden" name="format" value="pdf">
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">PDF</button>
                            </form>
                            <form method="POST" action="{{ route('admin.crm.quotations.export', $quotation) }}">
                                @csrf
                                <input type="hidden" name="format" value="xlsx">
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">Excel</button>
                            </form>
                            <form method="POST" action="{{ route('admin.crm.quotations.export', $quotation) }}">
                                @csrf
                                <input type="hidden" name="format" value="csv">
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">CSV</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

            @can('update', $quotation)
                <x-button type="ghost" size="sm" href="{{ route('admin.crm.quotations.edit', $quotation) }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </x-button>
            @endcan

            @can('create', App\Models\Quotation::class)
                <form method="POST" action="{{ route('admin.crm.quotations.duplicate', $quotation) }}" class="inline">
                    @csrf
                    <x-button type="ghost" size="sm" submit>
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Duplicate
                    </x-button>
                </form>
            @endcan
        </div>
    </div>

    {{-- Quotation Preview Layout --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-3 space-y-6">
            <x-card class="print-friendly">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-12">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Quotation</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ $quotation->quotation_number }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $quotation->company->name ?? 'Creative ERP' }}</p>
                        @if(isset($quotation->company->address))
                            <p>{!! nl2br(e($quotation->company->address)) !!}</p>
                        @endif
                    </div>
                </div>

                {{-- Parties Info --}}
                <div class="grid grid-cols-2 gap-12 mb-12 border-b border-t border-gray-100 py-6">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Quotation To:</h3>
                        <p class="text-base font-semibold text-gray-900">
                            @if($quotation->account)
                                {{ $quotation->account->name }}
                            @elseif($quotation->lead)
                                {{ $quotation->lead->first_name }} {{ $quotation->lead->last_name }}
                            @elseif($quotation->opportunity)
                                {{ $quotation->opportunity->name }}
                            @else
                                <span class="text-gray-400 italic">No Customer Specified</span>
                            @endif
                        </p>
                        
                        @if($quotation->contact)
                            <p class="text-sm text-gray-600 mt-1">Attn: {{ $quotation->contact->first_name }} {{ $quotation->contact->last_name }}</p>
                        @endif

                        @if($quotation->account && $quotation->account->billing_address)
                            <p class="text-sm text-gray-600 mt-1">{!! nl2br(e($quotation->account->billing_address)) !!}</p>
                        @endif
                    </div>

                    <div>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Date</span>
                                <span class="font-medium text-gray-900">{{ $quotation->created_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Valid Until</span>
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($quotation->valid_until)->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Prepared By</span>
                                <span class="font-medium text-gray-900">{{ $quotation->owner->name ?? 'System' }}</span>
                            </div>
                            @if($quotation->paymentTerm)
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Payment Terms</span>
                                <span class="font-medium text-gray-900">{{ $quotation->paymentTerm->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="mb-8 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-y border-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider w-2/5">Product / Service</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Qty</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit Price</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Discount</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Tax</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quotation->items as $item)
                                <tr>
                                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $item->product_name }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-right">
                                        @if($item->discount > 0)
                                            {{ $item->discount_type === 'percentage' ? $item->discount . '%' : '$' . number_format($item->discount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-right">
                                        @if($item->tax)
                                            {{ $item->tax->name }} ({{ $item->tax->rate }}%)
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-sm font-semibold text-gray-900 text-right">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals Area --}}
                <div class="flex justify-end mb-12">
                    <div class="w-full sm:w-1/2 lg:w-1/3">
                        <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium">${{ number_format($quotation->subtotal, 2) }}</span>
                            </div>
                            
                            @if($quotation->total_discount > 0)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Total Savings</span>
                                    <span class="font-medium">${{ number_format($quotation->total_discount, 2) }}</span>
                                </div>
                            @endif

                            @if($quotation->total_tax > 0)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Total Tax</span>
                                    <span class="font-medium">${{ number_format($quotation->total_tax, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                                    <span class="text-xl font-black text-gray-900">${{ number_format($quotation->grand_total, 2) }}</span>
                                </div>
                                <p class="text-xs text-right text-gray-500 mt-1">All amounts in {{ $quotation->currency ?? 'USD' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes & Terms --}}
                @if($quotation->notes || $quotation->terms)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-200 pt-8">
                        @if($quotation->notes)
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 mb-2">Notes</h4>
                                <div class="text-sm text-gray-600 prose prose-sm max-w-none">
                                    {!! nl2br(e($quotation->notes)) !!}
                                </div>
                            </div>
                        @endif
                        @if($quotation->terms)
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 mb-2">Terms & Conditions</h4>
                                <div class="text-sm text-gray-600 prose prose-sm max-w-none">
                                    {!! nl2br(e($quotation->terms)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </x-card>

            {{-- Documents Section --}}
            <div class="print-hide">
                @include('admin.documents.partials.document_tab', ['documentable' => $quotation])
            </div>

            {{-- Discussions Section --}}
            <div class="print-hide">
                <x-card>
                    <x-discussions :model="$quotation" />
                </x-card>
            </div>
        </div>

        {{-- Sidebar: Activity & Info --}}
        <div class="space-y-6">
            <x-card>
                <x-slot:header>Information</x-slot:header>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Created</span>
                        <span class="text-sm text-gray-900">{{ $quotation->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Last Updated</span>
                        <span class="text-sm text-gray-900">{{ $quotation->updated_at->diffForHumans() }}</span>
                    </div>
                    @if($quotation->opportunity)
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Related Opportunity</span>
                            <a href="{{ route('admin.crm.opportunities.show', $quotation->opportunity) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                {{ $quotation->opportunity->name }}
                            </a>
                        </div>
                    @endif
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
