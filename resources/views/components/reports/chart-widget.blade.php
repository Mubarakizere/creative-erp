@props(['id', 'title', 'type', 'labels' => [], 'data' => [], 'colors' => []])

<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    <div class="relative h-64 w-full">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id }}').getContext('2d');
        new Chart(ctx, {
            type: '{{ $type }}',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    data: {!! json_encode($data) !!},
                    backgroundColor: {!! json_encode($colors ?? ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endpush
