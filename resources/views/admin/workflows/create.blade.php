<x-layouts.admin title="Create Workflow">
    <div class="mb-6">
        <a href="{{ route('admin.workflows.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Workflows</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create New Workflow</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.workflows.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                    <select name="module" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="TimeTracking">Time Tracking</option>
                        <option value="Documents">Documents</option>
                        <option value="Expenses">Expenses</option>
                        <option value="Leaves">Leaves</option>
                        <option value="Invoice">Invoice</option>
                        <option value="Refund">Refund</option>
                        <option value="CreditNote">Credit Note</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Active Workflow</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Steps</h3>
                
                <div id="steps-container" class="space-y-4">
                    <!-- Step 1 -->
                    <div class="p-4 border rounded-lg bg-gray-50 flex gap-4 items-start">
                        <div class="font-bold text-gray-500 mt-2">1.</div>
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="hidden" name="steps[0][step_order]" value="1">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Step Name</label>
                                <input type="text" name="steps[0][name]" placeholder="e.g. Manager Approval" class="w-full text-sm rounded-md border-gray-300" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Approver Role</label>
                                <select name="steps[0][approver_role_id]" class="w-full text-sm rounded-md border-gray-300">
                                    <option value="">Specific User Instead</option>
                                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Approver User</label>
                                <select name="steps[0][approver_user_id]" class="w-full text-sm rounded-md border-gray-300">
                                    <option value="">Role Based Instead</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" onclick="addStep()" class="mt-4 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    + Add Another Step
                </button>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.workflows.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Workflow</button>
            </div>
        </form>
    </x-card>

    <script>
        let stepCount = 1;
        function addStep() {
            stepCount++;
            const html = `
            <div class="p-4 border rounded-lg bg-gray-50 flex gap-4 items-start mt-4">
                <div class="font-bold text-gray-500 mt-2">${stepCount}.</div>
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="steps[${stepCount-1}][step_order]" value="${stepCount}">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Step Name</label>
                        <input type="text" name="steps[${stepCount-1}][name]" class="w-full text-sm rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Approver Role</label>
                        <select name="steps[${stepCount-1}][approver_role_id]" class="w-full text-sm rounded-md border-gray-300">
                            <option value="">Specific User Instead</option>
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Approver User</label>
                        <select name="steps[${stepCount-1}][approver_user_id]" class="w-full text-sm rounded-md border-gray-300">
                            <option value="">Role Based Instead</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>`;
            document.getElementById('steps-container').insertAdjacentHTML('beforeend', html);
        }
    </script>
</x-layouts.admin>
