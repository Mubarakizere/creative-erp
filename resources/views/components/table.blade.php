@props([
    'striped' => false,
    'hoverable' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(isset($head))
                <thead class="bg-gray-50">
                    <tr>
                        {{ $head }}
                    </tr>
                </thead>
            @endif

            <tbody @class([
                'divide-y divide-gray-200',
                '[&>tr:nth-child(even)]:bg-gray-50/50' => $striped,
                '[&>tr]:hover:bg-blue-50/50 [&>tr]:transition-colors' => $hoverable,
            ])>
                {{ $slot }}
            </tbody>

            @if(isset($foot))
                <tfoot class="bg-gray-50">
                    {{ $foot }}
                </tfoot>
            @endif
        </table>
    </div>

    @if(isset($pagination))
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/50">
            {{ $pagination }}
        </div>
    @endif
</div>
