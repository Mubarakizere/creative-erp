<x-layouts.website title="Home">
    {{-- Hero Section --}}
    <section class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden pb-40">
        {{-- Background Image Carousel with Overlay --}}
        <div class="absolute inset-0 z-0" x-data="{
            images: [
                'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1687986261123-b17f08f2796c?q=80&w=1331&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1508450859948-4e04fabaa4ea?q=80&w=779&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ],
            active: 0,
            init() {
                setInterval(() => {
                    this.active = (this.active === this.images.length - 1) ? 0 : this.active + 1;
                }, 6000);
            }
        }">
            <style>
                @keyframes kenburns {
                    0% { transform: scale(1); }
                    100% { transform: scale(1.15); }
                }
                .animate-ken-burns {
                    animation: kenburns 20s ease-out forwards;
                }
            </style>
            
            <template x-for="(image, index) in images" :key="index">
                <img :src="image" 
                     alt="Engineering Excellence" 
                     class="absolute inset-0 w-full h-full object-cover object-center origin-center transition-opacity duration-1000 ease-in-out" 
                     :class="{'opacity-100 animate-ken-burns z-10': active === index, 'opacity-0 z-0': active !== index}" />
            </template>

            <div class="absolute inset-0 bg-slate-900/75 mix-blend-multiply z-20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent z-20"></div>
        </div>

        <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white leading-tight animate-fade-in-up tracking-tight">
                Building the Future<br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">With Excellence</span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto animate-fade-in-up animation-delay-200">
                Creative Century Engineering delivers top-tier civil, structural, and mechanical engineering solutions. We transform bold visions into enduring realities.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up animation-delay-300 relative z-40">
                <a href="{{ route('projects') }}" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 transform hover:-translate-y-1">
                    View Our Projects
                </a>
                <a href="{{ route('expertise') }}" class="px-8 py-4 bg-white/10 backdrop-blur-md text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1">
                    Our Expertise
                </a>
            </div>
        </div>


    </section>

    {{-- Stats Section --}}
    <section class="relative z-40 -mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 sm:p-12 border border-gray-100 backdrop-blur-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                <div class="text-center sm:px-4 pt-4 sm:pt-0">
                    <div class="text-4xl font-black text-slate-900 mb-2">15<span class="text-blue-500">+</span></div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Years Experience</div>
                </div>
                <div class="text-center sm:px-4 pt-4 sm:pt-0">
                    <div class="text-4xl font-black text-slate-900 mb-2">120<span class="text-blue-500">+</span></div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Projects Completed</div>
                </div>
                <div class="text-center sm:px-4 pt-4 sm:pt-0">
                    <div class="text-4xl font-black text-slate-900 mb-2">50<span class="text-blue-500">+</span></div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Expert Engineers</div>
                </div>
                <div class="text-center sm:px-4 pt-4 sm:pt-0">
                    <div class="text-4xl font-black text-slate-900 mb-2">100<span class="text-blue-500">%</span></div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Client Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Expertise Section --}}
    <section id="expertise" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold tracking-widest text-blue-600 uppercase mb-3">What We Do</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Our Core Expertise</h3>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Delivering comprehensive engineering solutions with precision, innovation, and unwavering commitment to quality.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Service 1 --}}
                <div class="group relative bg-white rounded-2xl p-8 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 border border-gray-100 hover:border-blue-100 overflow-hidden transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all"></div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Civil Construction</h4>
                    <p class="text-gray-600 leading-relaxed relative z-10">From residential complexes to large-scale commercial buildings, we build structures that stand the test of time.</p>
                </div>

                {{-- Service 2 --}}
                <div class="group relative bg-white rounded-2xl p-8 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 border border-gray-100 hover:border-blue-100 overflow-hidden transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all"></div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Electrical Engineering</h4>
                    <p class="text-gray-600 leading-relaxed relative z-10">Comprehensive electrical designs and installations ensuring safety, efficiency, and sustainability.</p>
                </div>

                {{-- Service 3 --}}
                <div class="group relative bg-white rounded-2xl p-8 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 border border-gray-100 hover:border-blue-100 overflow-hidden transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all"></div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Mechanical Systems</h4>
                    <p class="text-gray-600 leading-relaxed relative z-10">HVAC, plumbing, and mechanical infrastructure designed for optimal performance in modern facilities.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Projects Section --}}
    <section id="projects" class="py-24 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div class="max-w-2xl">
                    <h2 class="text-sm font-bold tracking-widest text-blue-500 uppercase mb-3">Portfolio</h2>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-white">Featured Projects</h3>
                    <p class="mt-4 text-gray-400">A glimpse into our recent successful deliveries across Rwanda.</p>
                </div>
                <a href="{{ route('projects') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-semibold mt-6 md:mt-0 transition-colors group">
                    View All Projects
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($websiteProjects as $project)
                    <div class="group relative h-80 rounded-2xl overflow-hidden cursor-pointer">
                        <img src="{{ $project->image ? asset($project->image) : 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            @if($project->category)
                                <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-3 inline-block">{{ $project->category }}</span>
                            @endif
                            <h4 class="text-2xl font-bold text-white mb-2">{{ $project->title }}</h4>
                            <p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">{{ $project->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12 text-gray-400">
                        No featured projects found. Check back later!
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section id="contact" class="py-20 bg-blue-600 relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="none" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <circle cx="400" cy="400" r="400" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
                <circle cx="400" cy="400" r="300" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
            </svg>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">Ready to Start Your Next Project?</h2>
            <p class="text-blue-100 text-lg sm:text-xl mb-10 max-w-2xl mx-auto">
                Partner with Creative Century Engineering to bring your vision to life. Contact our team of experts today.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-50 shadow-lg shadow-blue-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Contact Us Today
                </a>
                <a href="{{ route('projects') }}" class="px-8 py-4 bg-blue-700 text-white font-bold rounded-xl hover:bg-blue-800 transition-all duration-300 border border-blue-500">
                    Browse Portfolio
                </a>
            </div>
        </div>
    </section>
</x-layouts.website>
