<x-layouts.admin title="Units of Measure">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Units of Measure'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Units of Measure</h1>
            <p class="mt-1 text-sm text-gray-500">Manage measurement units for products.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Create UoM Form --}}
        <div class="lg:col-span-1">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Unit</h3>
                <form action="{{ route('admin.inventory.units.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <x-input name="name" label="Unit Name" placeholder="e.g. Kilogram" required />
                        <x-input name="abbreviation" label="Abbreviation" placeholder="e.g. kg" required />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <x-button type="primary" submit class="w-full justify-center">Create Unit</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- UoMs List --}}
        <div class="lg:col-span-2">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Units of Measure</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Abbreviation</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($uoms as $unit)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-sm text-gray-900 font-medium">{{ $unit->name }}</td>
                                    <td class="py-2 px-3 text-sm font-mono bg-gray-50 rounded px-1">{{ $unit->abbreviation }}</td>
                                    <td class="py-2 px-3 text-sm text-gray-500">{{ Str::limit($unit->description, 50) }}</td>
                                    <td class="py-2 px-3 text-right">
                                        <form action="{{ route('admin.inventory.units.destroy', $unit) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-sm text-gray-500">No units found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $uoms->links('components.pagination') }}
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
