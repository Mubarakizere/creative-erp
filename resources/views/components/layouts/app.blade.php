<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Creative ERP' }} - Creative ERP</title>
    <meta name="description" content="{{ $description ?? 'Creative ERP - Enterprise Resource Planning for Engineering & Construction' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Charting Library -->
    <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @stack('styles')
    
    <!-- Global Loading State & Progress Bar -->
    <style>
        #global-progress {
            width: 0%;
            transition: width 0.3s ease, opacity 0.3s ease;
        }
        .progress-loading #global-progress {
            width: 75%;
            transition: width 10s cubic-bezier(0.1, 0.5, 0.5, 1);
        }
        .progress-complete #global-progress {
            width: 100%;
            opacity: 0;
            transition: width 0.3s ease, opacity 0.3s ease 0.3s;
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('loading', {
                isGlobalLoading: false,
                isSubmitting: false,
                start() {
                    this.isGlobalLoading = true;
                    document.body.classList.remove('progress-complete');
                    document.body.classList.add('progress-loading');
                },
                stop() {
                    this.isGlobalLoading = false;
                    document.body.classList.remove('progress-loading');
                    document.body.classList.add('progress-complete');
                }
            });
            
            // Intercept standard navigation to show progress bar
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.startsWith('#') && link.target !== '_blank' && !e.ctrlKey && !e.metaKey && !link.hasAttribute('download')) {
                    // Only intercept internal links
                    if (link.hostname === window.location.hostname) {
                        Alpine.store('loading').start();
                    }
                }
            });
            
            // Re-enable on back button / cache reload
            window.addEventListener('pageshow', (e) => {
                Alpine.store('loading').stop();
            });
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <!-- Top Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1 z-[100] pointer-events-none">
        <div id="global-progress" class="h-full bg-blue-600 shadow-[0_0_10px_#2563eb]"></div>
    </div>
    {{ $slot }}

    @if(config('realtime.features.toast', true))
        <x-toast />
    @endif
    @stack('scripts')
    
    <!-- Global Error Handling & Auto-scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Find the first element with a validation error class
            const firstError = document.querySelector('.border-red-300, .text-red-600');
            if (firstError) {
                // Scroll into view with a slight offset
                const y = firstError.getBoundingClientRect().top + window.scrollY - 100;
                window.scrollTo({ top: y, behavior: 'smooth' });
                
                // Focus the input if it is an input field
                if(firstError.tagName === 'INPUT' || firstError.tagName === 'SELECT' || firstError.tagName === 'TEXTAREA') {
                    firstError.focus({ preventScroll: true });
                } else {
                    // Try to find the closest input to focus
                    const input = firstError.closest('div').querySelector('input, select, textarea');
                    if (input) input.focus({ preventScroll: true });
                }
            }
        });
    </script>
</body>
</html>
