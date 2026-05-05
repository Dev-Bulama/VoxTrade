@extends('layouts.trader')
@section('title', 'Performance')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">Performance <span class="gold-text">Analytics</span></h1>
        <p class="text-gray-500 text-sm mt-1">AI signal performance overview</p>
    </div>

    {{-- Win Rate Big Display --}}
    <div class="glass rounded-2xl p-6 text-center gold-border">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Overall Win Rate</p>
        <div class="relative inline-flex items-center justify-center">
            <svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="50" fill="none" stroke="#1e1e1e" stroke-width="12"/>
                <circle cx="60" cy="60" r="50" fill="none" stroke="url(#gold)" stroke-width="12"
                    stroke-dasharray="{{ round($winRate * 3.14159) }} 314.159"
                    stroke-linecap="round"/>
                <defs>
                    <linearGradient id="gold" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#D4AF37"/>
                        <stop offset="100%" stop-color="#FFD700"/>
                    </linearGradient>
                </defs>
            </svg>
            <div class="absolute text-center">
                <p class="text-3xl font-black gold-text">{{ $winRate }}%</p>
                <p class="text-xs text-gray-500">Win Rate</p>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="glass rounded-2xl p-5 text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-chart-bar text-blue-400"></i>
            </div>
            <p class="text-2xl font-black text-white">{{ $totalSignals }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Signals</p>
        </div>
        <div class="glass rounded-2xl p-5 text-center">
            <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-circle-check text-green-400"></i>
            </div>
            <p class="text-2xl font-black text-green-400">{{ $tpHit }}</p>
            <p class="text-xs text-gray-500 mt-1">TP Hit (Wins)</p>
        </div>
        <div class="glass rounded-2xl p-5 text-center">
            <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-circle-xmark text-red-400"></i>
            </div>
            <p class="text-2xl font-black text-red-400">{{ $slHit }}</p>
            <p class="text-xs text-gray-500 mt-1">SL Hit (Losses)</p>
        </div>
        <div class="glass rounded-2xl p-5 text-center">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-bolt text-yellow-400"></i>
            </div>
            <p class="text-2xl font-black text-yellow-400">{{ $active }}</p>
            <p class="text-xs text-gray-500 mt-1">Active Now</p>
        </div>
    </div>

    {{-- Market Breakdown --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-globe text-[#D4AF37]"></i> Market Breakdown
        </h3>
        @php $total = max($forexSignals + $cryptoSignals, 1); @endphp
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-300 flex items-center gap-2"><i class="fas fa-money-bill-trend-up text-[#D4AF37]"></i> Forex</span>
                    <span class="text-white font-semibold">{{ $forexSignals }} signals</span>
                </div>
                <div class="h-2 bg-[#1e1e1e] rounded-full"><div class="h-2 rounded-full" style="background:linear-gradient(90deg,#D4AF37,#FFD700);width:{{ round($forexSignals/$total*100) }}%"></div></div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-300 flex items-center gap-2"><i class="fab fa-bitcoin text-orange-400"></i> Crypto</span>
                    <span class="text-white font-semibold">{{ $cryptoSignals }} signals</span>
                </div>
                <div class="h-2 bg-[#1e1e1e] rounded-full"><div class="h-2 rounded-full bg-orange-400" style="width:{{ round($cryptoSignals/$total*100) }}%"></div></div>
            </div>
        </div>
    </div>

    {{-- Recent Trade History --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-[#D4AF37]/10">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <i class="fas fa-history text-[#D4AF37]"></i> Recent Signals
            </h3>
        </div>
        <div class="divide-y divide-[#1e1e1e]">
            @forelse($recentTrades as $trade)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">{{ $trade->pair }}</p>
                    <p class="text-gray-500 text-xs">{{ $trade->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-2 py-0.5 rounded {{ $trade->type === 'BUY' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">{{ $trade->type }}</span>
                    @php
                        $statusMap = ['active'=>'bg-yellow-500/20 text-yellow-400','tp_hit'=>'bg-green-500/20 text-green-400','sl_hit'=>'bg-red-500/20 text-red-400','expired'=>'bg-gray-500/20 text-gray-400'];
                        $statusLabel = ['active'=>'Active','tp_hit'=>'TP Hit','sl_hit'=>'SL Hit','expired'=>'Expired'];
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded {{ $statusMap[$trade->status] ?? 'bg-gray-500/20 text-gray-400' }}">{{ $statusLabel[$trade->status] ?? $trade->status }}</span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-500 text-sm">
                <i class="fas fa-chart-line text-2xl mb-2 block opacity-30"></i>
                No trade history yet.
            </div>
            @endforelse
        </div>
    </div>

    <div class="glass rounded-xl p-3 border border-[#D4AF37]/20 text-center">
        <p class="text-xs text-gray-500"><i class="fas fa-triangle-exclamation text-[#D4AF37] mr-1"></i>Past performance does not guarantee future results.</p>
    </div>
</div>
@endsection
