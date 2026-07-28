@extends('layouts.admin')

@section('title', 'Fixed Assets')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Fixed Assets</h1>
            <p class="mt-1 text-sm text-gray-500">Manage company assets, depreciation, and assignments.</p>
        </div>
        <div class="flex items-center space-x-3">
            @can('asset_category.view')
            <a href="{{ route('admin.asset-categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-tags mr-2"></i> Categories
            </a>
            @endcan
            @can('asset.create')
            <a href="{{ route('admin.assets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Add Asset
            </a>
            @endcan
        </div>
    </div>

    <!-- Metrics Cards (Placeholder logic) -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @inject('metricsService', 'App\Services\Metrics\MetricsService')
        @php $metrics = collect($metricsService->getCards(['company_id' => auth()->user()->company_id]))->filter(fn($c) => $c['title'] === 'Total Assets' || $c['title'] === 'Net Book Value' || $c['title'] === 'Under Maintenance' || $c['title'] === 'Monthly Depreciation'); @endphp
        
        @foreach($metrics as $metric)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-md bg-{{ $metric['color'] }}-100 p-3">
                            <i class="fas fa-{{ $metric['icon'] }} text-{{ $metric['color'] }}-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ $metric['title'] }}</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $metric['value'] }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <form method="GET" action="{{ route('admin.assets.index') }}" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Search</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by name, number, or serial...">
                </div>
            </div>
            
            <div class="w-full sm:w-48">
                <select name="category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Under Maintenance" {{ request('status') == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="Fully Depreciated" {{ request('status') == 'Fully Depreciated' ? 'selected' : '' }}>Fully Depreciated</option>
                    <option value="Disposed" {{ request('status') == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Asset List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg">
                                <i class="fas fa-box text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.assets.show', $asset) }}" class="hover:text-indigo-600">{{ $asset->name }}</a>
                                </div>
                                <div class="text-sm text-gray-500">
                                    #{{ $asset->asset_number }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $asset->category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ format_currency($asset->current_book_value) }}</div>
                        <div class="text-xs text-gray-500">Cost: {{ format_currency($asset->purchase_cost) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($asset->assignedUser)
                            <div class="flex items-center">
                                {!! \App\Helpers\UIHelper::avatar($asset->assignedUser, 'sm') !!}
                                <span class="ml-2 text-sm text-gray-900">{{ $asset->assignedUser->name }}</span>
                            </div>
                        @else
                            <span class="text-sm text-gray-500">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($asset->status == 'Active') bg-green-100 text-green-800
                            @elseif($asset->status == 'Draft') bg-gray-100 text-gray-800
                            @elseif($asset->status == 'Under Maintenance') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $asset->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                        @can('asset.update', $asset)
                        <a href="{{ route('admin.assets.edit', $asset) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        No assets found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $assets->links() }}
    </div>
</div>
@endsection
