<x-layouts.admin title="Materials Catalog">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => '#'],
                ['label' => 'Materials'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Materials & Construction Catalog</h1>
                <p class="mt-1 text-sm text-gray-500">Manage raw materials, consumables, equipment, preferred suppliers, and last purchase costs.</p>
            </div>
            @can('create', App\Models\Product::class)
                <x-button type="primary" href="{{ route('admin.inventory.products.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Material
                </x-button>
            @endcan
        </div>
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.inventory.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input
                name="search"
                placeholder="Search materials by name, SKU or barcode..."
                :value="request('search')"
                :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'"
            />

            <x-select
                name="type"
                placeholder="All Material Types"
                :options="['raw_material' => 'Raw Material', 'consumable' => 'Consumable', 'equipment' => 'Equipment / Tool', 'service' => 'Subcontractor / Service']"
                :selected="request('type')"
            />

            <div class="flex items-end gap-2 lg:col-span-2">
                <x-button type="primary" size="md">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'type']))
                    <x-button type="ghost" href="{{ route('admin.inventory.products.index') }}" size="md">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">Image</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Material Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Default Supplier</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Purchase Cost</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($products as $product)
            <tr @class(['bg-red-50/30' => $product->trashed()])>
                <td class="px-4 py-3">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-sm font-bold text-sm">
                            {{ strtoupper(substr($product->name, 0, 2)) }}
                        </div>
                    @endif
                </td>

                <td class="px-4 py-3">
                    <div>
                        <a href="{{ route('admin.inventory.products.show', $product) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $product->name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">
                            SKU: {{ $product->sku }} 
                            @if($product->unit) | Unit: {{ $product->unit->code ?? $product->unit->name }} @endif
                        </p>
                    </div>
                </td>

                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-md font-medium bg-gray-100 text-gray-700 capitalize">
                        {{ str_replace('_', ' ', $product->type) }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="text-sm text-gray-700">
                        {{ $product->defaultSupplier?->name ?? '—' }}
                    </div>
                    @if($product->supplier_sku)
                        <div class="text-xs text-gray-400">P/N: {{ $product->supplier_sku }}</div>
                    @endif
                </td>

                <td class="px-4 py-3 text-right">
                    <div class="text-sm font-semibold text-gray-900">
                        {{ $product->cost_price !== null ? number_format($product->cost_price, 0) . ' RWF' : 'Not Set' }}
                    </div>
                    <div class="text-xs text-gray-400">Valuation: {{ $product->valuation_method ?? 'FIFO' }}</div>
                </td>

                <td class="px-4 py-3">
                    <div class="text-sm text-gray-600">{{ $product->category?->name ?? '—' }}</div>
                </td>

                <td class="px-4 py-3">
                    @php
                        $statusType = match($product->status) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ ucfirst($product->status) }}</x-badge>
                </td>

                <td class="px-4 py-3 text-right">
                    <x-action-dropdown>
                        @can('view', $product)
                            <x-action-dropdown-item href="{{ route('admin.inventory.products.show', $product) }}">
                                <x-slot:icon>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </x-slot:icon>
                                View Material
                            </x-action-dropdown-item>
                        @endcan
                        @can('update', $product)
                            <x-action-dropdown-item href="{{ route('admin.inventory.products.edit', $product) }}" icon="edit">
                                Edit Material
                            </x-action-dropdown-item>
                        @endcan
                        @can('delete', $product)
                            <x-action-dropdown-item 
                                type="button" 
                                @click="open = false; $dispatch('open-delete-material-modal', { name: {{ \Illuminate\Support\Js::from($product->name) }}, sku: {{ \Illuminate\Support\Js::from($product->sku) }}, deleteUrl: {{ \Illuminate\Support\Js::from(route('admin.inventory.products.destroy', $product)) }} })" 
                                danger
                            >
                                <x-slot:icon>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </x-slot:icon>
                                Delete Material
                            </x-action-dropdown-item>
                        @endcan
                    </x-action-dropdown>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">No materials found</p>
                        @can('create', App\Models\Product::class)
                            <x-button type="primary" href="{{ route('admin.inventory.products.create') }}" class="mt-4" size="sm">
                                Add Material
                            </x-button>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $products->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    {{-- Standalone hidden delete form to guarantee proper POST method handling --}}
    <form id="global-delete-material-form" method="POST" action="" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Global Single Delete Confirmation Modal --}}
    <div x-data="{ open: false, name: '', sku: '', deleteUrl: '' }"
         x-on:open-delete-material-modal.window="open = true; name = $event.detail.name; sku = $event.detail.sku; deleteUrl = $event.detail.deleteUrl;"
         x-on:keydown.escape.window="open = false">
        
        <template x-teleport="body">
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="open = false"></div>

                {{-- Modal Body --}}
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div class="text-lg font-bold text-gray-900">Delete Material</div>
                            <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="px-6 py-4 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4 border border-rose-200">
                                <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete "<span x-text="name"></span>"?</h3>
                            <p class="text-xs text-gray-500">Are you sure you want to delete this material (<span x-text="'SKU: ' + sku"></span>)? It will be moved to archived materials.</p>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 justify-end">
                            <button type="button" @click="open = false" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-xs">
                                Cancel
                            </button>
                            <button type="button" 
                                    @click="const form = document.getElementById('global-delete-material-form'); form.action = deleteUrl; form.submit();"
                                    class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
                                Delete Material
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-layouts.admin>
