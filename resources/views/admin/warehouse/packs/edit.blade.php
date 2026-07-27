<x-layouts.admin>
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Record Packing Details</h1>
            <p class="text-sm text-gray-500 mt-1">Package: {{ $packing->packing_number }}</p>
        </div>
        <a href="{{ route('admin.warehouse.packing.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            &larr; Back to List
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Package Information</h3>
            <p class="text-sm text-gray-500">Record the final metrics for this physical package.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.warehouse.packing.update', $packing) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Weight -->
                <div class="space-y-1.5">
                    <label for="total_weight" class="block text-sm font-medium text-gray-700">Total Weight (kg) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" step="0.01" name="total_weight" id="total_weight" required class="block w-full rounded-lg border {{ $errors->has('total_weight') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm text-sm py-2.5 px-3">
                    </div>
                    @error('total_weight')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dimensions -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <label for="length" class="block text-sm font-medium text-gray-700">Length (cm)</label>
                        <input type="number" step="0.01" name="length" id="length" class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3">
                    </div>
                    <div class="space-y-1.5">
                        <label for="width" class="block text-sm font-medium text-gray-700">Width (cm)</label>
                        <input type="number" step="0.01" name="width" id="width" class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3">
                    </div>
                    <div class="space-y-1.5">
                        <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
                        <input type="number" step="0.01" name="height" id="height" class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3">
                    </div>
                </div>
                <p class="text-xs text-gray-500">Dimensions are optional but recommended for shipping carrier calculations.</p>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Packing Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 px-3" placeholder="Any special instructions or observations during packing?"></textarea>
                </div>

                <!-- Actions -->
                <div class="pt-4 flex justify-end gap-3 border-t border-gray-200">
                    <a href="{{ route('admin.warehouse.packing.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Complete Packing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layouts.admin>
