<x-layouts.admin title="Project Expenses">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance'],
                ['label' => 'Expenses', 'url' => route('admin.finance.expenses.index')],
                ['label' => $project->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @php
        $currency = $project->currency ?: 'RWF';
        $laborLines = $taskGroups->sum(fn (array $group) => $group['labor']->count());
        $materialLines = $taskGroups->sum(fn (array $group) => $group['materials']->count());
        $laborTotal = $taskGroups->sum('labor_total');
        $materialsTotal = $taskGroups->sum('materials_total');
    @endphp

    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin.finance.expenses.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">← All expenses</a>
            <p class="mt-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $project->project_code }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $project->name }} expenses</h1>
            <p class="mt-1 text-sm text-slate-500">Expense lines for this project, grouped by task.</p>
        </div>
        @can('view', $project)
            <a href="{{ route('admin.projects.show', ['project' => $project, 'tab' => 'expenses']) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Open project</a>
        @endcan
    </div>

    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Labor · {{ $laborLines }} lines</p>
            <p class="mt-1 text-lg font-extrabold text-indigo-800">{{ format_currency($laborTotal, $currency) }}</p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50/70 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Materials · {{ $materialLines }} lines</p>
            <p class="mt-1 text-lg font-extrabold text-blue-800">{{ format_currency($materialsTotal, $currency) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Labor + materials</p>
            <p class="mt-1 text-lg font-extrabold text-slate-900">{{ format_currency($laborTotal + $materialsTotal, $currency) }}</p>
        </div>
    </div>

    <div class="space-y-5">
        @forelse($taskGroups as $group)
            @php
                $task = $group['task'];
                $taskTotal = $group['labor_total'] + $group['materials_total'];
            @endphp
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Task</p>
                        <h2 class="mt-1 font-bold text-slate-900">{{ $task?->name ?? 'Unassigned task' }}</h2>
                        @if($task?->task_code)<p class="mt-0.5 text-xs text-slate-400">{{ $task->task_code }}</p>@endif
                    </div>
                    <div class="sm:text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Task Expenses</p>
                        <p class="mt-1 text-xl font-black tracking-tight text-slate-900">{{ format_currency($taskTotal, $currency) }}</p>
                        <p class="text-[10px] text-slate-500">Labor + materials</p>
                    </div>
                </div>

                <div class="grid xl:grid-cols-2">
                    <div class="border-b border-slate-200 p-4 sm:p-5 xl:border-b-0 xl:border-r">
                        <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-indigo-700">Labor Expenses</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-xs">
                                <thead class="border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <tr><th class="py-2 pr-3">Resource Type</th><th class="py-2 pr-3">Payee / Date</th><th class="py-2 text-right">Amount Payable</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($group['labor'] as $line)
                                        <tr>
                                            <td class="py-2.5 pr-3 font-semibold text-slate-800">{{ $line->title }}</td>
                                            <td class="py-2.5 pr-3 text-slate-500">{{ $line->vendor_name ?: '—' }}<span class="mt-0.5 block text-[10px] text-slate-400">{{ $line->expense_date?->format('d M Y') }} · {{ $line->payment_status }}</span></td>
                                            <td class="whitespace-nowrap py-2.5 text-right font-bold text-slate-800">{{ format_currency($line->amount, $currency) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-5 text-center text-slate-400">No labor expenses recorded for this task.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="border-t border-slate-200"><tr><td colspan="2" class="py-3 font-bold text-slate-600">Labor Total</td><td class="py-3 text-right font-extrabold text-indigo-700">{{ format_currency($group['labor_total'], $currency) }}</td></tr></tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-blue-700">Materials Expenses</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-xs">
                                <thead class="border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <tr><th class="py-2 pr-3">Expense Account</th><th class="py-2 pr-3">Payee / Date</th><th class="py-2 text-right">Amount Payable</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($group['materials'] as $line)
                                        <tr>
                                            <td class="py-2.5 pr-3 font-semibold text-slate-800">{{ $line->title }}</td>
                                            <td class="py-2.5 pr-3 text-slate-500">{{ $line->vendor_name ?: '—' }}<span class="mt-0.5 block text-[10px] text-slate-400">{{ $line->expense_date?->format('d M Y') }} · {{ $line->payment_status }}</span></td>
                                            <td class="whitespace-nowrap py-2.5 text-right font-bold text-slate-800">{{ format_currency($line->amount, $currency) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-5 text-center text-slate-400">No materials expenses recorded for this task.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="border-t border-slate-200"><tr><td colspan="2" class="py-3 font-bold text-slate-600">Materials Total</td><td class="py-3 text-right font-extrabold text-blue-700">{{ format_currency($group['materials_total'], $currency) }}</td></tr></tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if($group['other']->isNotEmpty())
                    <div class="border-t border-slate-200 bg-white px-4 py-4 sm:px-5">
                        <h3 class="mb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Other Project Expenses</h3>
                        <div class="divide-y divide-slate-100">
                            @foreach($group['other'] as $line)
                                <div class="flex flex-wrap items-center justify-between gap-2 py-2 text-xs">
                                    <div>
                                        <span class="font-semibold text-slate-700">{{ $line->title }}</span>
                                        <span class="ml-1 text-slate-400">{{ $line->category }} · {{ $line->expense_date?->format('d M Y') }}</span>
                                        @if($line->vendor_name)<span class="ml-1 text-slate-400">· {{ $line->vendor_name }}</span>@endif
                                    </div>
                                    <span class="font-bold text-slate-700">{{ format_currency($line->amount, $currency) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                <h2 class="font-bold text-slate-800">No expenses for this project</h2>
                <p class="mt-1 text-sm text-slate-500">Add project expenses from the Finance expenses page or the project’s Expenses tab.</p>
            </div>
        @endforelse
    </div>
</x-layouts.admin>
