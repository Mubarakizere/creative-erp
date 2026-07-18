<x-layouts.admin title="Announcements">
 <div class="sm:flex sm:items-center sm:justify-between mb-6">
 <div>
 <h1 class="text-2xl font-semibold text-gray-900 ">Announcements</h1>
 <p class="mt-2 text-sm text-gray-700 ">Manage enterprise announcements and broadcasts.</p>
 </div>
 <div class="mt-4 sm:mt-0">
 @can('create', App\Models\Announcement::class)
 <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
 Create Announcement
 </a>
 @endcan
 </div>
 </div>

 <x-card>
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-300 ">
 <thead class="bg-gray-50 ">
 <tr>
 <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Title</th>
 <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 ">Category</th>
 <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 ">Audience</th>
 <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 ">Status</th>
 <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 ">Date</th>
 <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
 <span class="sr-only">Actions</span>
 </th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200 bg-white ">
 @forelse($announcements as $announcement)
 <tr>
 <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
 {{ $announcement->title }}
 @if($announcement->is_pinned)
 <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">Pinned</span>
 @endif
 @if($announcement->priority === 'urgent')
 <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Urgent</span>
 @endif
 </td>
 <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">
 {{ $announcement->category }}
 </td>
 <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">
 {{ str_replace('_', ' ', $announcement->audience_type) }}
 </td>
 <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 ">
 @if($announcement->is_published)
 <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Published</span>
 @else
 <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Draft</span>
 @endif
 </td>
 <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 ">
 {{ $announcement->created_at->format('M d, Y') }}
 </td>
 <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
 <div class="flex items-center justify-end space-x-2">
 @can('view', $announcement)
 <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-indigo-600 hover:text-indigo-900 ">View</a>
 @endcan
 @can('update', $announcement)
 <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-indigo-600 hover:text-indigo-900 ">Edit</a>
 @endcan
 @can('publish', $announcement)
  @if($announcement->is_published)
      <button type="button" @click="$dispatch('open-modal', 'unpublish-announcement-{{ $announcement->id }}')" class="text-orange-600 hover:text-orange-900">Unpublish</button>
      <x-modal id="unpublish-announcement-{{ $announcement->id }}" maxWidth="sm">
          <x-slot name="header">Unpublish Announcement</x-slot>
          <div class="text-sm text-gray-500 text-left">
              Are you sure you want to unpublish "<strong>{{ $announcement->title }}</strong>"? It will no longer be visible to the audience.
          </div>
          <x-slot name="footer">
              <x-button type="ghost" @click="open = false">Cancel</x-button>
              <form action="{{ route('admin.announcements.unpublish', $announcement) }}" method="POST" class="inline">
                  @csrf
                  @method('PATCH')
                  <x-button type="warning" submit>Unpublish</x-button>
              </form>
          </x-slot>
      </x-modal>
  @else
      <button type="button" @click="$dispatch('open-modal', 'publish-announcement-{{ $announcement->id }}')" class="text-green-600 hover:text-green-900">Publish</button>
      <x-modal id="publish-announcement-{{ $announcement->id }}" maxWidth="sm">
          <x-slot name="header">Publish Announcement</x-slot>
          <div class="text-sm text-gray-500 text-left">
              Are you sure you want to publish "<strong>{{ $announcement->title }}</strong>"? This will immediately notify the targeted audience.
          </div>
          <x-slot name="footer">
              <x-button type="ghost" @click="open = false">Cancel</x-button>
              <form action="{{ route('admin.announcements.publish', $announcement) }}" method="POST" class="inline">
                  @csrf
                  @method('PATCH')
                  <x-button type="success" submit>Publish</x-button>
              </form>
          </x-slot>
      </x-modal>
  @endif
  @endcan
 @can('delete', $announcement)
 <button type="button" @click="$dispatch('open-modal', 'delete-announcement-{{ $announcement->id }}')" class="text-red-600 hover:text-red-900">Delete</button>

 <x-modal id="delete-announcement-{{ $announcement->id }}" maxWidth="sm">
     <x-slot name="header">Delete Announcement</x-slot>
     <div class="text-sm text-gray-500">
         Are you sure you want to delete the announcement "<strong>{{ $announcement->title }}</strong>"? This action cannot be undone.
     </div>
     <x-slot name="footer">
         <x-button type="ghost" @click="open = false">Cancel</x-button>
         <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline">
             @csrf
             @method('DELETE')
             <x-button type="danger" submit>Delete</x-button>
         </form>
     </x-slot>
 </x-modal>
 @endcan
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 ">
 No announcements found.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if($announcements->hasPages())
 <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
 {{ $announcements->links() }}
 </div>
 @endif
 </x-card>
</x-layouts.admin>
