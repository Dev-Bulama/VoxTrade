<x-guest-layout>
<div class="w-full max-w-md">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center gold-gradient">
                <i class="fas fa-chart-line text-black text-xl"></i>
            </div>
        </a>
        <h1 class="text-3xl font-black text-white mb-1">Join <span class="gold-text">VoxTrade</span></h1>
        <p class="text-gray-400 text-sm">Create your account and start trading smarter</p>
    </div>

    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-2">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="John Doe"
                    class="input-dark @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    placeholder="you@example.com"
                    class="input-dark @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Password</label>
                <input id="password" type="password" name="password" required
                    placeholder="Minimum 8 characters"
                    class="input-dark @error('password') border-red-500 @enderror">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    placeholder="Repeat password"
                    class="input-dark">
            </div>

            <div class="pt-1">
                <p class="text-xs text-gray-500 mb-3">By creating an account you agree to our
                    <a href="{{ route('terms') }}" class="text-[#D4AF37] hover:underline">Terms & Disclaimer</a>.
                </p>
                <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-black text-sm transition hover:opacity-90 gold-gradient">
                    <i class="fas fa-rocket mr-2"></i> Create Account
                </button>
            </div>
        </form>
    </div>

    <p class="text-center text-sm text-gray-500 mt-5">
        Already have an account?
        <a href="{{ route('login') }}" class="text-[#D4AF37] font-semibold hover:underline ml-1">Sign In</a>
    </p>
    <p class="text-center mt-3">
        <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-400 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to home
        </a>
    </p>
</div>
</x-guest-layout>
