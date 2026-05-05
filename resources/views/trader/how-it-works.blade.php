@extends('layouts.trader')
@section('title', 'How It Works')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#D4AF37] transition mb-4">
        <i class="fas fa-arrow-left text-xs"></i> Back to Dashboard
    </a>
    <h1 class="text-2xl font-black text-white">How <span class="gold-text">VoxTrade</span> Works</h1>
    <p class="text-gray-500 text-sm mt-1">Everything you need to know to earn from AI signals</p>
</div>

{{-- ── Hero Summary ── --}}
<div class="glass rounded-2xl p-5 mb-6 border border-[#D4AF37]/20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background:radial-gradient(circle at 70% 50%, #D4AF37, transparent 60%);"></div>
    <div class="relative z-10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">
                <i class="fas fa-robot text-black text-lg"></i>
            </div>
            <div>
                <p class="text-base font-black text-white">AI-Powered Trade Intelligence</p>
                <p class="text-[11px] text-gray-400">Scans {{ count($watchedPairs) }} pairs every 5 minutes, 24/7</p>
            </div>
        </div>
        <p class="text-sm text-gray-300 leading-relaxed">
            VoxTrade's AI engine continuously analyzes live market data across {{ count($watchedPairs) }} forex and crypto pairs using
            real-time price feeds, technical indicators (RSI, EMA), trend analysis, and historical trade performance.
            It only posts signals it believes have a <span class="text-[#D4AF37] font-bold">≥70% probability</span> of hitting Take Profit before Stop Loss.
        </p>
    </div>
</div>

{{-- ── Step-by-Step ── --}}
<h2 class="text-sm font-bold text-gray-300 uppercase tracking-widest mb-4 flex items-center gap-2">
    <i class="fas fa-list-ol text-[#D4AF37]"></i> Step-by-Step Guide
</h2>

