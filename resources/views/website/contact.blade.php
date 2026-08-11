<x-layouts.website title="Contact Us - Creative Century Engineering">
    {{-- Hero Section --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80" alt="Office meeting room" class="w-full h-full object-cover opacity-30" />
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight animate-fade-in-up tracking-tight">
                Get In <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Touch</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto animate-fade-in-up animation-delay-100">
                Have a project in mind or want to learn more about our services? We'd love to hear from you. Our team is ready to answer your questions.
            </p>
        </div>
    </section>

    {{-- Contact Content --}}
    <section class="py-24 bg-slate-50 relative -mt-10 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-gray-100 flex flex-col lg:flex-row">
                
                {{-- Contact Info (Left Side) --}}
                <div class="w-full lg:w-2/5 bg-blue-600 text-white p-10 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="h-full w-full" fill="none" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="400" cy="400" r="400" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
                            <circle cx="400" cy="400" r="300" stroke="currentColor" stroke-width="40" stroke-dasharray="20 20"/>
                        </svg>
                    </div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl sm:text-3xl font-bold mb-8">Contact Information</h3>
                        <p class="text-blue-100 mb-12">Fill up the form and our team will get back to you within 24 hours.</p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-lg font-semibold mb-1">Phone</h4>
                                    <p class="text-blue-100">{{ $settings['contact_phone_1'] ?? '+250 788 123 456' }}</p>
                                    <p class="text-blue-100">{{ $settings['contact_phone_2'] ?? '+250 733 123 456' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-lg font-semibold mb-1">Email</h4>
                                    <p class="text-blue-100">{{ $settings['contact_email_1'] ?? 'contact@creativeengineering.rw' }}</p>
                                    <p class="text-blue-100">{{ $settings['contact_email_2'] ?? 'info@creativeengineering.rw' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-lg font-semibold mb-1">Office</h4>
                                    <p class="text-blue-100">{{ $settings['contact_address_line_1'] ?? 'KN 5 Rd, Kigali Business Center (KBC)' }}</p>
                                    <p class="text-blue-100">{{ $settings['contact_address_line_2'] ?? '6th Floor, Kigali, Rwanda' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Social Links --}}
                        <div class="mt-16 pt-8 border-t border-white/20">
                            <h4 class="text-sm font-semibold uppercase tracking-wider mb-6">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                </a>
                                <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                                </a>
                                <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Form (Right Side) --}}
                <div class="w-full lg:w-3/5 p-10 lg:p-16">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Send us a Message</h3>
                    
                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="ml-3 text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-medium">Please fix the following errors:</p>
                                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="John" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="Doe" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="john@example.com" required>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="+250 788 123 456">
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="How can we help?" required>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="5" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4 resize-none" placeholder="Tell us more about your inquiry..." required>{{ old('message') }}</textarea>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full inline-flex justify-center py-4 px-6 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.website>
