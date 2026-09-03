<x-layouts.admin title="Announcements">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Announcements</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage enterprise announcements and broadcasts.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search announcements..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </form>

            @can('create', App\Models\Announcement::class)
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Announcement
            </a>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Title</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Category</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Audience</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Date</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $announcement->title }}</span>
                                @if($announcement->is_pinned)
                                <span class="ml-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pinned</span>
                                @endif
                                @if($announcement->priority === 'urgent')
                                <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800 uppercase tracking-wider">Urgent</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600 capitalize">{{ $announcement->category }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $announcement->audience_type) }}</td>
                        <td class="px-6 py-4">
                            @if($announcement->is_published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Published</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wider">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $announcement->created_at->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-action-dropdown>
                                @can('view', $announcement)
                                    <x-action-dropdown-item href="{{ route('admin.announcements.show', $announcement) }}" icon="view">
                                        View Details
                                    </x-action-dropdown-item>
                                @endcan

                                @can('update', $announcement)
                                    <x-action-dropdown-item href="{{ route('admin.announcements.edit', $announcement) }}" icon="edit">
                                        Edit Announcement
                                    </x-action-dropdown-item>
                                @endcan

                                @can('publish', $announcement)
                                    @if($announcement->is_published)
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'unpublish-announcement-{{ $announcement->id }}')">
                                            Unpublish
                                        </x-action-dropdown-item>
                                    @else
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'publish-announcement-{{ $announcement->id }}')">
                                            Publish
                                        </x-action-dropdown-item>
                                    @endif
                                @endcan

                                @can('delete', $announcement)
                                    <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-announcement-{{ $announcement->id }}')" icon="delete" variant="danger">
                                        Delete Announcement
                                    </x-action-dropdown-item>
                                @endcan
                            </x-action-dropdown>
                        </td>

                            {{-- Modals --}}
                            @can('publish', $announcement)
                                @if($announcement->is_published)
                                    <x-modal id="unpublish-announcement-{{ $announcement->id }}" maxWidth="md">
                                        <x-slot:header>Unpublish Announcement</x-slot:header>
                                        <div class="text-center py-4">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4 border border-amber-200">
                                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Unpublish Announcement?</h3>
                                            <p class="text-sm text-gray-500 font-medium">Are you sure you want to unpublish "<strong>{{ $announcement->title }}</strong>"? It will no longer be visible.</p>
                                        </div>
                                        <x-slot:footer>
                                            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                            <form action="{{ route('admin.announcements.unpublish', $announcement) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition-colors shadow-sm">Unpublish</button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                @else
                                    <x-modal id="publish-announcement-{{ $announcement->id }}" maxWidth="md">
                                        <x-slot:header>Publish Announcement</x-slot:header>
                                        <div class="text-center py-4">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 mb-4 border border-emerald-200">
                                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Publish Announcement?</h3>
                                            <p class="text-sm text-gray-500 font-medium">Are you sure you want to publish "<strong>{{ $announcement->title }}</strong>"? This will notify the targeted audience.</p>
                                        </div>
                                        <x-slot:footer>
                                            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                            <form action="{{ route('admin.announcements.publish', $announcement) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">Publish</button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                @endif
                            @endcan

                            @can('delete', $announcement)
                            <x-modal id="delete-announcement-{{ $announcement->id }}" maxWidth="md">
                                <x-slot:header>Delete Announcement</x-slot:header>
                                <div class="text-center py-4">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Delete Announcement?</h3>
                                    <p class="text-sm text-gray-500 font-medium">Are you sure you want to delete "<strong>{{ $announcement->title }}</strong>"? This action cannot be undone.</p>
                                </div>
                                <x-slot:footer>
                                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete</button>
                                    </form>
                                </x-slot:footer>
                            </x-modal>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">No announcements found</h3>
                            <p class="text-sm text-gray-500 font-medium">There are currently no announcements to display.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $announcements->appends(['search' => request('search')])->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
