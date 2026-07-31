<?php

$file = 'resources/views/admin/permissions/index.blade.php';
$content = file_get_contents($file);

// Replace <tr> with <tr class="group">
$content = str_replace('<tr class="hover:bg-gray-50 transition-colors">', '<tr class="group hover:bg-gray-50 transition-colors">', $content);

// Replace the Actions block
$oldActions = <<<'EOT'
                        @can('permission.view')
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        @endcan
                        
                        @can('permission.update')
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-amber-600 hover:text-amber-900">Edit</a>
                        @endcan

                        @can('permission.delete')
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this permission? This might break application logic.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900">Delete</button>
                            </form>
                        @endcan
EOT;

$newActions = <<<'EOT'
                    <div class="flex justify-end items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @can('permission.view')
                            <a href="{{ route('admin.permissions.show', $permission) }}" title="View" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        @endcan
                        
                        @can('permission.update')
                            <a href="{{ route('admin.permissions.edit', $permission) }}" title="Edit" class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        @endcan

                        @can('permission.delete')
                            <button type="button" @click="$dispatch('open-modal', 'delete-permission-{{ $permission->id }}')" title="Delete" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endcan
                    </div>
EOT;

$content = str_replace($oldActions, $newActions, $content);

// Add the modal before @empty
$oldEmpty = <<<'EOT'
                </tr>
            @empty
EOT;

$newEmpty = <<<'EOT'
                </tr>
                {{-- Delete Modal --}}
                @can('permission.delete')
                    <x-modal id="delete-permission-{{ $permission->id }}" maxWidth="md">
                        <x-slot:header>Delete Permission</x-slot:header>
                        <div class="text-center py-4">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $permission->name }}"?</h3>
                            <p class="text-sm text-gray-500">This action cannot be undone. Users relying on this permission might lose access. This might break application logic.</p>
                        </div>
                        <x-slot:footer>
                            <x-button type="ghost" @click="$dispatch('close-modal', 'delete-permission-{{ $permission->id }}')">Cancel</x-button>
                            <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-button type="danger" submit>Delete Permission</x-button>
                            </form>
                        </x-slot:footer>
                    </x-modal>
                @endcan
            @empty
EOT;

$content = str_replace($oldEmpty, $newEmpty, $content);

file_put_contents($file, $content);
echo "Permissions index updated!";
