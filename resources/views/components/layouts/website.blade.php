<x-layouts.app :title="$title ?? 'Creative Century Engineering'">
    <div class="min-h-screen flex flex-col">

        {{-- Website Header --}}
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <img src="{{ asset('images/logo.png') }}" alt="Creative Century Engineering" class="h-12 w-auto">
                    </a>

                    {{-- Navigation --}}
                    <nav class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">Home</a>
                        <a href="{{ route('expertise') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">Expertise</a>
                        <a href="{{ route('projects') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">Projects</a>
                        <a href="{{ route('about') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">About Us</a>
                    </nav>

                    {{-- Auth Actions --}}
                    <div class="flex items-center space-x-3">
                        @auth
                            <a href="{{ route('contact') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                Contact Us
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                Portal Login
                            </a>
                            <a href="{{ route('contact') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                Contact Us
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1">
            {{ $slot }}
        </main>

        {{-- Website Footer --}}
        <footer class="bg-gray-900 text-gray-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    {{-- Brand --}}
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center mb-4">
                            <img src="{{ asset('images/logo.png') }}" alt="Creative Century Engineering" class="h-10 w-auto bg-white/10 rounded px-2">
                        </div>
                        <p class="text-sm max-w-md">
                            Leading the future of construction and engineering in Rwanda with innovative solutions and unmatched excellence.
                        </p>
                    </div>

                    {{-- Links --}}
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Company</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('expertise') }}" class="hover:text-blue-400 transition-colors">Our Expertise</a></li>
                            <li><a href="{{ route('projects') }}" class="hover:text-blue-400 transition-colors">Featured Projects</a></li>
                            <li><a href="{{ route('about') }}" class="hover:text-blue-400 transition-colors">About Us</a></li>
                        </ul>
                    </div>

                    {{-- Contact --}}
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Contact</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Sales</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Partners</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-800 text-center text-sm">
                    <p>&copy; {{ date('Y') }} Creative Century Engineering. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .animation-delay-100 { animation-delay: 100ms; }
        .animation-delay-200 { animation-delay: 200ms; }
        .animation-delay-300 { animation-delay: 300ms; }
        .animation-delay-400 { animation-delay: 400ms; }
        .animation-delay-500 { animation-delay: 500ms; }
    </style>
</x-layouts.app>
