<x-layouts.admin title="Notification Preferences">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Notification Preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500  flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Notifications
                </a>
            </div>

            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900  border-b border-gray-200 ">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Notification Settings
                    </h3>
                </div>

                <div class="p-6 text-gray-900 ">
                    
                    @if (session('success'))
                        <div class="mb-6 bg-green-50  border border-green-200  text-green-800  px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('admin.notifications.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-10">
                            <h3 class="text-base font-medium text-gray-900  mb-4 border-b border-gray-100  pb-2">Delivery Channels</h3>
                            <div class="space-y-4 pl-1">
                                <div class="flex items-start hover:bg-gray-50  p-2 rounded transition-colors -ml-2">
                                    <div class="flex h-5 items-center">
                                        <input id="email" name="email" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500  " {{ $preferences->email ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm flex-1">
                                        <label for="email" class="font-medium text-gray-700  block cursor-pointer">Email Notifications</label>
                                        <p class="text-gray-500  mt-1 cursor-pointer">Receive an email when you get a notification.</p>
                                    </div>
                                </div>
                                <div class="flex items-start hover:bg-gray-50  p-2 rounded transition-colors -ml-2">
                                    <div class="flex h-5 items-center">
                                        <input id="database" name="database" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500  " {{ $preferences->database ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm flex-1">
                                        <label for="database" class="font-medium text-gray-700  block cursor-pointer">In-App Notifications</label>
                                        <p class="text-gray-500  mt-1 cursor-pointer">Receive notifications inside the application (bell icon).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-10">
                            <h3 class="text-base font-medium text-gray-900  mb-4 border-b border-gray-100  pb-2">Notification Categories</h3>
                            <p class="text-sm text-gray-500  mb-6">Choose which events you want to be notified about.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @php
                                    $categories = [
                                        'assignments' => 'Task Assignments',
                                        'mentions' => 'Mentions & Comments',
                                        'workflow' => 'Workflow & Approvals',
                                        'projects' => 'Project Updates',
                                        'documents' => 'Document Changes',
                                        'meetings' => 'Meeting Invitations',
                                        'system' => 'System Alerts',
                                    ];
                                @endphp

                                @foreach($categories as $key => $label)
                                    <div class="flex items-start p-3 border border-gray-200  rounded-md hover:bg-gray-50  transition-colors shadow-sm bg-white ">
                                        <div class="flex h-5 items-center">
                                            <input id="{{ $key }}" name="{{ $key }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500  " {{ $preferences->{$key} ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm w-full">
                                            <label for="{{ $key }}" class="font-medium text-gray-700  block cursor-pointer w-full">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200 ">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2.5 px-6 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
