<x-layouts.admin title="Create Pipeline">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Create Pipeline</h1></div>
        <x-button type="ghost" href="{{ route('admin.crm.pipelines.index') }}" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.pipelines.store') }}">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                @endif
                <x-input name="name" label="Pipeline Name" required />
            </div>
            
            <div class="mt-4 flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Set as Default Pipeline</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.pipelines.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Create Pipeline</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
