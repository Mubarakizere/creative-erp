<x-layouts.admin title="Notification Center">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Notification Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-notification-filters />

            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900  border-b border-gray-200  flex justify-between items-center">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        All Notifications
                    </h3>
                    
                    @if($notifications->count() > 0)
                    <div class="flex space-x-3 items-center">
                        <a href="{{ route('admin.notifications.preferences') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900   transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                        
                        <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500  border border-blue-200  px-3 py-1 rounded hover:bg-blue-50  transition-colors">
                                Mark all as read
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                
                @if($notifications->count() > 0)
                <form action="{{ route('admin.notifications.bulk-action') }}" method="POST" id="bulk-action-form">
                    @csrf
                    
                    <div class="px-6 py-3 bg-gray-50  border-b border-gray-200  flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500  ">
                            <label for="select-all" class="ml-2 text-sm text-gray-600 ">Select All</label>
                        </div>
                        <div class="flex space-x-2" x-data>
                            <input type="hidden" name="action" id="bulk-action-input" value="">
                            <button type="button" class="text-sm text-gray-600 hover:text-blue-600  " @click="$dispatch('open-modal', 'confirm-bulk-read')">Mark Read</button>
                            <span class="text-gray-300 ">|</span>
                            <button type="button" class="text-sm text-gray-600 hover:text-blue-600  " @click="$dispatch('open-modal', 'confirm-bulk-unread')">Mark Unread</button>
                            <span class="text-gray-300 ">|</span>
                            <button type="button" class="text-sm text-red-600 hover:text-red-800  " @click="$dispatch('open-modal', 'confirm-bulk-delete')">Delete</button>
                        </div>
                    </div>

                    <!-- Modals for Bulk Actions -->
                    <x-modal id="confirm-bulk-read" maxWidth="sm">
                        <x-slot:header>Mark as Read</x-slot:header>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Are you sure you want to mark the selected notifications as read?</p>
                        </div>
                        <x-slot:footer>
                            <x-button type="ghost" @click="$dispatch('close-modal', 'confirm-bulk-read')">Cancel</x-button>
                            <x-button type="primary" @click="document.getElementById('bulk-action-input').value='read'; document.getElementById('bulk-action-form').submit();">Confirm</x-button>
                        </x-slot:footer>
                    </x-modal>

                    <x-modal id="confirm-bulk-unread" maxWidth="sm">
                        <x-slot:header>Mark as Unread</x-slot:header>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Are you sure you want to mark the selected notifications as unread?</p>
                        </div>
                        <x-slot:footer>
                            <x-button type="ghost" @click="$dispatch('close-modal', 'confirm-bulk-unread')">Cancel</x-button>
                            <x-button type="primary" @click="document.getElementById('bulk-action-input').value='unread'; document.getElementById('bulk-action-form').submit();">Confirm</x-button>
                        </x-slot:footer>
                    </x-modal>

                    <x-modal id="confirm-bulk-delete" maxWidth="sm">
                        <x-slot:header>Delete Notifications</x-slot:header>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Are you sure you want to delete the selected notifications? This action cannot be undone.</p>
                        </div>
                        <x-slot:footer>
                            <x-button type="ghost" @click="$dispatch('close-modal', 'confirm-bulk-delete')">Cancel</x-button>
                            <x-button type="danger" @click="document.getElementById('bulk-action-input').value='delete'; document.getElementById('bulk-action-form').submit();">Delete</x-button>
                        </x-slot:footer>
                    </x-modal>

                    <div class="divide-y divide-gray-100 ">
                        @foreach($notifications as $notification)
                            <div class="flex items-start hover:bg-gray-50  transition-colors">
                                <div class="pt-5 pl-6 pr-2">
                                    <input type="checkbox" name="ids[]" value="{{ $notification->id }}" class="notification-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500   mt-1">
                                </div>
                                <div class="flex-1 w-full relative">
                                    <x-notification-item :notification="$notification" />
                                    <!-- Invisible overlay link to make the whole area clickable if there's an action url -->
                                    @if($notification->action_url)
                                        <a href="{{ route('admin.notifications.show', $notification->id) }}" class="absolute inset-0 z-0"></a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
                
                <div class="p-4 border-t border-gray-200 ">
                    {{ $notifications->withQueryString()->links() }}
                </div>
                
                <script>
                    document.getElementById('select-all').addEventListener('change', function(e) {
                        const checkboxes = document.querySelectorAll('.notification-checkbox');
                        checkboxes.forEach(cb => cb.checked = e.target.checked);
                    });
                </script>
                @else
                    <div class="py-12">
                        <x-notification-empty />
                        <div class="text-center mt-4">
                            <a href="{{ route('admin.notifications.preferences') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 ">
                                View Notification Settings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Hidden form for single actions to avoid nested forms --}}
            <form id="single-action-form" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
            </form>
        </div>
    </div>
</x-app-layout>
