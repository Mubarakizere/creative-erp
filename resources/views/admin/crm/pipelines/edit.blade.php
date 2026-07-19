<x-layouts.admin title="Edit Pipeline">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => 'Edit']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Edit Pipeline</h1></div>
        <x-button type="ghost" href="{{ route('admin.crm.pipelines.index') }}" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.pipelines.update', $pipeline) }}">
        @csrf
        @method('PUT')
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-input name="name" label="Pipeline Name" :value="$pipeline->name" required />
            </div>
            
            <div class="mt-4 flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" {{ $pipeline->is_default ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Set as Default Pipeline</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $pipeline->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.pipelines.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Update Pipeline</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
