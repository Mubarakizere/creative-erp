<x-layouts.admin title="Equipment Maintenance">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Maintenance'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Maintenance Logs</h1>
                <p class="mt-1 text-sm text-gray-500">Track maintenance and repair records for all equipment.</p>
            </div>
            @can('create', App\Models\AssetMaintenance::class)
                <x-button type="primary" @click="$dispatch('open-modal', 'create-maintenance')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Log Maintenance
                </x-button>
            @endcan
        </div>
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipment</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Description</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Vendor</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cost</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell">Next Due</th>
        </x-slot:head>

        @forelse($maintenances as $m)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $m->asset->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $m->asset->asset_code ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $m->maintenance_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500">{{ Str::limit($m->description, 40) }}</td>
                <td class="px-4 py-3 hidden lg:table-cell text-sm text-gray-500">{{ $m->vendor ?? '—' }}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ number_format($m->cost, 2) }}</td>
                <td class="px-4 py-3">
                    <x-badge :type="match($m->status) { 'Completed' => 'success', 'In Progress' => 'warning', 'Scheduled' => 'info', default => 'default' }">
                        {{ $m->status }}
                    </x-badge>
                </td>
                <td class="px-4 py-3 hidden xl:table-cell text-sm text-gray-500">
                    {{ $m->next_maintenance_date ? $m->next_maintenance_date->format('M d, Y') : '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                    No maintenance records found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $maintenances->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    {{-- Create Maintenance Modal --}}
    @can('create', App\Models\AssetMaintenance::class)
    <x-modal id="create-maintenance" maxWidth="lg">
        <x-slot:header>Log Equipment Maintenance</x-slot:header>
        <form method="POST" action="{{ route('admin.asset-maintenances.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                    <select name="asset_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select equipment...</option>
                        @foreach(\App\Models\Asset::where('company_id', auth()->user()->company_id)->orderBy('name')->get() as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="maintenance_date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="Scheduled">Scheduled</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Describe the maintenance work..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                        <input type="text" name="vendor" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Service provider">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                        <input type="number" name="cost" step="0.01" min="0" value="0" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Next Maintenance Due</label>
                    <input type="date" name="next_maintenance_date" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Save Record</x-button>
            </x-slot:footer>
        </form>
    </x-modal>
    @endcan
</x-layouts.admin>
