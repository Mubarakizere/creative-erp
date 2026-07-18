@props([
 'announcements' => collect()
])

@if(config('realtime.features.announcements') && $announcements->isNotEmpty())
 <div x-data="{ show: true, currentIndex: 0 }" x-show="show" class="relative bg-indigo-600">
 <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
 <div class="pr-16 sm:text-center sm:px-16">
 <p class="font-medium text-white">
 <span class="md:hidden" x-text="'{{ $announcements->first()->title }}'">
 {{ Str::limit($announcements->first()->title, 30) }}
 </span>
 <span class="hidden md:inline" x-text="'{{ $announcements->first()->title }}'">
 {{ $announcements->first()->title }}
 </span>
 <span class="block sm:ml-2 sm:inline-block">
 <a href="{{ route('admin.announcements.show', $announcements->first()) }}" class="text-white font-bold underline"> Learn more <span aria-hidden="true">&rarr;</span></a>
 </span>
 </p>
 </div>
 <div class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start">
 <button @click="show = false" type="button" class="flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white">
 <span class="sr-only">Dismiss</span>
 <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
 </svg>
 </button>
 </div>
 </div>
 </div>
@endif
