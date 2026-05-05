@extends('layouts.trader')
@section('title', 'Dashboard')

@section('content')

{{-- ── Greeting ── --}}
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = explode(' ', auth()->user()->name)[0];
@endphp

<div class="mb-5">
    <h1 class="text-2xl font-bold text-white">{{ $greeting }}, <span class="gold-text">{{ $firstName }}</span>!</h1>
    <p class="text-gray-500 text-sm mt-0.5">{{ now()->format('l, F j Y') }}</p>
</div>

{{-- ── Disclaimer Banner ── --}}
@if($disclaimer)
<div class="glass mb-5 rounded-xl p-3 border border-[#D4AF37]/30 flex gap-2.5 items-start">
    <i class="fas fa-triangle-exclamation text-[#D4AF37] mt-0.5 shrink-0 text-sm"></i>
    <p class="text-xs text-gray-400 leading-relaxed">{{ $disclaimer }}</p>
</div>
@endif

{{-- ── Stats Row ── --}}
<div class="flex gap-3 overflow-x-auto pb-2 mb-6 scrollbar-hide" style="scrollbar-width:none;">
    {{-- Active Signals --}}
    <div class="glass rounded-xl p-4 flex-shrink-0 w-36 flex flex-col gap-1">
        <span class="text-[10px] uppercase tracking-widest text-gray-500 font-semibold">Active</span>
        <div class="flex items-end gap-1.5 mt-1">
            <span class="text-3xl font-black text-white">{{ $activeSignals }}</span>
            <span class="badge bg-green-500/20 text-green-400 border border-green-500/30 mb-1">live</span>
        </div>
        <span class="text-xs text-gray-600">Signals</span>
    </div>

    {{-- Total Signals --}}
    <div class="glass rounded-xl p-4 flex-shrink-0 w-36 flex flex-col gap-1">
        <span class="text-[10px] uppercase tracking-widest text-gray-500 font-semibold">Total</span>
        <div class="flex items-end gap-1.5 mt-1">
            <span class="text-3xl font-black text-white">{{ $totalSignals }}</span>
            <span class="badge bg-blue-500/20 text-blue-400 border border-blue-500/30 mb-1">all</span>
        </div>
        <span class="text-xs text-gray-600">Signals</span>
    </div>

    {{-- Win Rate --}}
    <div class="glass rounded-xl p-4 flex-shrink-0 w-36 flex flex-col gap-1">
        <span class="text-[10px] uppercase tracking-widest text-gray-500 font-semibold">Win Rate</span>
        <div class="flex items-end gap-1.5 mt-1">
            <span class="text-3xl font-black gold-text">{{ $winRate }}</span>
            <span class="text-xl font-bold text-[#D4AF37] mb-0.5">%</span>
        </div>
        <span class="text-xs text-gray-600">Accuracy</span>
    </div>
</div>

{{-- ── Subscription Status Card ── --}}
@if($subscription)
@php
    $expiring = $subscription->expires_at && $subscription->expires_at->diffInDays(now()) <= 3 && !$subscription->is_expired;
