<x-layouts.app :title="$title ?? 'Creative Century Engineering'">
    <div class="min-h-screen flex flex-col">

        {{-- Website Header --}}
        <header x-data="{ mobileMenuOpen: false }" class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
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

                    {{-- Auth Actions & Mobile Toggle --}}
                    <div class="flex items-center space-x-3">
                        <div class="hidden md:flex items-center space-x-3">
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
                        
                        {{-- Mobile menu button --}}
                        <div class="flex items-center md:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50 focus:outline-none transition-colors" aria-controls="mobile-menu" aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <svg :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-200" id="mobile-menu" style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Home</a>
                    <a href="{{ route('expertise') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Expertise</a>
                    <a href="{{ route('projects') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Projects</a>
                    <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">About Us</a>
                </div>
                <div class="pt-4 pb-4 border-t border-gray-200">
                    <div class="px-4 space-y-2">
                        @auth
                            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Contact Us</a>
                            <a href="{{ route('admin.dashboard') }}" class="block text-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Portal Login</a>
                            <a href="{{ route('contact') }}" class="block text-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">Contact Us</a>
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


        {{-- Back to Top Button --}}
        <div x-data="{ showBackToTop: false }"
             x-init="window.addEventListener('scroll', () => { showBackToTop = window.scrollY > 300 })"
             class="fixed bottom-8 right-8 z-50">
            <button x-show="showBackToTop"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-90"
                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="group flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                    aria-label="Back to top"
                    title="Back to top">
                <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>
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
