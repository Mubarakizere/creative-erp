<x-layouts.website title="About Us - Creative Century Engineering">
    {{-- Hero Section --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1504307651254-35680f356f12?auto=format&fit=crop&w=1920&q=80" alt="Construction site team" class="w-full h-full object-cover opacity-30" />
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight animate-fade-in-up tracking-tight">
                About <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Creative Century Engineering</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto animate-fade-in-up animation-delay-100">
                Pioneering excellence in engineering across Rwanda. We are a team of dedicated professionals committed to building the infrastructure of tomorrow.
            </p>
        </div>
    </section>

    {{-- Story Section --}}
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="w-full lg:w-1/2 relative">
                    <div class="absolute inset-0 bg-blue-500 rounded-2xl transform translate-x-4 translate-y-4"></div>
                    <img src="https://images.unsplash.com/photo-1517581177682-a085bb7ffb15?auto=format&fit=crop&w=800&q=80" alt="Engineers working" class="relative z-10 rounded-2xl w-full h-auto object-cover shadow-xl" />
                </div>
                <div class="w-full lg:w-1/2">
                    <h2 class="text-sm font-bold tracking-widest text-blue-600 uppercase mb-3">Who We Are</h2>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-6">Building the Future of Rwanda</h3>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Founded with a vision to transform the engineering landscape, Creative Century Engineering has grown into a leading firm renowned for delivering complex projects with precision. Our multidisciplinary approach combines civil, electrical, and mechanical engineering to provide holistic solutions.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        We believe that great infrastructure is the foundation of a thriving society. That is why every project we undertake is driven by a commitment to sustainability, safety, and innovation. We don't just build structures; we create environments where people and businesses can flourish.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                        <div>
                            <div class="text-3xl font-bold text-blue-600 mb-2">15+</div>
                            <div class="text-sm font-medium text-gray-500 uppercase">Years Experience</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-blue-600 mb-2">120+</div>
                            <div class="text-sm font-medium text-gray-500 uppercase">Projects Delivered</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Values Section --}}
    <section class="py-24 bg-slate-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold tracking-widest text-blue-600 uppercase mb-3">Our Core Values</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900">What Drives Us Forward</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Value 1 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Excellence</h4>
                    <p class="text-gray-600 leading-relaxed">We never compromise on quality. Our stringent standards ensure that every project is delivered to the highest specifications.</p>
                </div>

                {{-- Value 2 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Innovation</h4>
                    <p class="text-gray-600 leading-relaxed">We embrace new technologies and modern engineering methodologies to provide creative solutions to complex challenges.</p>
                </div>

                {{-- Value 3 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Sustainability</h4>
                    <p class="text-gray-600 leading-relaxed">We are committed to eco-friendly practices that minimize environmental impact and promote long-term sustainability.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 bg-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="none" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <circle cx="400" cy="400" r="400" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
                <circle cx="400" cy="400" r="300" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
            </svg>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">Let's Build Together</h2>
            <p class="text-blue-100 text-lg sm:text-xl mb-10 max-w-2xl mx-auto">
                Ready to start your next big project? Our team of experts is here to turn your vision into reality.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-50 shadow-lg shadow-blue-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Contact Us Today
                </a>
                <a href="{{ route('projects') }}" class="px-8 py-4 bg-blue-700 text-white font-bold rounded-xl hover:bg-blue-800 transition-all duration-300 border border-blue-500">
                    See Our Work
                </a>
            </div>
        </div>
    </section>
</x-layouts.website>
