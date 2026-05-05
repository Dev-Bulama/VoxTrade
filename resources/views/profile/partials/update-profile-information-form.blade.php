<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf @method('patch')

        <div>
            <label class="block text-sm text-gray-400 mb-2">Full Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            @error('name', 'updateProfileInformation')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-2">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            @error('email', 'updateProfileInformation')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 p-3 rounded-xl bg-yellow-900/20 border border-yellow-700/30">
                <p class="text-yellow-400 text-xs">Your email is unverified.
                    <button form="send-verification" class="underline ml-1">Re-send verification</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                <p class="text-green-400 text-xs mt-1">Verification link sent!</p>
                @endif
            </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
            @if (session('status') === 'profile-updated')
            <span class="text-green-400 text-sm"><i class="fas fa-check mr-1"></i>Saved!</span>
            @endif
        </div>
    </form>
</section>
