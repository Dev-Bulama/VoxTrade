<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf @method('put')
        <div>
            <label class="block text-sm text-gray-400 mb-2">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password"
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            @if($errors->updatePassword->get('current_password'))
            <p class="text-red-400 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-2">New Password</label>
            <input id="update_password_password" name="password" type="password"
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            @if($errors->updatePassword->get('password'))
            <p class="text-red-400 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-2">Confirm New Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-key mr-1"></i> Update Password
            </button>
            @if (session('status') === 'password-updated')
            <span class="text-green-400 text-sm"><i class="fas fa-check mr-1"></i>Updated!</span>
            @endif
        </div>
    </form>
</section>