<div class="space-y-4 mb-6">

    {{-- Step 1 --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-black font-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">1</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-white mb-2">Check your Dashboard for Active Signals</p>
                <p class="text-[12px] text-gray-400 leading-relaxed mb-3">
                    Open VoxTrade and go to <strong class="text-white">Home (Dashboard)</strong>.
                    You'll see a grid of active buy/sell signals. Each card shows the pair, direction (BUY or SELL),
                    entry price, stop loss, take profit, and AI confidence.
                </p>
                <div class="flex gap-2 flex-wrap">
                    <span class="badge bg-green-500/15 text-green-400 border border-green-500/30 text-[10px]"><i class="fas fa-arrow-trend-up mr-1"></i>BUY = expect price to rise</span>
                    <span class="badge bg-red-500/15 text-red-400 border border-red-500/30 text-[10px]"><i class="fas fa-arrow-trend-down mr-1"></i>SELL = expect price to fall</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2 --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-black font-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">2</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-white mb-2">Tap "Copy MT4" to get signal details</p>
                <p class="text-[12px] text-gray-400 leading-relaxed mb-3">
                    Each signal card has a <strong class="text-[#D4AF37]">Copy MT4</strong> button.
                    Tap it to copy the full signal parameters to your clipboard in MetaTrader-ready format.
                </p>
                <div class="bg-[#0d0d0d] rounded-xl p-3 border border-[#2a2a2a] font-mono text-[10px] text-gray-400">
                    === VoxTrade AI Signal ===<br>
                    Symbol: EURUSD<br>
                    Action: <span class="text-green-400">BUY</span><br>
                    Entry Price: 1.08450<br>
                    Stop Loss: <span class="text-red-400">1.07900</span><br>
                    Take Profit: <span class="text-green-400">1.09300</span><br>
                    Hold Duration: 4 hours<br>
                    AI Confidence: 82%<br>
                    =========================
                </div>
            </div>
        </div>
    </div>

    {{-- Step 3 --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-black font-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">3</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-white mb-2">Open MetaTrader 4 or MetaTrader 5</p>
                <p class="text-[12px] text-gray-400 leading-relaxed mb-3">
                    Launch MetaTrader 4 (MT4) or MetaTrader 5 (MT5) on your phone or desktop.
                    Find the currency pair in your watchlist (e.g. EURUSD).
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-[#111] rounded-xl p-3 border border-[#2a2a2a]">
                        <p class="text-[10px] font-bold text-white mb-1">MetaTrader 4</p>
                        <p class="text-[10px] text-gray-500">Best for Forex. Available on iOS, Android & Desktop.</p>
                    </div>
                    <div class="bg-[#111] rounded-xl p-3 border border-[#2a2a2a]">
                        <p class="text-[10px] font-bold text-white mb-1">MetaTrader 5</p>
                        <p class="text-[10px] text-gray-500">Supports Forex + Crypto. More advanced features.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4 --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-black font-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">4</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-white mb-2">Place the trade in MetaTrader</p>
                <p class="text-[12px] text-gray-400 leading-relaxed mb-3">
                    In MetaTrader, find the pair and open a <strong class="text-white">New Order</strong>. Enter the values from your signal:
                </p>
                <div class="space-y-2 text-[11px]">
                    <div class="flex items-center gap-3 p-2 bg-[#111] rounded-lg">
                        <i class="fas fa-crosshairs text-[#D4AF37] w-4 text-center"></i>
                        <div><span class="text-gray-500">Type:</span> <span class="text-white font-semibold">Market Buy or Market Sell (match the signal direction)</span></div>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-[#111] rounded-lg">
                        <i class="fas fa-coins text-[#D4AF37] w-4 text-center"></i>
                        <div><span class="text-gray-500">Volume:</span> <span class="text-white font-semibold">0.01 lots for beginners (never risk more than 1–2% per trade)</span></div>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-[#111] rounded-lg">
                        <i class="fas fa-shield-xmark text-red-400 w-4 text-center"></i>
                        <div><span class="text-gray-500">Stop Loss:</span> <span class="text-red-400 font-semibold">Enter the SL price from the signal exactly</span></div>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-[#111] rounded-lg">
                        <i class="fas fa-bullseye text-green-400 w-4 text-center"></i>
                        <div><span class="text-gray-500">Take Profit:</span> <span class="text-green-400 font-semibold">Enter the TP price from the signal exactly</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 5 --}}
    <div class="glass rounded-2xl p-5 border border-[#D4AF37]/20">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-black font-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">5</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-white mb-2">Wait for TP or SL — do NOT interfere</p>
                <p class="text-[12px] text-gray-400 leading-relaxed">
                    Once the trade is placed, <strong class="text-white">do not move your Stop Loss or Take Profit</strong>.
                    The AI has calculated these levels based on technical analysis. Trust the system.
                    Your trade will either hit <span class="text-green-400 font-semibold">Take Profit (WIN)</span> or
                    <span class="text-red-400 font-semibold">Stop Loss (LOSS)</span> automatically.
                    Come back to VoxTrade — signal status will update accordingly.
                </p>
            </div>
        </div>
    </div>

</div>

{{-- ── Pairs Monitored ── --}}
<h2 class="text-sm font-bold text-gray-300 uppercase tracking-widest mb-4 flex items-center gap-2">
    <i class="fas fa-radar text-[#D4AF37]"></i> Pairs the AI Monitors
</h2>
<div class="glass rounded-2xl p-4 mb-6">
    <div class="grid grid-cols-2 gap-2">
        @foreach($watchedPairs as $pair)
        <div class="flex items-center gap-2 bg-[#111]/60 rounded-xl p-3">
            <i class="fas fa-{{ $pair['type'] === 'crypto' ? 'bitcoin-sign' : 'chart-line' }} text-[#D4AF37] text-xs w-4 text-center"></i>
            <div>
                <p class="text-xs font-bold text-white">{{ $pair['display'] }}</p>
                <p class="text-[10px] text-gray-500 capitalize">{{ $pair['type'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <p class="text-[10px] text-gray-600 mt-3">
        <i class="fas fa-sync-alt mr-1 text-[#D4AF37]"></i>
        All pairs are analyzed every 5 minutes. New signals are published only when the AI confidence is ≥70%.
    </p>
</div>

{{-- ── Risk Management ── --}}
<h2 class="text-sm font-bold text-gray-300 uppercase tracking-widest mb-4 flex items-center gap-2">
    <i class="fas fa-shield-halved text-[#D4AF37]"></i> Risk Management Rules
</h2>
<div class="glass rounded-2xl p-4 mb-6">
    <div class="space-y-3">
        <div class="flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-400 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[12px] text-gray-300"><strong class="text-white">Risk only 1–2% per trade.</strong> If your account is $1,000, never lose more than $10–20 on a single trade. Use 0.01 lots for small accounts.</p>
        </div>
        <div class="flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-400 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[12px] text-gray-300"><strong class="text-white">Never skip the Stop Loss.</strong> Every VoxTrade signal includes a calculated SL. Always set it in MetaTrader — it protects your account if the trade moves against you.</p>
        </div>
        <div class="flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-400 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[12px] text-gray-300"><strong class="text-white">Don't trade every signal.</strong> Only take signals with confidence ≥75% if you want higher probability trades. Lower confidence signals carry more risk.</p>
        </div>
        <div class="flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-400 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[12px] text-gray-300"><strong class="text-white">Respect the duration.</strong> If a signal says "4 hours", the trade should complete within that window. If you're past the duration and TP hasn't been hit, consider closing manually.</p>
        </div>
        <div class="flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-400 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[12px] text-gray-300"><strong class="text-white">Compound your gains.</strong> Don't withdraw every win. Reinvest to grow your lot size gradually. Consistent 1:1.5 R:R trades compound quickly over time.</p>
        </div>
    </div>
</div>

{{-- ── Risk Disclaimer ── --}}
<div class="rounded-xl p-4 mb-2 border border-yellow-500/20 bg-yellow-500/5">
    <div class="flex gap-3 items-start">
        <i class="fas fa-triangle-exclamation text-yellow-400 mt-0.5 shrink-0"></i>
        <div>
            <p class="text-xs font-bold text-yellow-400 mb-1">Important Disclaimer</p>
            <p class="text-[11px] text-gray-400 leading-relaxed">
                VoxTrade provides AI-generated trade signals for educational and analytical purposes only.
                These are <strong class="text-white">not financial advice</strong>. Trading forex and cryptocurrency involves substantial risk of loss.
                Past signal performance does not guarantee future results. Never trade with money you cannot afford to lose.
                Always use proper risk management and consult a licensed financial advisor before trading.
            </p>
        </div>
    </div>
</div>

<div class="text-center mt-6 pb-2">
    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-black font-bold text-sm transition hover:opacity-90"
       style="background:linear-gradient(135deg,#D4AF37,#FFD700);">
        <i class="fas fa-bolt"></i> View Live Signals
    </a>
</div>

@endsection
