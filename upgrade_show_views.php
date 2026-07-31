<?php

function addDeleteModalToShow($file, $entityType) {
    if (!file_exists($file)) return;
    
    $content = file_get_contents($file);
    
    // 1. Add Delete Button to the top action bar
    $updateBtnSearch = "@can('{$entityType}.update')";
    $deleteBtnCode = <<<EOT
            @can('{$entityType}.delete')
                <x-button type="button" color="danger" @click="\$dispatch('open-modal', 'delete-{$entityType}-{{ \${$entityType}->id }}')">
                    Delete {$entityType}
                </x-button>
            @endcan
            
            @can('{$entityType}.update')
EOT;
    
    $content = str_replace($updateBtnSearch, $deleteBtnCode, $content);
    
    // 2. Add Modal Code at the bottom before </x-layouts.admin>
    $modalCode = <<<EOT
    {{-- Delete Modal --}}
    @can('{$entityType}.delete')
        <x-modal id="delete-{$entityType}-{{ \${$entityType}->id }}" maxWidth="md">
            <x-slot:header>Delete {$entityType}</x-slot:header>
            <div class="text-center py-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ \${$entityType}->name }}"?</h3>
                <p class="text-sm text-gray-500">This action cannot be undone. Users relying on this {$entityType} might lose access.</p>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="\$dispatch('close-modal', 'delete-{$entityType}-{{ \${$entityType}->id }}')">Cancel</x-button>
                <form method="POST" action="{{ route('admin.{$entityType}s.destroy', \${$entityType}) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="danger" submit>Delete {$entityType}</x-button>
                </form>
            </x-slot:footer>
        </x-modal>
    @endcan
</x-layouts.admin>
EOT;

    $content = str_replace('</x-layouts.admin>', $modalCode, $content);
    
    file_put_contents($file, $content);
    echo "Added delete modal to $file\n";
}

addDeleteModalToShow('resources/views/admin/permissions/show.blade.php', 'permission');
addDeleteModalToShow('resources/views/admin/roles/show.blade.php', 'role');
