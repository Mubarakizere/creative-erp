<x-layouts.admin title="Approval Workflows">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Approval Workflows</h1>
            <p class="text-sm text-gray-500">Manage your company's approval processes.</p>
        </div>
        @can('create', \App\Models\ApprovalWorkflow::class)
        <a href="{{ route('admin.workflows.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            New Workflow
        </a>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Module</th>
                        <th class="px-6 py-3">Steps</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($workflows as $workflow)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $workflow->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $workflow->module }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $workflow->steps->count() }} steps
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $workflow->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.workflows.show', $workflow) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            @can('update', $workflow)
                            <a href="{{ route('admin.workflows.edit', $workflow) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endcan
                            @can('delete', $workflow)
                            <button x-data type="button" @click="$dispatch('open-modal', 'delete-workflow-{{ $workflow->id }}')" class="text-red-600 hover:text-red-900">Delete</button>

                            <x-modal id="delete-workflow-{{ $workflow->id }}" maxWidth="md">
                                <x-slot:header>Delete Workflow</x-slot:header>
                                <div class="text-center py-4">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Workflow?</h3>
                                    <p class="text-sm text-gray-500 whitespace-normal">Are you sure you want to delete the workflow <strong>{{ $workflow->name }}</strong>? This action cannot be undone.</p>
                                </div>
                                <x-slot:footer>
                                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                                    <form method="POST" action="{{ route('admin.workflows.destroy', $workflow) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="danger" submit>Delete Workflow</x-button>
                                    </form>
                                </x-slot:footer>
                            </x-modal>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No workflows found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $workflows->links() }}
        </div>
    </x-card>
</x-layouts.admin>
