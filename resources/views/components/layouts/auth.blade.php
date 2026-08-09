<x-layouts.app :title="$title ?? 'Authentication'">
    <div class="min-h-screen flex bg-white">
        {{-- Left: Login Form --}}
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                {{-- Logo / Branding --}}
                <div class="mb-8">
                    <a href="{{ route('home') }}" class="inline-block hover:opacity-80 transition-opacity">
                        <img src="{{ asset('images/logo.png') }}" alt="Creative Century Engineering" class="h-16 w-auto">
                    </a>
                    <p class="mt-4 text-sm text-gray-500 font-medium">Enterprise Management System</p>
                </div>

                {{-- Flash Messages handled globally --}}

                {{ $slot }}

                {{-- Footer --}}
                <p class="mt-10 text-sm text-gray-400">
                    &copy; {{ date('Y') }} Creative Century Engineering. All rights reserved.
                </p>
            </div>
        </div>

        {{-- Right: Image Background --}}
        <div class="hidden lg:block relative w-0 flex-1 bg-slate-900">
            <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('images/login-bg.png') }}" alt="Engineers working at site">
            {{-- Optional overlay --}}
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 via-blue-900/20 to-transparent"></div>
            
            <div class="absolute bottom-12 left-12 right-12 text-white">
                <blockquote class="space-y-4">
                    <p class="text-xl font-medium leading-relaxed">
                        "Creative Century Engineering's enterprise system has completely transformed how we manage our engineering projects and resources. It's the backbone of our daily operations."
                    </p>
                    <footer class="flex items-center gap-3">
                        <div class="font-semibold text-white">Project Management Team</div>
                        <div class="text-blue-200 text-sm">— Creative Century Engineering</div>
                    </footer>
                </blockquote>
            </div>
        </div>
    </div>
</x-layouts.app>
