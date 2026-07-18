@props([
 'announcements' => collect()
])

<x-card title="Announcements">
 @if($announcements->isEmpty())
 <div class="text-center py-6">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
 </svg>
 <h3 class="mt-2 text-sm font-medium text-gray-900 ">No announcements</h3>
 <p class="mt-1 text-sm text-gray-500 ">You're all caught up!</p>
 </div>
 @else
 <ul role="list" class="divide-y divide-gray-200 ">
 @foreach($announcements as $announcement)
 <li class="py-4">
 <div class="flex space-x-3">
 <div class="flex-1 space-y-1">
 <div class="flex items-center justify-between">
 <h3 class="text-sm font-medium text-gray-900 ">
 <a href="{{ route('admin.announcements.show', $announcement) }}" class="hover:underline">
 {{ $announcement->title }}
 </a>
 </h3>
 <p class="text-sm text-gray-500 ">{{ $announcement->published_at?->diffForHumans() }}</p>
 </div>
 <p class="text-sm text-gray-500 ">
 {{ Str::limit(strip_tags($announcement->content), 100) }}
 </p>
 <div class="flex items-center space-x-2 mt-2">
 @if($announcement->priority === 'urgent')
 <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Urgent</span>
 @elseif($announcement->priority === 'high')
 <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">High</span>
 @endif
 <span class="text-xs text-gray-400">{{ ucfirst($announcement->category) }}</span>
 </div>
 </div>
 </div>
 </li>
 @endforeach
 </ul>
 <div class="mt-4 border-t border-gray-200 pt-4">
 <a href="{{ route('admin.announcements.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 ">
 View all announcements <span aria-hidden="true">&rarr;</span>
 </a>
 </div>
 @endif
</x-card>
