<x-layouts.admin title="Request Details">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.approvals.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Approvals</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Request #{{ $approval->id }}</h1>
            <p class="text-sm text-gray-500">Workflow: {{ $approval->workflow->name }}</p>
        </div>
        <div>
            <span class="px-3 py-1.5 text-sm font-medium rounded-full 
                @if($approval->status == 'Approved') bg-emerald-100 text-emerald-800
                @elseif($approval->status == 'Rejected') bg-red-100 text-red-800
                @elseif($approval->status == 'Returned for Revision') bg-orange-100 text-orange-800
                @elseif($approval->status == 'Cancelled') bg-gray-100 text-gray-800
                @else bg-blue-100 text-blue-800 @endif
            ">
                {{ $approval->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Request Information</h3>
                
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div>
                        <span class="block text-gray-500 mb-1">Entity Type</span>
                        <span class="font-medium text-gray-900">{{ class_basename($approval->approvable_type) }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">Submitted By</span>
                        <span class="font-medium text-gray-900">{{ $approval->submitter->full_name ?? 'System' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">Date Submitted</span>
                        <span class="font-medium text-gray-900">{{ $approval->submitted_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($approval->completed_at)
                    <div>
                        <span class="block text-gray-500 mb-1">Date Completed</span>
                        <span class="font-medium text-gray-900">{{ $approval->completed_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </x-card>

            <x-card>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approval History</h3>
                
                <div class="space-y-4">
                    @forelse($approval->actions as $action)
                    <div class="flex items-start gap-4 p-4 border rounded-lg bg-gray-50">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            @if($action->action == 'Approve') bg-emerald-100 text-emerald-600
                            @elseif($action->action == 'Reject') bg-red-100 text-red-600
                            @elseif($action->action == 'Return') bg-orange-100 text-orange-600
                            @elseif($action->action == 'Cancel') bg-gray-100 text-gray-600
                            @elseif($action->action == 'Submit') bg-blue-100 text-blue-600
                            @endif
                        ">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($action->action == 'Approve') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @elseif($action->action == 'Reject') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                @elseif($action->action == 'Return') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                @elseif($action->action == 'Cancel') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                @elseif($action->action == 'Submit') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h4 class="font-medium text-gray-900">{{ $action->user->full_name }} <span class="text-gray-500 font-normal">performed</span> {{ $action->action }}</h4>
                                <span class="text-xs text-gray-500">{{ $action->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($action->step)
                            <p class="text-xs text-gray-500 mt-1">Step: {{ $action->step->name }}</p>
                            @endif
                            @if($action->comment)
                            <div class="mt-2 text-sm text-gray-700 bg-white p-3 border rounded">
                                {{ $action->comment }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No actions recorded yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div>
            @if($approval->status === 'Pending Approval')
                @if(auth()->user()->can('approve', $approval))
                <x-card class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Take Action</h3>
                    
                    <form action="{{ route('admin.approvals.action', $approval) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comments (Optional)</label>
                            <textarea name="comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Provide any feedback or reason..."></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <button type="submit" name="action" value="approve" class="w-full py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">Approve</button>
                            <button type="submit" name="action" value="reject" class="w-full py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Reject</button>
                        </div>
                        <button type="submit" name="action" value="return" class="w-full py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">Return for Revision</button>
                    </form>
                </x-card>
                @endif
                
                @if(auth()->user()->can('cancel', $approval))
                <x-card class="mb-6 border-red-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Request</h3>
                    <p class="text-sm text-gray-500 mb-4">You can cancel this request since it is still pending.</p>
                    
                    <form action="{{ route('admin.approvals.action', $approval) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                        @csrf
                        <input type="hidden" name="comment" value="Cancelled by requester">
                        <button type="submit" name="action" value="cancel" class="w-full py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium">Cancel Request</button>
                    </form>
                </x-card>
                @endif
            @elseif($approval->status === 'Returned for Revision' && auth()->user()->can('submit', $approval))
                <x-card class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Resubmit Request</h3>
                    <p class="text-sm text-gray-500 mb-4">This request was returned. After addressing the feedback, you can resubmit.</p>
                    
                    <form action="{{ route('admin.approvals.action', $approval) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea name="comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Note what was changed..."></textarea>
                        </div>
                        <button type="submit" name="action" value="resubmit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Resubmit Request</button>
                    </form>
                </x-card>
            @endif

            <x-card>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Path</h3>
                
                <div class="relative">
                    @foreach($approval->workflow->steps as $index => $step)
                    <div class="flex items-start mb-6 last:mb-0">
                        <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                            @if($approval->current_step_id == $step->id && $approval->status == 'Pending Approval') bg-blue-100 text-blue-600 ring-2 ring-blue-500 ring-offset-2
                            @elseif($approval->completed_at || $step->step_order < ($approval->currentStep->step_order ?? 999)) bg-emerald-100 text-emerald-600
                            @else bg-gray-100 text-gray-400 @endif
                        ">
                            {{ $step->step_order }}
                        </div>
                        <div class="ml-4">
                            <h4 class="font-medium text-sm text-gray-900">{{ $step->name }}</h4>
                            <p class="text-xs text-gray-500">
                                @if($step->approver_user_id) {{ $step->user->full_name }}
                                @elseif($step->approver_role_id) {{ $step->role->name }} Role
                                @endif
                            </p>
                        </div>
                        @if(!$loop->last)
                        <div class="absolute top-8 left-4 bottom-[-1.5rem] w-px bg-gray-200"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
