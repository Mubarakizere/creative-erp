<x-layouts.admin title="Workflow Details">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.workflows.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Workflows</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $workflow->name }}</h1>
            <p class="text-sm text-gray-500">Module: {{ $workflow->module }}</p>
        </div>
        @can('update', $workflow)
        <a href="{{ route('admin.workflows.edit', $workflow) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            Edit Workflow
        </a>
        @endcan
    </div>

    <x-card class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
        <p class="text-gray-700">{{ $workflow->description ?: 'No description provided.' }}</p>
        <div class="mt-4">
            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $workflow->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $workflow->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </x-card>

    <x-card>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Steps</h3>
        <div class="space-y-4">
            @forelse($workflow->steps as $step)
            <div class="p-4 border rounded-lg bg-gray-50 flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center">
                    {{ $step->step_order }}
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">{{ $step->name }}</h4>
                    <p class="text-sm text-gray-500">
                        Assigned to: 
                        @if($step->approver_user_id)
                            {{ $step->user->full_name }} (User)
                        @elseif($step->approver_role_id)
                            {{ $step->role->name }} (Role)
                        @else
                            Unassigned
                        @endif
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">No steps defined for this workflow.</p>
            @endforelse
        </div>
    </x-card>
</x-layouts.admin>
