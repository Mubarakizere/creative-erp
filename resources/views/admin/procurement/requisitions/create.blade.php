<x-layouts.admin title="Create Requisition">
    <div class="px-4 py-8" x-data="{ items: [{ product_id: '', quantity: 1 }] }">
        <h1 class="text-2xl font-bold mb-4">Create Purchase Requisition</h1>
        <form action="{{ route('admin.procurement.requisitions.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label>Code</label>
                <input type="text" name="code" class="w-full border p-2" required>
            </div>
            <div class="mb-4">
                <label>Status</label>
                <select name="status" class="w-full border p-2">
                    <option value="draft">Draft</option>
                    <option value="submitted">Submit for Approval</option>
                </select>
            </div>
            
            <h3 class="font-bold mt-4">Items</h3>
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-4 mt-2">
                    <select :name="`items[${index}][product_id]`" class="border p-2 w-1/2">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" :name="`items[${index}][quantity]`" class="border p-2 w-1/4" placeholder="Quantity">
                </div>
            </template>
            <button type="button" @click="items.push({ product_id: '', quantity: 1 })" class="text-blue-600 mt-2 text-sm">+ Add Item</button>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Requisition</button>
            </div>
        </form>
    </div>
</x-layouts.admin>