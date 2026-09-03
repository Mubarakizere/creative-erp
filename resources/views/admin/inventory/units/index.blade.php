<x-layouts.admin title="Units of Measure">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Units of Measure'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\UnitOfMeasure::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Units of Measure</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage measurement units for products and inventory.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Create UoM Form --}}
        <div class="lg:col-span-1">
            @can('create', App\Models\UnitOfMeasure::class)
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden sticky top-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Add New Unit</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.inventory.units.store') }}" method="POST" class="space-y-4" id="unit-form">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Unit Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" placeholder="e.g. Kilogram" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="abbreviation" class="block text-sm font-medium text-gray-700 mb-1">Abbreviation <span class="text-red-500">*</span></label>
                            <input type="text" name="abbreviation" id="abbreviation" placeholder="e.g. kg" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                            @error('abbreviation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3" placeholder="Optional details..." class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors"></textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none min-h-[42px]">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Create Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-gray-50 rounded-2xl border border-gray-200/60 p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <p class="text-sm font-medium text-gray-500">You do not have permission to add new units of measure.</p>
            </div>
            @endcan
        </div>

        {{-- UoMs List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Active Units</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Name</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Abbreviation</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Description</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($uoms as $unit)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-900">{{ $unit->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 font-mono border border-blue-100">
                                            {{ $unit->abbreviation }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <span class="text-sm text-gray-500">{{ Str::limit($unit->description, 50) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @can('delete', $unit)
                                        <x-action-dropdown>
                                            <x-action-dropdown-item onclick="Alpine.$data(document.getElementById('modal-delete-unit-{{ $unit->id }}')).open = true" icon="delete" variant="danger">
                                                Delete Unit
                                            </x-action-dropdown-item>
                                        </x-action-dropdown>

                                        <div id="modal-delete-unit-{{ $unit->id }}">
                                            <x-modal id="delete-unit-{{ $unit->id }}" maxWidth="md">
                                                <x-slot:header>Delete Unit</x-slot:header>

                                                <div class="text-center py-4 whitespace-normal">
                                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $unit->name }}"?</h3>
                                                    <p class="text-sm text-gray-500">Are you sure you want to delete this unit of measure? This action cannot be undone and may affect associated inventory items.</p>
                                                </div>

                                                <x-slot:footer>
                                                    <div class="flex items-center gap-3 w-full justify-end">
                                                        <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                        <form method="POST" action="{{ route('admin.inventory.units.destroy', $unit) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Unit</button>
                                                        </form>
                                                    </div>
                                                </x-slot:footer>
                                            </x-modal>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">No units of measure found</h3>
                                        <p class="text-sm text-gray-500 font-medium">Add your first unit using the form to the left.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($uoms, 'hasPages') && $uoms->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $uoms->links('components.pagination') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view units of measure.</p>
    </div>
    @endcan
</x-layouts.admin>
