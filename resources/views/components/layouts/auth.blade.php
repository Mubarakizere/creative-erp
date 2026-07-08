<x-layouts.app :title="$title ?? 'Authentication'">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 px-4 py-12 sm:px-6 lg:px-8">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-600/5 rounded-full blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-md">
            {{-- Logo / Branding --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl shadow-lg shadow-blue-500/25 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Creative ERP</h1>
                <p class="mt-1 text-sm text-blue-200/70">Enterprise Resource Planning</p>
            </div>

            {{-- Auth Card --}}
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 p-8">
                {{-- Flash Messages --}}
                @if(session('status'))
                    <div class="mb-4">
                        <x-alert type="success" :message="session('status')" />
                    </div>
                @endif

                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-center text-sm text-blue-200/40">
                &copy; {{ date('Y') }} Creative ERP. All rights reserved.
            </p>
        </div>
    </div>
</x-layouts.app>
