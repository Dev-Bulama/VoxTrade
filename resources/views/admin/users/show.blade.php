@extends('layouts.admin')
@section('title', 'User — ' . $user->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="w-9 h-9 glass rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
            <p class="text-gray-500 text-sm">User Detail</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- User Info --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="glass rounded-2xl p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center font-black text-3xl text-black mb-3" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                    <h3 class="text-white font-bold text-lg">{{ $user->name }}</h3>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $user->status === 'active' ? 'bg-green-900/40 text-green-400 border border-green-700/30' : 'bg-red-900/40 text-red-400 border border-red-700/30' }}">{{ ucfirst($user->status) }}</span>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="text-white">{{ $user->phone ?? 'Not provided' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Role</span><span class="text-[#D4AF37] font-medium capitalize">{{ $user->role }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Joined</span><span class="text-white">{{ $user->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold {{ $user->status === 'active' ? 'bg-yellow-900/30 text-yellow-400 border border-yellow-700/30 hover:bg-yellow-900/50' : 'bg-green-900/30 text-green-400 border border-green-700/30 hover:bg-green-900/50' }} transition">
                        <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }} mr-1"></i>
                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-900/30 text-red-400 border border-red-700/30 hover:bg-red-900/50 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- History --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Subscriptions --}}
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[#D4AF37]/10 flex items-center gap-2">
                    <i class="fas fa-crown text-[#D4AF37]"></i>
                    <h3 class="text-white font-semibold">Subscription History</h3>
                </div>
                <div class="divide-y divide-[#1a1a1a]">
                    @forelse($user->subscriptions->sortByDesc('created_at') as $sub)
                    <div class="px-5 py-3 flex items-center justify-between text-sm">
                        <div>
                            <p class="text-white font-medium capitalize">{{ $sub->plan }} Plan</p>
                            <p class="text-gray-500 text-xs">₦{{ number_format($sub->amount, 2) }} · {{ $sub->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $sub->status === 'active' && $sub->expires_at > now() ? 'bg-green-900/30 text-green-400' : 'bg-gray-900/30 text-gray-400' }}">{{ $sub->status === 'active' && $sub->expires_at > now() ? 'Active' : 'Expired' }}</span>
                            <p class="text-gray-500 text-xs mt-1">Exp: {{ $sub->expires_at ? $sub->expires_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-500 text-sm">No subscriptions found.</div>
                    @endforelse
                </div>
            </div>

            {{-- Payments --}}
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[#D4AF37]/10 flex items-center gap-2">
                    <i class="fas fa-receipt text-[#D4AF37]"></i>
                    <h3 class="text-white font-semibold">Payment History</h3>
                </div>
                <div class="divide-y divide-[#1a1a1a]">
                    @forelse($user->payments->sortByDesc('created_at') as $payment)
                    <div class="px-5 py-3 flex items-center justify-between text-sm">
                        <div>
                            <p class="text-white font-medium">{{ $payment->reference }}</p>
                            <p class="text-gray-500 text-xs">{{ ucfirst($payment->gateway) }} · {{ $payment->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-semibold">₦{{ number_format($payment->amount, 2) }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $payment->status === 'successful' ? 'bg-green-900/30 text-green-400' : ($payment->status === 'pending' ? 'bg-yellow-900/30 text-yellow-400' : 'bg-red-900/30 text-red-400') }}">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-500 text-sm">No payments found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
