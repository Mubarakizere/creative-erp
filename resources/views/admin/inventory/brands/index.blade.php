<x-layouts.admin title="Brands">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Brands'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Brands</h1>
            <p class="mt-1 text-sm text-gray-500">Manage product brands and manufacturers.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Create Brand Form --}}
        <div class="lg:col-span-1">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Brand</h3>
                <form action="{{ route('admin.inventory.brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <x-input name="name" label="Brand Name" required />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <x-input name="website" label="Website URL" type="url" />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            <input type="file" name="logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                        </div>
                        <x-button type="primary" submit class="w-full justify-center">Create Brand</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Brands List --}}
        <div class="lg:col-span-2">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Brands</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($brands as $brand)
                        <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center text-center">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="w-16 h-16 object-contain mb-3">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3 font-bold text-xl">
                                    {{ substr($brand->name, 0, 1) }}
                                </div>
                            @endif
                            <h4 class="font-medium text-gray-900">{{ $brand->name }}</h4>
                            @if($brand->website)
                                <a href="{{ $brand->website }}" target="_blank" class="text-xs text-blue-500 hover:underline mt-1">{{ parse_url($brand->website, PHP_URL_HOST) }}</a>
                            @endif
                            <div class="mt-4 pt-3 border-t border-gray-100 w-full flex justify-end">
                                <form action="{{ route('admin.inventory.brands.destroy', $brand) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-500">No brands found.</div>
                    @endforelse
                </div>
                <div class="mt-4">
                    {{ $brands->links('components.pagination') }}
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
