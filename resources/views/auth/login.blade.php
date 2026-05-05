<x-guest-layout>
<div class="w-full max-w-md">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center gold-gradient">
                <i class="fas fa-chart-line text-black text-xl"></i>
            </div>
        </a>
        <h1 class="text-3xl font-black text-white mb-1">Welcome Back</h1>
        <p class="text-gray-400 text-sm">Sign in to access your trade signals</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
    <div class="mb-4 p-3 rounded-xl bg-green-900/30 border border-green-700/40 text-green-400 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Form --}}
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="you@example.com"
                    class="input-dark @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm text-gray-400">Password</label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-[#D4AF37] hover:underline">Forgot password?</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                    placeholder="••••••••"
                    class="input-dark @error('password') border-red-500 @enderror">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 accent-yellow-500 rounded">
                <label for="remember_me" class="text-sm text-gray-400">Remember me</label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-black text-sm transition hover:opacity-90 gold-gradient mt-2">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-gray-500 mt-5">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-[#D4AF37] font-semibold hover:underline ml-1">Create Account</a>
    </p>
    <p class="text-center mt-3">
        <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-400 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to home
        </a>
    </p>
</div>
</x-guest-layout>
