@extends('layouts.trader')
@section('title', 'Subscription Status')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">Subscription <span class="gold-text">Status</span></h1>
        <p class="text-gray-500 text-sm mt-1">Manage your VoxTrade subscription</p>
    </div>

    @if($subscription && $subscription->status === 'active' && $subscription->expires_at > now())
    <div class="glass rounded-2xl p-6 border border-[#D4AF37]/30">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-crown text-black text-2xl"></i>
            </div>
            <div>
                <p class="text-white font-bold text-xl">{{ ucfirst($subscription->plan) }} Plan</p>
                <span class="text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full border border-green-500/30">
                    <i class="fas fa-circle text-xs mr-1"></i>Active
                </span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-[#1a1a1a] rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Plan</p>
                <p class="text-white font-semibold">{{ ucfirst($subscription->plan) }}</p>
            </div>
            <div class="bg-[#1a1a1a] rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Amount Paid</p>
                <p class="text-white font-semibold">₦{{ number_format($subscription->amount, 2) }}</p>
            </div>
            <div class="bg-[#1a1a1a] rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Started</p>
                <p class="text-white font-semibold text-sm">{{ $subscription->created_at->format('M d, Y') }}</p>
            </div>
            <div class="bg-[#1a1a1a] rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Expires</p>
                <p class="text-white font-semibold text-sm">{{ $subscription->expires_at->format('M d, Y') }}</p>
            </div>
        </div>
        @if($subscription->expires_at->diffInDays(now()) <= 2)
        <div class="p-3 rounded-xl bg-yellow-900/20 border border-yellow-600/30 mb-4 text-sm text-yellow-400 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i> Expires in {{ $subscription->expires_at->diffForHumans() }} — Consider renewing.
        </div>
        @endif
        <a href="{{ route('subscription.plans') }}" class="block text-center py-3 rounded-xl font-bold text-sm border border-[#D4AF37]/30 text-[#D4AF37] hover:bg-white/5 transition">
            <i class="fas fa-rotate mr-2"></i> Renew / Upgrade Plan
        </a>
    </div>
    @else
    <div class="glass rounded-2xl p-8 text-center border border-red-500/20">
        <div class="w-16 h-16 rounded-full bg-red-900/30 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-crown text-red-400 text-2xl"></i>
        </div>
        <h3 class="text-white font-bold text-xl mb-2">No Active Subscription</h3>
        <p class="text-gray-400 text-sm mb-6">Subscribe to access live AI trade signals.</p>
        <a href="{{ route('subscription.plans') }}" class="inline-block px-8 py-3 rounded-xl font-bold text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
            <i class="fas fa-crown mr-2"></i> View Plans
        </a>
    </div>
    @endif

    <div class="glass rounded-xl p-3 border border-[#D4AF37]/20 text-center">
        <p class="text-xs text-gray-500"><i class="fas fa-shield-halved text-[#D4AF37] mr-1"></i>Payments secured by Paystack. Need help? Contact support.</p>
    </div>
</div>
@endsection
