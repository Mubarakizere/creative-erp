@php
    $notificationService = app(\App\Services\NotificationService::class);
    $user = auth()->user();
    $unreadCount = $notificationService->getUnreadCount($user);
    $recentNotifications = $notificationService->getRecentNotifications($user, 5);
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <button @click="open = !open" type="button" class="relative p-1 rounded-full text-gray-500 hover:text-gray-700   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        <span class="sr-only">View notifications</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-600 text-white text-[10px] font-bold text-center leading-4 ring-2 ring-white ">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         class="origin-top-right absolute right-0 mt-2 w-80 md:w-96 rounded-md shadow-xl bg-white  ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden" 
         style="display: none;">
        
        <div class="px-4 py-3 border-b border-gray-100  flex justify-between items-center bg-gray-50 ">
            <h3 class="text-sm font-semibold text-gray-900 ">Notifications</h3>
            @if($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-500  font-medium transition-colors">Mark all as read</button>
                </form>
            @endif
        </div>

        <div class="max-h-[24rem] overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <x-notification-empty />
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-100  text-center bg-gray-50 ">
            <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500  transition-colors">
                View all notifications
            </a>
        </div>
    </div>
</div>
