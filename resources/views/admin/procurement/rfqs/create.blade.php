<x-layouts.admin title="Create Quotation">
    <div class="px-4 py-8" x-data="{ items: [{ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 }] }">
        <h1 class="text-2xl font-bold">Record Quotation</h1>
        <form action="{{ route('admin.procurement.rfqs.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label>Quotation Number</label>
                <input type="text" name="code" class="w-full border p-2" required>
            </div>
            <div class="mb-4">
                <label>Supplier</label>
                <select name="supplier_id" class="w-full border p-2" required>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label>From Purchase Requisition (Approved)</label>
                <select name="purchase_requisition_id" class="w-full border p-2">
                    <option value="">-- None --</option>
                    @foreach($requisitions as $pr)
                        <option value="{{ $pr->id }}">{{ $pr->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label>Issue Date</label>
                    <input type="date" name="issue_date" class="w-full border p-2" required>
                </div>
                <div class="w-1/2">
                    <label>Valid Until</label>
                    <input type="date" name="valid_until" class="w-full border p-2" required>
                </div>
            </div>
            <h3 class="font-bold mt-4">Items (Prices, Discounts, Taxes)</h3>
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-4 mt-2">
                    <select :name="`items[${index}][product_id]`" class="border p-2 w-1/4">
                        @foreach(\App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" :name="`items[${index}][quantity]`" class="border p-2 w-24" placeholder="Qty">
                    <input type="number" :name="`items[${index}][unit_price]`" class="border p-2 w-32" placeholder="Price">
                    <input type="number" :name="`items[${index}][discount]`" class="border p-2 w-32" placeholder="Discount">
                    <input type="number" :name="`items[${index}][tax]`" class="border p-2 w-32" placeholder="Tax">
                </div>
            </template>
            <button type="button" @click="items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 })" class="text-blue-600 mt-2 text-sm">+ Add Item</button>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Record Quotation</button>
            </div>
        </form>
    </div>
</x-layouts.admin>