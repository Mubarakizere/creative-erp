<?php

$dirs = [
    'resources/views/admin/procurement/suppliers',
    'resources/views/admin/procurement/requisitions',
    'resources/views/admin/procurement/rfqs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$views = [
    'suppliers/index.blade.php' => <<<'EOT'
<x-layouts.admin title="Suppliers">
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Suppliers</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all suppliers including their code, name, and status.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="{{ route('admin.procurement.suppliers.create') }}" class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Add Supplier</a>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Code</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Preferred</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($suppliers as $supplier)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $supplier->code }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $supplier->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $supplier->email }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $supplier->is_preferred ? 'Yes' : 'No' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
EOT,
    'suppliers/create.blade.php' => <<<'EOT'
<x-layouts.admin title="Add Supplier">
    <div class="px-4 py-8">
        <form action="{{ route('admin.procurement.suppliers.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Code</label>
                    <input type="text" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_preferred" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2">Is Preferred Supplier</span>
                    </label>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
EOT,
    'requisitions/index.blade.php' => <<<'EOT'
<x-layouts.admin title="Purchase Requisitions">
    <div class="px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Purchase Requisitions</h1>
            <a href="{{ route('admin.procurement.requisitions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Requisition</a>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Requested By</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisitions as $pr)
                <tr>
                    <td>{{ $pr->code }}</td>
                    <td>{{ $pr->status }}</td>
                    <td>{{ $pr->requestedBy?->name }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.requisitions.show', $pr->id) }}" class="text-blue-600">View</a>
                        @if($pr->status === 'submitted')
                        <form action="{{ route('admin.procurement.requisitions.approve', $pr->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-600 ml-2">Approve</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
    'requisitions/create.blade.php' => <<<'EOT'
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
EOT,
    'requisitions/show.blade.php' => <<<'EOT'
<x-layouts.admin title="View Requisition">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Requisition: {{ $requisition->code }}</h1>
        <p>Status: {{ $requisition->status }}</p>
        <p>Requested By: {{ $requisition->requestedBy?->name }}</p>

        <h3 class="text-lg font-bold mt-6">Items</h3>
        <ul>
            @foreach($requisition->items as $item)
                <li>{{ $item->product?->name }} - Qty: {{ $item->quantity }}</li>
            @endforeach
        </ul>
    </div>
</x-layouts.admin>
EOT,
    'rfqs/index.blade.php' => <<<'EOT'
<x-layouts.admin title="RFQs">
    <div class="px-4 py-8">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold">Request for Quotations</h1>
            <a href="{{ route('admin.procurement.rfqs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New RFQ</a>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Number</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Issue Date</th>
                    <th class="text-left">Valid Until</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rfqs as $rfq)
                <tr>
                    <td>{{ $rfq->quotation_number }}</td>
                    <td>{{ $rfq->supplier?->name }}</td>
                    <td>{{ $rfq->issue_date }}</td>
                    <td>{{ $rfq->valid_until }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
EOT,
    'rfqs/create.blade.php' => <<<'EOT'
<x-layouts.admin title="Create RFQ">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Generate RFQ</h1>
        <form action="{{ route('admin.procurement.rfqs.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label>Quotation Number</label>
                <input type="text" name="quotation_number" class="w-full border p-2" required>
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
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
        </form>
    </div>
</x-layouts.admin>
EOT,
];

foreach ($views as $filename => $content) {
    file_put_contents("resources/views/admin/procurement/$filename", $content);
    echo "Created resources/views/admin/procurement/$filename\n";
}
