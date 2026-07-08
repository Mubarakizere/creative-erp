<x-layouts.app :title="$title ?? 'Dashboard'">
    <div x-data="{ sidebarOpen: true, mobileMenuOpen: false }" class="min-h-screen flex">

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
             :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

            {{-- Top Navbar --}}
            <x-navbar />

            {{-- Breadcrumbs --}}
            @if(isset($breadcrumbs))
                <div class="px-4 sm:px-6 lg:px-8 pt-4">
                    <x-breadcrumb :items="$breadcrumbs" />
                </div>
            @endif

            {{-- Flash Messages --}}
            <div class="px-4 sm:px-6 lg:px-8 pt-2">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" dismissible />
                @endif
                @if(session('error'))
                    <x-alert type="error" :message="session('error')" dismissible />
                @endif
                @if(session('warning'))
                    <x-alert type="warning" :message="session('warning')" dismissible />
                @endif
                @if(session('status'))
                    <x-alert type="info" :message="session('status')" dismissible />
                @endif
            </div>

            {{-- Page Content --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-sm text-gray-500 text-center">
                    &copy; {{ date('Y') }} Creative ERP. All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"
         style="display: none;">
    </div>
</x-layouts.app>
