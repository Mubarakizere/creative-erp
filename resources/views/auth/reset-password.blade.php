<x-layouts.auth title="Reset Password">
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-white">Reset Password</h2>
        <p class="mt-2 text-sm text-blue-200/60">
            Enter your new password below.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="space-y-1.5">
            <label for="email" class="block text-sm font-medium text-blue-100">Email Address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $email) }}"
                required
                autocomplete="email"
                class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:bg-white/15 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 transition-all duration-200 text-sm"
            >
            @error('email')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="space-y-1.5">
            <label for="password" class="block text-sm font-medium text-blue-100">New Password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    name="password"
                    :type="show ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                    class="block w-full px-4 py-3 pr-12 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:bg-white/15 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 transition-all duration-200 text-sm"
                >
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-200/50 hover:text-blue-200 transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-sm font-medium text-blue-100">Confirm Password</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Re-enter your password"
                class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:bg-white/15 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 transition-all duration-200 text-sm"
            >
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400/50 focus:ring-offset-2 focus:ring-offset-transparent shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-200"
        >
            Reset Password
        </button>

        {{-- Back to Login --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-300 hover:text-blue-100 transition-colors inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Sign In
            </a>
        </div>
    </form>
</x-layouts.auth>
