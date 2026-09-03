@props([
    'striped' => false,
    'hoverable' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70">
            @if(isset($head))
                <thead class="bg-slate-50/80">
                    <tr class="divide-x divide-transparent">
                        {{ $head }}
                    </tr>
                </thead>
            @endif

            <tbody @class([
                'bg-white divide-y divide-slate-100',
                '[&>tr:nth-child(even)]:bg-slate-50/40' => $striped,
                '[&>tr]:hover:bg-slate-50/80 [&>tr]:transition-colors [&>tr]:duration-150' => $hoverable,
            ])>
                {{ $slot }}
            </tbody>

            @if(isset($foot))
                <tfoot class="bg-slate-50/80 border-t border-slate-200/70">
                    {{ $foot }}
                </tfoot>
            @endif
        </table>
    </div>

    @if(isset($pagination))
        <div class="px-5 py-3.5 border-t border-slate-200/70 bg-slate-50/40">
            {{ $pagination }}
        </div>
    @endif
</div>
