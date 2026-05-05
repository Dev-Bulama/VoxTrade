@extends('layouts.trader')
@section('title', 'Subscription Plans')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">Choose Your <span class="gold-text">Plan</span></h1>
        <p class="text-gray-500 text-sm mt-1">Unlock AI-powered trade signals</p>
    </div>

    {{-- Current Subscription Status --}}
    @if($subscription && $subscription->status === 'active' && $subscription->expires_at > now())
    <div class="glass rounded-2xl p-4 border border-green-500/30 bg-green-900/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-crown text-green-400"></i>
            </div>
            <div>
                <p class="text-green-400 font-semibold text-sm">Active Subscription</p>
                <p class="text-gray-400 text-xs">{{ ucfirst($subscription->plan) }} Plan · Expires {{ $subscription->expires_at->format('M d, Y') }}</p>
            </div>
            <span class="ml-auto text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full border border-green-500/30">Active</span>
        </div>
    </div>
    @elseif($subscription)
    <div class="glass rounded-2xl p-4 border border-red-500/30 bg-red-900/10">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-400 text-xl flex-shrink-0"></i>
            <div>
                <p class="text-red-400 font-semibold text-sm">Subscription Expired</p>
                <p class="text-gray-400 text-xs">Renew below to regain access to signals.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Plan Cards --}}
    <div class="space-y-4">
        @foreach($plans as $key => $plan)
        @php $isPopular = $key === 'monthly'; $isCurrent = $subscription && $subscription->plan === $key && $subscription->status === 'active' && $subscription->expires_at > now(); @endphp
        <div class="rounded-2xl {{ $isPopular ? 'p-0.5' : '' }}" style="{{ $isPopular ? 'background:linear-gradient(135deg,#D4AF37,#FFD700);box-shadow:0 0 30px rgba(212,175,55,0.2)' : '' }}">
            <div class="glass rounded-2xl p-5 {{ $isPopular ? 'rounded-[14px]' : '' }}">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        @if($isPopular)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full mb-2 inline-block" style="background:linear-gradient(135deg,#D4AF37,#FFD700);color:#000">MOST POPULAR</span>
                        @endif
                        @if($isCurrent)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full mb-2 inline-block bg-green-500/20 text-green-400 border border-green-500/30">CURRENT PLAN</span>
                        @endif
                        <h3 class="text-white font-bold text-xl flex items-center gap-2">
                            <i class="fas fa-crown text-[#D4AF37]"></i> {{ $plan['name'] }}
                        </h3>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-white">₦{{ number_format($plan['price']) }}</p>
                        <p class="text-gray-500 text-xs">per {{ $key === 'daily' ? 'day' : ($key === 'weekly' ? 'week' : 'month') }}</p>
                    </div>
                </div>
                <ul class="space-y-2 mb-5">
                    @foreach($plan['features'] as $feature)
                    <li class="flex items-center gap-2 text-sm text-gray-300">
                        <i class="fas fa-check text-[#D4AF37] text-xs flex-shrink-0"></i> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                @if(!$isCurrent)
                <form method="POST" action="{{ route('subscription.initialize') }}">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $key }}">
                    <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm transition {{ $isPopular ? 'text-black hover:opacity-90' : 'border border-[#D4AF37]/40 text-[#D4AF37] hover:bg-white/5' }}" style="{{ $isPopular ? 'background:linear-gradient(135deg,#D4AF37,#FFD700)' : '' }}">
                        <i class="fas fa-lock mr-2"></i> Subscribe — ₦{{ number_format($plan['price']) }}
                    </button>
                </form>
                @else
                <button disabled class="w-full py-3 rounded-xl font-bold text-sm bg-green-900/20 text-green-400 border border-green-500/30 cursor-not-allowed">
                    <i class="fas fa-check-circle mr-2"></i> Current Plan
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Security note --}}
    <div class="glass rounded-xl p-4 border border-[#D4AF37]/15 text-center">
        <p class="text-xs text-gray-500 flex items-center justify-center gap-2">
            <i class="fas fa-shield-halved text-[#D4AF37]"></i>
            Payments secured by <span class="text-[#D4AF37] font-semibold">Paystack</span> — SSL encrypted
        </p>
    </div>

    <div class="glass rounded-xl p-3 border border-[#D4AF37]/20 text-center">
        <p class="text-xs text-gray-500"><i class="fas fa-triangle-exclamation text-[#D4AF37] mr-1"></i>AI-assisted signals only. Not financial advice. Trading involves risk.</p>
    </div>
</div>
@endsection
