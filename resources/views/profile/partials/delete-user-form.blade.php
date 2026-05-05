<section>
    <p class="text-gray-400 text-sm mb-4">Once your account is deleted, all data will be permanently removed. This action cannot be undone.</p>

    <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')"
        class="px-5 py-2.5 rounded-xl font-semibold text-sm bg-red-900/30 text-red-400 border border-red-800/40 hover:bg-red-900/50 transition">
        <i class="fas fa-trash mr-2"></i> Delete My Account
    </button>

    {{-- Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" style="background:rgba(0,0,0,0.8)">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-red-800/30">
            <h3 class="text-white font-bold text-lg mb-2 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-red-400"></i> Delete Account?
            </h3>
            <p class="text-gray-400 text-sm mb-5">This action is permanent. Enter your password to confirm.</p>
            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf @method('delete')
                <input type="password" name="password" placeholder="Your current password"
                    class="w-full bg-[#1a1a1a] border border-red-700/30 text-white rounded-xl px-4 py-3 text-sm focus:outline-none">
                @if($errors->userDeletion->get('password'))
                <p class="text-red-400 text-xs">{{ $errors->userDeletion->first('password') }}</p>
                @endif
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-2.5 rounded-xl font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition">Delete Forever</button>
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="flex-1 py-2.5 rounded-xl font-semibold text-sm glass text-gray-300 hover:text-white transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</section>
