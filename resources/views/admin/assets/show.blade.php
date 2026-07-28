@extends('layouts.admin')

@section('title', 'Asset Details - ' . $asset->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $asset->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">#{{ $asset->asset_number }} | Category: {{ $asset->category->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Assets
            </a>
            @can('asset.update', $asset)
            <a href="{{ route('admin.assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-b border-gray-200" x-data="{ tab: 'details' }">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="#" @click.prevent="tab = 'details'" :class="{'border-indigo-500 text-indigo-600': tab === 'details', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'details'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Details
                </a>
                <a href="#" @click.prevent="tab = 'depreciation'" :class="{'border-indigo-500 text-indigo-600': tab === 'depreciation', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'depreciation'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Depreciation
                </a>
                <a href="#" @click.prevent="tab = 'maintenance'" :class="{'border-indigo-500 text-indigo-600': tab === 'maintenance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'maintenance'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Maintenance
                </a>
                <a href="#" @click.prevent="tab = 'assignments'" :class="{'border-indigo-500 text-indigo-600': tab === 'assignments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'assignments'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Assignments & Transfers
                </a>
            </nav>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Details Tab -->
                <div x-show="tab === 'details'">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $asset->status }}</span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->barcode ?? '-' }}</dd>
                        </div>
                        
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : '-' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">In Service Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->in_service_date ? $asset->in_service_date->format('M d, Y') : '-' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Useful Life</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->useful_life }} months</dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Purchase Cost</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ format_currency($asset->purchase_cost) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Current Book Value</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900">{{ format_currency($asset->current_book_value) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Accumulated Depreciation</dt>
                            <dd class="mt-1 text-sm text-red-600">{{ format_currency($asset->accumulated_depreciation) }}</dd>
                        </div>

                        <div class="sm:col-span-3">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $asset->description ?? 'No description provided.' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Depreciation Tab -->
                <div x-show="tab === 'depreciation'" style="display: none;">
                    <div class="mb-4 flex justify-end">
                        @if($asset->status == 'Active' && $asset->current_book_value > $asset->residual_value)
                        <form action="{{ route('admin.asset-depreciations.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="period_end" value="{{ now()->endOfMonth()->toDateString() }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Generate Monthly Preview
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acc. Dep.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Value</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($asset->depreciations as $depreciation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $depreciation->period_date->format('M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ format_currency($depreciation->amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_currency($depreciation->accumulated_depreciation) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_currency($depreciation->book_value) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($depreciation->status == 'Preview')
                                        <form action="{{ route('admin.asset-depreciations.post', $depreciation) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900">Post</button>
                                        </form>
                                    @else
                                        <span class="text-green-600">Posted</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No depreciation records.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Maintenance Tab -->
                <div x-show="tab === 'maintenance'" style="display: none;">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Maintenance History</h3>
                        <!-- Form to add maintenance could go here -->
                    </div>
                    
                    <table class="min-w-full divide-y divide-gray-200 mt-4">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($asset->maintenances as $maintenance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->maintenance_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $maintenance->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_currency($maintenance->cost) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->status }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No maintenance records.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Assignments & Transfers -->
                <div x-show="tab === 'assignments'" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-900">Assignments</h3>
                    <ul class="divide-y divide-gray-200 mt-4 border border-gray-200 rounded-md">
                        @forelse($asset->assignments as $assignment)
                        <li class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Assigned to: {{ $assignment->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">Assigned At: {{ $assignment->assigned_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                @if($assignment->returned_at)
                                    <span class="text-xs text-gray-500">Returned: {{ $assignment->returned_at->format('M d, Y') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Current</span>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-sm text-gray-500 text-center">No assignments.</li>
                        @endforelse
                    </ul>

                    <h3 class="text-lg font-medium text-gray-900 mt-8">Transfers History</h3>
                    <div class="mt-4 border border-gray-200 rounded-md overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">From Dept</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">To Dept</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($asset->transfers as $transfer)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $transfer->transfer_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $transfer->fromDepartment->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $transfer->toDepartment->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $transfer->requestedBy ? $transfer->requestedBy->first_name . ' ' . $transfer->requestedBy->last_name : 'System' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $transfer->status == 'Approved' ? 'green' : 'yellow' }}-100 text-{{ $transfer->status == 'Approved' ? 'green' : 'yellow' }}-800">
                                            {{ $transfer->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">No transfers recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
