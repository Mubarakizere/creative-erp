<x-layouts.admin title="Reports & Analytics">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <div x-data="{ searchQuery: '' }">
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reports & Analytics</h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">Financial dashboard and generated reports.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm transition-colors min-h-[42px]" placeholder="Search reports...">
                </div>
                @can('create', \App\Models\ReportTemplate::class)
                <div>
                    <a href="{{ route('admin.reports.builder') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors whitespace-nowrap min-h-[42px]">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Custom Report
                    </a>
                </div>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50/50 border border-green-100 p-4 rounded-xl flex items-center shadow-sm">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Financial Dashboard Widgets --}}
        <div x-show="searchQuery === ''" class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-4">Financial Overview</h2>
            
            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-2xl border border-gray-200/60 p-6 shadow-sm flex items-center">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Invoiced</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">RWF {{ number_format($totalInvoiced, 2) }}</h3>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl border border-gray-200/60 p-6 shadow-sm flex items-center">
                    <div class="w-14 h-14 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Received</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">RWF {{ number_format($totalPaid, 2) }}</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200/60 p-6 shadow-sm flex items-center">
                    <div class="w-14 h-14 rounded-xl bg-red-50 flex items-center justify-center text-red-600 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Outstanding Balance</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">RWF {{ number_format($totalOutstanding, 2) }}</h3>
                    </div>
                </div>
            </div>

            {{-- Charts Area --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Revenue Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Monthly Payments Received (Last 6 Months)</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Status Pie Charts --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6 flex flex-col justify-between">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Invoice Status</h3>
                        <div class="relative h-40 w-full flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6 flex flex-col justify-between">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Payment Methods</h3>
                        <div class="relative h-40 w-full flex items-center justify-center">
                            <canvas id="methodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Favorite Reports --}}
        @if($favoriteTemplates->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-4">Favorite Reports</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($favoriteTemplates as $template)
                        <a href="{{ route('admin.reports.show', $template) }}" x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="bg-white rounded-2xl border border-yellow-200 p-6 shadow-sm hover:shadow-md hover:border-yellow-300 transition-all group relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-yellow-50 to-transparent rounded-bl-3xl opacity-50"></div>
                            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 mb-4 border border-yellow-100/50">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $template->name }}</h3>
                            <p class="mt-1.5 text-sm font-medium text-gray-500 line-clamp-2">{{ $template->description }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- System Templates --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-4">Standard Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($systemTemplates as $template)
                    <a href="{{ route('admin.reports.show', $template) }}" x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="bg-white rounded-2xl border border-gray-200/60 p-6 shadow-sm hover:shadow-md hover:border-blue-200 transition-all group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-blue-50 to-transparent rounded-bl-3xl opacity-50"></div>
                        <div class="w-12 h-12 bg-blue-50/80 rounded-xl flex items-center justify-center text-blue-600 mb-4 border border-blue-100/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $template->name }}</h3>
                        <p class="mt-1.5 text-sm font-medium text-gray-500 line-clamp-2">{{ $template->description }}</p>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Custom Templates --}}
        @if($userTemplates->count() > 0)
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-4">My Custom Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($userTemplates as $template)
                    <div x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase())" class="relative group">
                        <a href="{{ route('admin.reports.show', $template) }}" class="block bg-white rounded-2xl border border-gray-200/60 p-6 shadow-sm hover:shadow-md hover:border-purple-200 transition-all h-full overflow-hidden relative">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-purple-50 to-transparent rounded-bl-3xl opacity-50"></div>
                            <div class="w-12 h-12 bg-purple-50/80 rounded-xl flex items-center justify-center text-purple-600 mb-4 border border-purple-100/50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $template->name }}</h3>
                            <p class="mt-1.5 text-sm font-medium text-gray-500 line-clamp-2">{{ $template->description }}</p>
                        </a>
                        
                        <div class="absolute top-6 right-6 z-10" x-data="{ open: false }">
                            <button @click="open = true" type="button" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <x-modal id="delete-report-{{$template->id}}" maxWidth="md">
                                <x-slot:header>Delete Report</x-slot:header>
                                <div class="text-center py-4 whitespace-normal">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete {{ $template->name }}?</h3>
                                    <p class="text-sm text-gray-500">Are you sure you want to delete this custom report? This action cannot be undone.</p>
                                </div>
                                <x-slot:footer>
                                    <div class="flex items-center gap-3 w-full justify-end">
                                        <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                        <form action="{{ route('admin.reports.destroy', $template) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Report</button>
                                        </form>
                                    </div>
                                </x-slot:footer>
                            </x-modal>
                            <button x-show="!open" @click="$dispatch('open-modal', 'delete-report-{{ $template->id }}')" type="button" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Chart Initialization Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup common chart styling
            Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
            Chart.defaults.color = '#6b7280';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.9)';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            
            // 1. Revenue Chart (Bar)
            const revCtx = document.getElementById('revenueChart').getContext('2d');
            const monthlyData = @json($monthlyPayments);
            
            new Chart(revCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(d => d.label),
                    datasets: [{
                        label: 'Payments Received (RWF)',
                        data: monthlyData.map(d => d.total),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    return value >= 1000 ? (value/1000) + 'k' : value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false }
                        }
                    }
                }
            });

            // 2. Invoice Status Chart (Doughnut)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusDataRaw = @json($invoiceStatusData);
            
            const statusColors = {
                'Paid': '#10b981',
                'Overdue': '#ef4444',
                'Sent': '#3b82f6',
                'Draft': '#9ca3af',
                'Partially Paid': '#f59e0b'
            };
            
            const labels = statusDataRaw.map(d => d.status);
            const data = statusDataRaw.map(d => d.total);
            const bgColors = labels.map(l => statusColors[l] || '#6366f1');

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 15 }
                        }
                    }
                }
            });

            // 3. Payment Methods Chart (Pie)
            const methodCtx = document.getElementById('methodChart').getContext('2d');
            const methodDataRaw = @json($paymentMethodData);
            
            const methodLabels = methodDataRaw.map(d => d.payment_method ? d.payment_method.name : 'Unknown');
            const methodTotals = methodDataRaw.map(d => d.total);
            
            new Chart(methodCtx, {
                type: 'doughnut',
                data: {
                    labels: methodLabels,
                    datasets: [{
                        data: methodTotals,
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6366f1'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 15 }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.admin>
