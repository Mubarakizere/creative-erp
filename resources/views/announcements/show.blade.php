<x-layouts.admin title="{{ $announcement->title }}">
 <div class="sm:flex sm:items-center sm:justify-between mb-6">
 <div>
 <h1 class="text-2xl font-semibold text-gray-900 ">{{ $announcement->title }}</h1>
 <p class="mt-2 text-sm text-gray-700 ">
 Published on {{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : 'Not Published' }}
 by {{ $announcement->creator->full_name }}
 </p>
 </div>
 <div class="mt-4 sm:mt-0 flex space-x-3">
 @can('update', $announcement)
 <a href="{{ route('admin.announcements.edit', $announcement) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
 Edit
 </a>
 @endcan
 <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
 Back to List
 </a>
 </div>
 </div>

 <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
 <div class="lg:col-span-2 space-y-6">
 <x-card>
 <div class="prose max-w-none">
 {!! nl2br(e($announcement->content)) !!}
 </div>
 </x-card>
 </div>

 <div class="space-y-6">
 <x-card>
 <h3 class="text-lg font-medium text-gray-900 mb-4">Details</h3>
 <dl class="space-y-4">
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Category</dt>
 <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $announcement->category }}</dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Priority</dt>
 <dd class="mt-1 text-sm text-gray-900 capitalize">
 <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
 {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
 ($announcement->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
 {{ $announcement->priority }}
 </span>
 </dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Audience</dt>
 <dd class="mt-1 text-sm text-gray-900 capitalize">
 {{ str_replace('_', ' ', $announcement->audience_type) }}
 @if($announcement->audience_type === 'specific_users')
     <ul class="mt-2 list-disc list-inside">
     @foreach($announcement->users as $user)
         <li>{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</li>
     @endforeach
     </ul>
 @elseif($announcement->audience_id)
     (ID: {{ $announcement->audience_id }})
 @endif
 </dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Status</dt>
 <dd class="mt-1 text-sm text-gray-900 ">
 @if($announcement->is_published)
 <span class="text-green-600 font-medium">Published</span>
 @else
 <span class="text-gray-500">Draft</span>
 @endif
 </dd>
 </div>
 @if($announcement->starts_at)
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Starts At</dt>
 <dd class="mt-1 text-sm text-gray-900 ">{{ $announcement->starts_at->format('M d, Y H:i') }}</dd>
 </div>
 @endif
 @if($announcement->expires_at)
 <div>
 <dt class="text-sm font-medium text-gray-500 ">Expires At</dt>
 <dd class="mt-1 text-sm text-gray-900 ">{{ $announcement->expires_at->format('M d, Y H:i') }}</dd>
 </div>
 @endif
 </dl>
 </x-card>
 </div>
 </div>
</x-layouts.admin>