@endphp
<div class="glass rounded-xl p-4 mb-6 border {{ $subscription->is_expired ? 'border-red-500/30' : ($expiring ? 'border-yellow-500/40' : 'border-[#D4AF37]/20') }}">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-crown text-[#D4AF37] text-sm"></i>
                <span class="text-sm font-semibold text-white capitalize">{{ $subscription->plan }} Plan</span>
                @if($subscription->is_expired)
                    <span class="badge bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                @elseif($expiring)
                    <span class="badge bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Expiring soon</span>
                @else
                    <span class="badge bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                @endif
            </div>
            @if($subscription->expires_at)
            <p class="text-xs text-gray-500">
                {{ $subscription->is_expired ? 'Expired' : 'Expires' }}
                {{ $subscription->expires_at->format('M j, Y \a\t g:i A') }}
            </p>
            @endif
        </div>
        @if($subscription->is_expired || $expiring)
        <a href="{{ route('subscription.plans') }}"
           class="shrink-0 px-4 py-2 rounded-lg text-xs font-semibold text-black"
           style="background: linear-gradient(135deg, #D4AF37, #FFD700);">
            Renew
        </a>
        @endif
    </div>
</div>
@else
<div class="glass rounded-xl p-4 mb-6 border border-[#D4AF37]/30 flex items-center justify-between">
    <div>
        <p class="text-sm font-semibold text-white">No active subscription</p>
        <p class="text-xs text-gray-500 mt-0.5">Subscribe to access all AI signals</p>
    </div>
    <a href="{{ route('subscription.plans') }}"
       class="shrink-0 px-4 py-2 rounded-lg text-xs font-semibold text-black"
       style="background: linear-gradient(135deg, #D4AF37, #FFD700);">
        Subscribe
    </a>
</div>
@endif

{{-- ── Live Signals Section ── --}}
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-base font-bold text-white flex items-center gap-2">
        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
        Live AI Signals
    </h2>
    <a href="{{ route('signals.index') }}" class="text-xs font-semibold text-[#D4AF37] hover:text-[#FFD700] transition">
        View All <i class="fas fa-arrow-right ml-0.5 text-[10px]"></i>
    </a>
</div>

@forelse($recentSignals->take(5) as $signal)
@php
    $isBuy = strtolower($signal->type) === 'buy';
    $isActive = $signal->status === 'active';
    $statusMap = [
        'active'   => ['text' => 'Active',  'cls' => 'bg-green-500/15 text-green-400 border-green-500/30'],
        'tp_hit'   => ['text' => 'TP Hit',  'cls' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
        'sl_hit'   => ['text' => 'SL Hit',  'cls' => 'bg-red-500/15 text-red-400 border-red-500/30'],
        'expired'  => ['text' => 'Expired', 'cls' => 'bg-gray-500/15 text-gray-400 border-gray-500/30'],
    ];
    $statusStyle = $statusMap[$signal->status] ?? $statusMap['expired'];
@endphp
<a href="{{ route('signals.show', $signal) }}" class="block mb-3">
    <div class="glass glass-hover rounded-xl p-4 transition-all duration-200">
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="text-base font-bold text-[#D4AF37]">{{ $signal->pair }}</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="badge {{ $isBuy ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                        <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} mr-1 text-[9px]"></i>
                        {{ strtoupper($signal->type) }}
                    </span>
                    <span class="badge border {{ $statusStyle['cls'] }}">{{ $statusStyle['text'] }}</span>
                </div>
            </div>
            <span class="text-[10px] text-gray-600">{{ $signal->created_at->diffForHumans() }}</span>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-3 text-xs">
            <div class="text-center">
                <p class="text-gray-600 text-[10px] uppercase tracking-wide mb-0.5">Entry</p>
                <p class="font-semibold text-white">{{ number_format((float)$signal->entry_price, 5) }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-[10px] uppercase tracking-wide mb-0.5">SL</p>
                <p class="font-semibold text-red-400">{{ number_format((float)$signal->stop_loss, 5) }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-[10px] uppercase tracking-wide mb-0.5">TP</p>
                <p class="font-semibold text-green-400">{{ number_format((float)$signal->take_profit, 5) }}</p>
            </div>
        </div>

        {{-- Confidence Bar --}}
        <div class="mb-2">
            <div class="flex justify-between text-[10px] text-gray-500 mb-1">
                <span>Confidence</span>
                <span class="font-semibold {{ $signal->confidence >= 75 ? 'text-green-400' : ($signal->confidence >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                    {{ $signal->confidence }}%
                </span>
            </div>
            <div class="conf-bar">
                <div class="conf-fill {{ $signal->confidence >= 75 ? 'bg-green-500' : ($signal->confidence >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                     style="width: {{ $signal->confidence }}%"></div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($signal->duration)
            <span class="badge bg-[#1e1e1e] text-gray-400 border border-[#2a2a2a]">
                <i class="fas fa-clock mr-1 text-[9px]"></i>{{ $signal->duration }}
            </span>
            @endif
            @if($signal->category)
            <span class="badge bg-[#1e1e1e] text-gray-400 border border-[#2a2a2a] capitalize">
                {{ $signal->category }}
            </span>
            @endif
        </div>
    </div>
</a>
@empty
<div class="glass rounded-xl p-8 text-center">
    <i class="fas fa-satellite-dish text-3xl text-gray-700 mb-3"></i>
    <p class="text-gray-500 text-sm">No signals available yet.</p>
    <p class="text-gray-700 text-xs mt-1">Our AI is scanning the markets.</p>
</div>
@endforelse

@endsection
