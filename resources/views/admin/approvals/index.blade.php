<x-layouts.admin title="My Approvals">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Approvals</h1>
        <p class="text-sm text-gray-500">Manage requests awaiting your decision and your submitted requests.</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ tab: 'awaiting' }">
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button @click="tab = 'awaiting'" :class="{ 'border-blue-500 text-blue-600': tab === 'awaiting', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'awaiting' }" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Awaiting My Decision ({{ $myApprovals->total() }})
                </button>
                <button @click="tab = 'submitted'" :class="{ 'border-blue-500 text-blue-600': tab === 'submitted', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'submitted' }" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    My Requests ({{ $myRequests->total() }})
                </button>
            </nav>
        </div>

        <!-- Awaiting My Decision Tab -->
        <div x-show="tab === 'awaiting'">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                            <tr>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Workflow</th>
                                <th class="px-6 py-3">Entity</th>
                                <th class="px-6 py-3">Submitted By</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($myApprovals as $approval)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $approval->id }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $approval->workflow->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ class_basename($approval->approvable_type) }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $approval->submitter->full_name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $approval->submitted_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.approvals.show', $approval) }}" class="text-blue-600 hover:text-blue-900">Review</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No requests awaiting your approval.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $myApprovals->appends(['requests_page' => request('requests_page')])->links() }}
                </div>
            </x-card>
        </div>

        <!-- My Requests Tab -->
        <div x-show="tab === 'submitted'" style="display: none;">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                            <tr>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Workflow</th>
                                <th class="px-6 py-3">Entity</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($myRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $request->id }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $request->workflow->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ class_basename($request->approvable_type) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($request->status == 'Approved') bg-emerald-100 text-emerald-700
                                        @elseif($request->status == 'Rejected') bg-red-100 text-red-700
                                        @elseif($request->status == 'Returned for Revision') bg-orange-100 text-orange-700
                                        @else bg-blue-100 text-blue-700 @endif
                                    ">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $request->submitted_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.approvals.show', $request) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">You haven't submitted any requests.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $myRequests->appends(['approvals_page' => request('approvals_page')])->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
