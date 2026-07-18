@props([
 'position' => 'bottom-right', // top-right, top-left, bottom-right, bottom-left
])

@php
 $positionClasses = match($position) {
 'top-right' => 'top-4 right-4',
 'top-left' => 'top-4 left-4',
 'bottom-right' => 'bottom-4 right-4',
 'bottom-left' => 'bottom-4 left-4',
 default => 'bottom-4 right-4',
 };
@endphp

<div
 x-data="{
 toasts: [],
 addToast(toast) {
 const id = Date.now();
 this.toasts.push({
 id: id,
 type: toast.type || 'info',
 message: toast.message,
 title: toast.title || '',
 timeout: toast.timeout || 5000,
 show: true
 });
 
 if (toast.timeout !== 0) {
 setTimeout(() => {
 this.removeToast(id);
 }, toast.timeout || 5000);
 }
 },
 removeToast(id) {
 const index = this.toasts.findIndex(t => t.id === id);
 if (index > -1) {
 this.toasts[index].show = false;
 setTimeout(() => {
 this.toasts = this.toasts.filter(t => t.id !== id);
 }, 300); // Wait for animation
 }
 },
 init() {
 // Listen for global event bus
 if (window.EventBus) {
 window.EventBus.on('toast', (data) => {
 this.addToast(data);
 });
 }
 
 // Listen for Alpine custom events
 window.addEventListener('toast', (e) => {
 this.addToast(e.detail);
 });
 
 // Check for flash messages from backend
 @if(session('success'))
 this.addToast({ type: 'success', message: '{{ session('success') }}' });
 @endif
 @if(session('error'))
 this.addToast({ type: 'error', message: '{{ session('error') }}' });
 @endif
 @if(session('info'))
 this.addToast({ type: 'info', message: '{{ session('info') }}' });
 @endif
 @if(session('warning'))
 this.addToast({ type: 'warning', message: '{{ session('warning') }}' });
 @endif
 }
 }"
 class="fixed z-50 flex flex-col gap-3 {{ $positionClasses }}"
 aria-live="polite"
>
 <template x-for="toast in toasts" :key="toast.id">
 <div
 x-show="toast.show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
 x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0 translate-x-2"
 class="flex items-start w-full max-w-sm p-4 text-gray-500 bg-white rounded-lg shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5"
 role="alert"
 >
 <!-- Icons based on type -->
 <div class="flex-shrink-0">
 <template x-if="toast.type === 'success'">
 <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </template>
 <template x-if="toast.type === 'error'">
 <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </template>
 <template x-if="toast.type === 'warning'">
 <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
 </svg>
 </template>
 <template x-if="toast.type === 'info'">
 <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </template>
 </div>
 
 <div class="ml-3 w-0 flex-1 pt-0.5">
 <template x-if="toast.title">
 <p class="text-sm font-medium text-gray-900 " x-text="toast.title"></p>
 </template>
 <p class="text-sm text-gray-500 mt-1" x-text="toast.message"></p>
 </div>
 
 <div class="ml-4 flex-shrink-0 flex">
 <button @click="removeToast(toast.id)" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <span class="sr-only">Close</span>
 <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 </button>
 </div>
 </div>
 </template>
</div>
