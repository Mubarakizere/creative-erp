<x-layouts.admin title="My Approvals">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Approvals</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Manage requests awaiting your decision and your submitted requests.</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ tab: 'awaiting' }">
        <div class="mb-6 inline-flex space-x-1 bg-gray-100/80 p-1.5 rounded-xl border border-gray-200/50">
            <button @click="tab = 'awaiting'" :class="{ 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5': tab === 'awaiting', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50': tab !== 'awaiting' }" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">
                Awaiting My Decision ({{ $myApprovals->total() }})
            </button>
            <button @click="tab = 'submitted'" :class="{ 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5': tab === 'submitted', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50': tab !== 'submitted' }" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">
                My Requests ({{ $myRequests->total() }})
            </button>
        </div>

        <!-- Awaiting My Decision Tab -->
        <div x-show="tab === 'awaiting'" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Reference</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Workflow</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Entity</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Submitted By</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Date</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($myApprovals as $approval)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">#{{ $approval->id }}</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $approval->workflow->name }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ class_basename($approval->approvable_type) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $approval->submitter->full_name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $approval->submitted_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.approvals.show', $approval) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Review</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">No pending approvals</h3>
                                    <p class="text-sm text-gray-500 font-medium">You're all caught up! No requests are waiting for your decision.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($myApprovals->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $myApprovals->appends(['requests_page' => request('requests_page')])->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- My Requests Tab -->
        <div x-show="tab === 'submitted'" x-cloak style="display: none;">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Reference</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Workflow</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Entity</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Date</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($myRequests as $request)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">#{{ $request->id }}</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $request->workflow->name }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ class_basename($request->approvable_type) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full uppercase tracking-wider
                                        @if($request->status == 'Approved') bg-emerald-100 text-emerald-800
                                        @elseif($request->status == 'Rejected') bg-red-100 text-red-800
                                        @elseif($request->status == 'Returned for Revision') bg-orange-100 text-orange-800
                                        @else bg-blue-100 text-blue-800 @endif
                                    ">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $request->submitted_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.approvals.show', $request) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">View Details</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">No submitted requests</h3>
                                    <p class="text-sm text-gray-500 font-medium">You haven't submitted any requests for approval yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($myRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $myRequests->appends(['approvals_page' => request('approvals_page')])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
