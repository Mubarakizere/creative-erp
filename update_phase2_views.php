<?php

$dirs = [
    'resources/views/admin/procurement/pos',
    'resources/views/admin/procurement/receipts',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$views = [
    'rfqs/create.blade.php' => <<<'EOT'
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
EOT,
    'requisitions/compare.blade.php' => <<<'EOT'
<x-layouts.admin title="Compare Quotations">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Compare Quotations: {{ $requisition->code }}</h1>
        <div class="mt-6 grid grid-cols-3 gap-6">
            @foreach($requisition->quotations as $quotation)
            <div class="border p-4 rounded shadow-sm">
                <h3 class="font-bold text-lg">{{ $quotation->supplier?->name }}</h3>
                <p>Status: {{ $quotation->status }}</p>
                <div class="mt-4">
                    @foreach($quotation->items as $item)
                        <div class="mb-2">
                            <span class="font-semibold">{{ $item->product?->name }}</span><br>
                            Qty: {{ $item->quantity }} | Price: ${{ $item->unit_price }}<br>
                            Discount: ${{ $item->discount }} | Tax: ${{ $item->tax }}<br>
                            <strong>Total: ${{ $item->total }}</strong>
                        </div>
                    @endforeach
                </div>
                @if($quotation->status === 'draft')
                <form action="{{ route('admin.procurement.requisitions.accept', [$requisition->id, $quotation->id]) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded text-center block">Accept & Generate PO</button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
EOT,
    'pos/index.blade.php' => <<<'EOT'
<x-layouts.admin title="Purchase Orders">
    <div class="px-4 py-8">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold">Purchase Orders</h1>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Order Date</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pos as $po)
                <tr>
                    <td>{{ $po->code }}</td>
                    <td>{{ $po->supplier?->name }}</td>
                    <td>{{ $po->order_date }}</td>
                    <td>{{ $po->status }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
    'pos/show.blade.php' => <<<'EOT'
<x-layouts.admin title="Purchase Order">
    <div class="px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Purchase Order: {{ $po->code }}</h1>
            <div>
                @if($po->status === 'draft')
                <form action="{{ route('admin.procurement.pos.approve', $po->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Approve PO</button>
                </form>
                @elseif($po->status === 'approved')
                <a href="{{ route('admin.procurement.receipts.create', ['po_id' => $po->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded">Receive Goods</a>
                @endif
            </div>
        </div>
        <div class="mt-6">
            <p><strong>Supplier:</strong> {{ $po->supplier?->name }}</p>
            <p><strong>Status:</strong> {{ $po->status }}</p>
            <p><strong>Order Date:</strong> {{ $po->order_date }}</p>
        </div>
        <h3 class="text-lg font-bold mt-6">Items</h3>
        <table class="w-full mt-2">
            <thead>
                <tr>
                    <th class="text-left">Product</th>
                    <th class="text-left">Qty</th>
                    <th class="text-left">Unit Price</th>
                    <th class="text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ $item->unit_price }}</td>
                    <td>${{ $item->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
    'receipts/index.blade.php' => <<<'EOT'
<x-layouts.admin title="Goods Receipts">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Goods Receipts</h1>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">PO</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $gr)
                <tr>
                    <td>{{ $gr->code }}</td>
                    <td>{{ $gr->purchaseOrder?->code }}</td>
                    <td>{{ $gr->purchaseOrder?->supplier?->name }}</td>
                    <td>{{ $gr->receipt_date }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.receipts.show', $gr->id) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
    'receipts/create.blade.php' => <<<'EOT'
<x-layouts.admin title="Receive Goods">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Receive Goods against {{ $po->code }}</h1>
        <form action="{{ route('admin.procurement.receipts.store') }}" method="POST" class="mt-6">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label>Receipt Date</label>
                    <input type="date" name="receipt_date" class="w-full border p-2" required>
                </div>
                <div class="w-1/2">
                    <label>Delivery Note Number</label>
                    <input type="text" name="delivery_note_number" class="w-full border p-2">
                </div>
            </div>
            <div class="mb-4">
                <label>Warehouse to Receive Into</label>
                <select name="warehouse_id" class="w-full border p-2" required>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <h3 class="font-bold mt-4">Items to Receive</h3>
            <table class="w-full mt-2">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Ordered Qty</th>
                        <th class="text-left">Received (Good)</th>
                        <th class="text-left">Rejected (Damaged)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $index => $item)
                    <tr>
                        <td>
                            {{ $item->product?->name }}
                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td><input type="number" name="items[{{ $index }}][received_quantity]" class="border p-2 w-24" value="{{ $item->quantity }}" required></td>
                        <td><input type="number" name="items[{{ $index }}][rejected_quantity]" class="border p-2 w-24" value="0" required></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit Receipt (Updates Stock)</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
EOT,
    'receipts/show.blade.php' => <<<'EOT'
<x-layouts.admin title="View Receipt">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Goods Receipt: {{ $receipt->code }}</h1>
        <div class="mt-4">
            <p><strong>Purchase Order:</strong> {{ $receipt->purchaseOrder?->code }}</p>
            <p><strong>Warehouse:</strong> {{ $receipt->warehouse?->name }}</p>
            <p><strong>Date:</strong> {{ $receipt->receipt_date }}</p>
            <p><strong>Delivery Note:</strong> {{ $receipt->delivery_note_number }}</p>
        </div>
        <h3 class="text-lg font-bold mt-6">Items Received</h3>
        <table class="w-full mt-2">
            <thead>
                <tr>
                    <th class="text-left">Product</th>
                    <th class="text-left">Accepted Qty</th>
                    <th class="text-left">Rejected Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipt->items as $item)
                <tr>
                    <td>{{ $item->purchaseOrderItem?->product?->name }}</td>
                    <td class="text-green-600">{{ $item->received_quantity }}</td>
                    <td class="text-red-600">{{ $item->rejected_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
];

foreach ($views as $filename => $content) {
    file_put_contents("resources/views/admin/procurement/$filename", $content);
    echo "Created resources/views/admin/procurement/$filename\n";
}
