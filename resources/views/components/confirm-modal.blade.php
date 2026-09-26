@props([
    'name',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed with this action? This cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmType' => 'danger', // 'danger', 'primary', 'warning'
    'form' => null,
])

@php
    $targetFormId = $form ?? ('form-' . $name);

    $buttonColors = match($confirmType) {
        'danger' => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500 text-white shadow-xs hover:shadow-sm',
        'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500 text-white shadow-xs hover:shadow-sm',
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-white shadow-xs hover:shadow-sm',
        default => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500 text-white shadow-xs hover:shadow-sm',
    };
    
    $iconColors = match($confirmType) {
        'danger' => 'text-rose-600 bg-rose-100 border border-rose-200',
        'warning' => 'text-amber-600 bg-amber-100 border border-amber-200',
        'primary' => 'text-indigo-600 bg-indigo-100 border border-indigo-200',
        default => 'text-rose-600 bg-rose-100 border border-rose-200',
    };
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    {{ $attributes }}
>
    <template x-teleport="body">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] overflow-y-auto"
            style="display: none;"
            aria-labelledby="modal-title-{{ $name }}"
            role="dialog"
            aria-modal="true"
        >
            <!-- Background backdrop -->
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="show = false"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100"
                    @click.stop
                >
                    <div class="bg-white px-5 pb-5 pt-6 sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <!-- Icon -->
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl sm:mx-0 sm:h-11 sm:w-11 {{ $iconColors }}">
                                @if($confirmType === 'danger' || $confirmType === 'warning')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                @endif
                            </div>
                            
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title-{{ $name }}">{{ $title }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 leading-relaxed">{{ $message }}</p>
                                </div>
                                
                                @if(isset($slot) && $slot->isNotEmpty())
                                    <div class="mt-4">
                                        {{ $slot }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50/80 px-5 py-3.5 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 gap-2">
                        <button type="button" 
                                @click="const targetForm = document.getElementById('{{ $targetFormId }}'); if(targetForm) { targetForm.submit(); } else { console.error('Form not found: {{ $targetFormId }}'); } show = false;" 
                                class="inline-flex w-full justify-center items-center rounded-xl px-4 py-2.5 text-xs font-semibold shadow-xs sm:w-auto focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all cursor-pointer {{ $buttonColors }}">
                            {{ $confirmText }}
                        </button>
                        <button type="button" 
                                @click="show = false" 
                                class="mt-2 sm:mt-0 inline-flex w-full justify-center items-center rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-xs ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:w-auto transition-colors cursor-pointer">
                            {{ $cancelText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
