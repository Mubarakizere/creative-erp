@props(['notifications'])

<div class="bg-white  shadow rounded-lg overflow-hidden border border-gray-200 ">
    <div class="p-4 border-b border-gray-200  flex justify-between items-center bg-gray-50 ">
        <h3 class="text-lg font-semibold text-gray-900  flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            Recent Notifications
        </h3>
        <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 ">View All</a>
    </div>
    
    <div class="divide-y divide-gray-100 ">
        @forelse($notifications as $notification)
            <x-notification-item :notification="$notification" />
        @empty
            <x-notification-empty />
        @endforelse
    </div>
</div>
