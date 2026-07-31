@props([
    'name',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed with this action? This cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmType' => 'danger', // 'danger', 'primary', 'warning'
])

@php
    $buttonColors = match($confirmType) {
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
        'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        default => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    };
    
    $iconColors = match($confirmType) {
        'danger' => 'text-red-600 bg-red-100',
        'warning' => 'text-amber-600 bg-amber-100',
        'primary' => 'text-blue-600 bg-blue-100',
        default => 'text-blue-600 bg-blue-100',
    };
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="relative z-50"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Background backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
    ></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
                @click.away="show = false"
            >
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <!-- Icon -->
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 {{ $iconColors }}">
                            @if($confirmType === 'danger' || $confirmType === 'warning')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            @endif
                        </div>
                        
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-bold leading-6 text-gray-900" id="modal-title">{{ $title }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 leading-relaxed">{{ $message }}</p>
                            </div>
                            
                            <!-- Custom content slot if needed (like input for confirmation text) -->
                            @if(isset($slot) && $slot->isNotEmpty())
                                <div class="mt-4">
                                    {{ $slot }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                    <button type="button" @click="document.getElementById('form-{{ $name }}').submit()" class="inline-flex w-full justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $buttonColors }}">
                        {{ $confirmText }}
                    </button>
                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                        {{ $cancelText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
