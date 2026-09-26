<x-layouts.admin :title="'Payment ' . $payment->payment_number">
    <div class="mx-auto max-w-5xl space-y-6 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Procurement · Supplier payment</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $payment->payment_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $payment->payment_date?->format('d M Y') ?? 'Date not set' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.procurement.payments.pdf', $payment) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Download PDF</a>
                <a href="{{ route('admin.procurement.payments.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-6 border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Paid amount</p>
                    <p class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">RWF {{ number_format($payment->amount, 2) }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Payment method</p>
                    <p class="mt-1 text-base font-medium capitalize text-slate-800">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                </div>
            </div>

            <div class="grid gap-8 p-6 sm:grid-cols-2">
                <section>
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Supplier</h2>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $payment->supplier?->name ?? 'Supplier not available' }}</p>
                    @if($payment->supplier?->code)<p class="mt-1 text-sm text-slate-600">Supplier code: {{ $payment->supplier->code }}</p>@endif
                    @if($payment->supplier?->email)<p class="mt-1 text-sm text-slate-600">{{ $payment->supplier->email }}</p>@endif
                    @if($payment->supplier?->phone)<p class="mt-1 text-sm text-slate-600">{{ $payment->supplier->phone }}</p>@endif
                </section>
                <section>
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Payment reference</h2>
                    <p class="mt-2 text-sm text-slate-800">{{ $payment->reference ?? $payment->reference_number ?? 'No reference supplied' }}</p>
                    @if($payment->bankAccount)
                        <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Paid from</p>
                        <p class="mt-1 text-sm text-slate-800">{{ $payment->bankAccount->account_name }}{{ $payment->bankAccount->bank_name ? ' · ' . $payment->bankAccount->bank_name : '' }}</p>
                    @endif
                </section>
            </div>

            @if($payment->invoice)
                <div class="border-t border-slate-200 px-6 py-5">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Applied to supplier invoice</h2>
                    <a href="{{ route('admin.procurement.invoices.show', $payment->invoice->id) }}" class="mt-2 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 p-4 hover:bg-slate-50">
                        <span class="font-medium text-slate-900">{{ $payment->invoice->invoice_number }}</span>
                        <span class="text-sm text-slate-600">Invoice total: RWF {{ number_format($payment->invoice->grand_total, 2) }}</span>
                    </a>
                </div>
            @endif

            @if($payment->notes)
                <div class="border-t border-slate-200 px-6 py-5">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Notes</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $payment->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
