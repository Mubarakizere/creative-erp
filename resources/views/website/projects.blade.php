<x-layouts.website title="Our Projects">
    {{-- Hero Section --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1541888081622-c2871da050e6?auto=format&fit=crop&w=1920&q=80" alt="Engineering blueprint" class="w-full h-full object-cover opacity-30" />
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight animate-fade-in-up tracking-tight">
                Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Projects</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto animate-fade-in-up animation-delay-100">
                Explore our portfolio of successful deliveries. From massive infrastructure to modern commercial complexes, our work speaks for itself.
            </p>
        </div>
    </section>

    {{-- Projects Grid Section --}}
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $websiteProjects = \App\Models\WebsiteProject::where('is_active', true)->orderBy('sort_order')->get();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($websiteProjects as $project)
                    <div class="group relative h-96 rounded-2xl overflow-hidden cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300">
                        <img src="{{ $project->image ? asset($project->image) : 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 p-8 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            @if($project->category)
                                <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-3 inline-block">{{ $project->category }}</span>
                            @endif
                            <h4 class="text-2xl font-bold text-white mb-2">{{ $project->title }}</h4>
                            <p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">{{ $project->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No projects yet</h3>
                        <p class="text-gray-500">Check back later for updates to our portfolio.</p>
                    </div>
                @endforelse
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
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">Ready to Start Your Next Project?</h2>
            <p class="text-blue-100 text-lg sm:text-xl mb-10 max-w-2xl mx-auto">
                Partner with Creative Engineering Rwanda to bring your vision to life.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-50 shadow-lg shadow-blue-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Contact Us Today
                </a>
            </div>
        </div>
    </section>
</x-layouts.website>
