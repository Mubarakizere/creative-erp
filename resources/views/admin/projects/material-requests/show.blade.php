<x-layouts.admin title="Material Request Details">
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Material Request: {{ $materialRequest->request_number }}</h1>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @if($materialRequest->status === 'Draft' && auth()->user()->can('submit', $materialRequest))
                <form action="{{ route('admin.material-requests.submit', $materialRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">Submit for Approval</button>
                </form>
            @endif

            @if(in_array($materialRequest->status, ['Submitted', 'Under Review']) && auth()->user()->can('approve', $materialRequest))
                <form action="{{ route('admin.material-requests.approve', $materialRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">Approve</button>
                </form>
            @endif

            @if(in_array($materialRequest->status, ['Submitted', 'Under Review']) && auth()->user()->can('reject', $materialRequest))
                <form action="{{ route('admin.material-requests.reject', $materialRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">Reject</button>
                </form>
            @endif

            @if($materialRequest->status !== 'Approved' && $materialRequest->status !== 'Cancelled' && auth()->user()->can('cancel', $materialRequest))
                <form action="{{ route('admin.material-requests.cancel', $materialRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                </form>
            @endif

            @if(!$materialRequest->purchaseRequisition && auth()->user()->can('convertToProcurement', $materialRequest))
                <form action="{{ route('admin.material-requests.convert', $materialRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to convert this request to a Purchase Requisition?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Convert to Purchase Requisition</button>
                </form>
            @endif

            @can('update', $materialRequest)
                <a href="{{ route('admin.material-requests.edit', $materialRequest) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">Edit</a>
            @endcan
        </div>
    </div>

    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Project</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->project->name }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->requestedBy->name }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                        @if($materialRequest->status === 'Draft') bg-gray-100 text-gray-800 
                        @elseif(in_array($materialRequest->status, ['Submitted', 'Under Review'])) bg-yellow-100 text-yellow-800 
                        @elseif($materialRequest->status === 'Approved') bg-green-100 text-green-800 
                        @elseif(in_array($materialRequest->status, ['Rejected', 'Cancelled'])) bg-red-100 text-red-800 
                        @endif">
                        {{ $materialRequest->status }}
                    </span>
                </dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Request Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->request_date->format('Y-m-d') }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Required Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->required_date ? $materialRequest->required_date->format('Y-m-d') : 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->priority }}</dd>
            </div>
            <div class="sm:col-span-3">
                <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $materialRequest->purpose ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    @if($materialRequest->purchaseRequisition)
    <div class="mt-8 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Procurement Information</h3>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Purchase Requisition</dt>
                <dd class="mt-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                    <a href="{{ route('admin.procurement.requisitions.show', $materialRequest->purchaseRequisition) }}" class="hover:underline">
                        {{ $materialRequest->purchaseRequisition->code }}
                    </a>
                </dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Procurement Status</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">{{ $materialRequest->purchaseRequisition->status }}</dd>
            </div>
        </dl>
    </div>
    @endif

    <div class="mt-10">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Requested Items</h3>
        <div class="mt-4 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Product</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Quantity</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                                @foreach($materialRequest->items as $item)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item->product->name }} ({{ $item->product->sku }})
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                        {{ $item->quantity_requested }} {{ $item->product->unit->name ?? 'Unit' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                        {{ $item->notes ?? 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.admin>
