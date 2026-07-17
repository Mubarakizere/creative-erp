<x-layouts.app title="Access Pending">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            {{-- Logo --}}
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Creative <span class="text-blue-600">ERP</span>
                </h2>
            </div>

            {{-- Access Pending Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <div class="text-center">
                    {{-- Icon --}}
                    <div class="mx-auto w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Access Pending</h3>

                    {{-- Message --}}
                    <p class="text-gray-600 mb-6">
                        Your account has been created successfully, but no role has been assigned to you yet.
                        Please contact your system administrator to request access to the ERP modules.
                    </p>

                    {{-- User Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-center gap-3">
                            @if(auth()->user()->avatar)
                                <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    {{ auth()->user()->initials ?? strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6 text-left">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">What happens next?</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    An administrator will review your account and assign the appropriate role and permissions.
                                    You will receive access to the relevant ERP modules once your role is configured.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Creative ERP. All rights reserved.
            </p>
        </div>
    </div>
</x-layouts.app>
