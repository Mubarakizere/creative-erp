<x-layouts.website title="Our Expertise">
    {{-- Hero Section --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1541888081622-c2871da050e6?auto=format&fit=crop&w=1920&q=80" alt="Engineering blueprint" class="w-full h-full object-cover opacity-30" />
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight animate-fade-in-up tracking-tight">
                Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Expertise</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto animate-fade-in-up animation-delay-100">
                Delivering world-class civil, electrical, and mechanical engineering solutions across Rwanda. We build the infrastructure that drives tomorrow.
            </p>
        </div>
    </section>

    {{-- Expertise Areas --}}
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $expertiseCards = \App\Models\ExpertiseCard::where('is_active', true)->orderBy('sort_order')->get();
            @endphp

            @foreach($expertiseCards as $index => $card)
                @php
                    // Alternate the flex direction based on the index (even = image left, odd = image right)
                    $isReversed = $index % 2 !== 0;
                @endphp
                <div class="flex flex-col {{ $isReversed ? 'lg:flex-row-reverse' : 'lg:flex-row' }} items-center gap-12 lg:gap-20 {{ !$loop->last ? 'mb-24' : '' }}">
                    <div class="w-full lg:w-1/2 relative group">
                        <div class="absolute inset-0 bg-blue-500 rounded-2xl transform {{ $isReversed ? '-translate-x-4' : 'translate-x-4' }} translate-y-4 group-hover:{{ $isReversed ? '-translate-x-2' : 'translate-x-2' }} group-hover:translate-y-2 transition-transform duration-300"></div>
                        @if($card->image)
                            <img src="{{ Str::startsWith($card->image, 'http') ? $card->image : asset($card->image) }}" alt="{{ $card->title }}" class="relative z-10 rounded-2xl w-full h-auto object-cover shadow-xl group-hover:-translate-y-1 transition-transform duration-300" />
                        @else
                            <div class="relative z-10 rounded-2xl w-full h-64 bg-gray-200 flex items-center justify-center shadow-xl group-hover:-translate-y-1 transition-transform duration-300">
                                <span class="text-gray-400">No image</span>
                            </div>
                        @endif
                    </div>
                    <div class="w-full lg:w-1/2">
                        <div class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm font-semibold mb-6">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} / Core Pillar
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-6">{{ $card->title }}</h2>
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            {{ $card->description }}
                        </p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-gray-700">Industry-leading safety standards</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-gray-700">Sustainable building practices</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-gray-700">Advanced project management tools</span>
                            </li>
                        </ul>
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-600 transition-colors">
                            Discuss Your Project
                        </a>
                    </div>
                </div>
            @endforeach
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
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">Need Our Expertise?</h2>
            <p class="text-blue-100 text-lg sm:text-xl mb-10 max-w-2xl mx-auto">
                Whether you're planning a massive infrastructure project or need specialized mechanical systems, we are ready to help.
            </p>
            <div class="flex justify-center">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-50 shadow-lg shadow-blue-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Contact Us Today
                </a>
            </div>
        </div>
    </section>
</x-layouts.website>
