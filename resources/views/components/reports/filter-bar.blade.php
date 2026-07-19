@props(['action', 'template' => null])

<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
    <form action="{{ $action }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <x-input 
                name="date_from" 
                type="date" 
                label="Date From" 
                :value="request('date_from', $template?->filters['date_from'] ?? '')" 
            />
            
            <x-input 
                name="date_to" 
                type="date" 
                label="Date To" 
                :value="request('date_to', $template?->filters['date_to'] ?? '')" 
            />
            
            @php
                $currentStatus = request('status', $template?->filters['status'] ?? '');
                $currentStatus = is_array($currentStatus) ? ($currentStatus[0] ?? '') : $currentStatus;
            @endphp
            <x-select 
                name="status" 
                label="Status" 
                :options="['' => 'All Statuses', 'Pending' => 'Pending', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled']" 
                :selected="$currentStatus" 
            />
            
            <div>
                <button type="submit" class="w-full bg-blue-600 border border-transparent rounded-lg shadow-sm py-2.5 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>
