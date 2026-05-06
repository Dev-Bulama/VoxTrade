@extends('layouts.trader')
@section('title', 'Dashboard')

@section('content')

@php
    $hour      = now()->hour;
    $greeting  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = explode(' ', auth()->user()->name)[0];
@endphp

{{-- ── Greeting ── --}}
<div class="mb-4 flex items-start justify-between">
    <div>
        <h1 class="text-xl font-bold text-white">{{ $greeting }}, <span class="gold-text">{{ $firstName }}</span>!</h1>
        <p class="text-gray-600 text-xs mt-0.5">{{ now()->format('l, F j Y · g:i A') }}</p>
    </div>
    <a href="{{ route('how-it-works') }}"
       class="shrink-0 flex items-center gap-1.5 text-[11px] text-[#D4AF37] border border-[#D4AF37]/30 rounded-full px-3 py-1.5 hover:bg-[#D4AF37]/10 transition">
        <i class="fas fa-circle-question text-xs"></i> How it works
    </a>
</div>

{{-- ── AI Scanning Status Bar ── --}}
<div class="glass rounded-xl px-4 py-3 mb-5 border border-green-500/20 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <span class="relative flex h-2.5 w-2.5 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
        </span>
        <div>
            <p class="text-xs font-bold text-white">AI Engine Active</p>
            <p class="text-[10px] text-gray-500">Scanning {{ count($watchedPairs) }} pairs · refreshes every 5 min</p>
        </div>
    </div>
    <div class="text-right">
        <p class="text-[10px] text-gray-500">Last checked</p>
        <p class="text-sm font-black text-[#D4AF37]" id="lastChecked">now</p>
    </div>
</div>

{{-- ── Stats Row ── --}}
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="glass rounded-xl p-3 text-center">
        <p class="text-[9px] text-gray-500 uppercase tracking-widest mb-1">Active</p>
        <p class="text-2xl font-black text-white" id="activeCount">{{ $activeCount }}</p>
        <p class="text-[10px] text-green-400 mt-0.5">signals</p>
    </div>
    <div class="glass rounded-xl p-3 text-center">
        <p class="text-[9px] text-gray-500 uppercase tracking-widest mb-1">Win Rate</p>
        <p class="text-2xl font-black gold-text">{{ $winRate }}</p>
        <p class="text-[10px] text-[#D4AF37] mt-0.5">percent</p>
    </div>
    <div class="glass rounded-xl p-3 text-center">
        <p class="text-[9px] text-gray-500 uppercase tracking-widest mb-1">Pairs</p>
        <p class="text-2xl font-black text-white">{{ count($watchedPairs) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">monitored</p>
    </div>
</div>

{{-- ── Subscription alert ── --}}
@if($subscription)
@php $expiring = $subscription->expires_at && $subscription->expires_at->diffInDays(now()) <= 3 && !$subscription->is_expired; @endphp
@if($subscription->is_expired || $expiring)
<div class="rounded-xl p-3 mb-5 border {{ $subscription->is_expired ? 'border-red-500/30 bg-red-500/5' : 'border-yellow-500/30 bg-yellow-500/5' }} flex items-center justify-between">
    <div class="flex items-center gap-2">
        <i class="fas fa-crown text-{{ $subscription->is_expired ? 'red' : 'yellow' }}-400 text-sm"></i>
        <div>
            <p class="text-xs font-semibold text-white">{{ $subscription->is_expired ? 'Subscription expired' : 'Expiring in ' . $subscription->expires_at->diffForHumans() }}</p>
            <p class="text-[10px] text-gray-500">Renew to keep receiving signals</p>
        </div>
    </div>
    <a href="{{ route('subscription.plans') }}" class="text-xs font-bold text-black px-3 py-1.5 rounded-lg" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">Renew</a>
</div>
@endif
@endif

{{-- ── Disclaimer ── --}}
@if($disclaimer)
<div class="rounded-xl p-3 mb-5 border border-[#D4AF37]/20 bg-[#D4AF37]/5 flex gap-2 items-start">
    <i class="fas fa-triangle-exclamation text-[#D4AF37] text-xs mt-0.5 shrink-0"></i>
    <p class="text-[11px] text-gray-400 leading-relaxed">{{ $disclaimer }}</p>
</div>
@endif

{{-- ── New Signal Banner ── --}}
<div id="newSignalBanner"
     class="fixed top-4 left-1/2 -translate-x-1/2 z-50 opacity-0 pointer-events-none -translate-y-3 transition-all duration-300 w-[90%] max-w-sm">
    <div class="flex items-center gap-3 bg-[#0f1e0f]/95 border border-green-500/50 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-xl">
        <span class="relative flex h-2.5 w-2.5 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
        </span>
        <p class="text-sm font-bold flex-1" id="newSignalCountText">New signal available</p>
        <button onclick="window.location.reload()" class="text-xs font-bold text-black px-3 py-1 rounded-lg shrink-0"
                style="background:linear-gradient(135deg,#D4AF37,#FFD700);">
            <i class="fas fa-rotate-right mr-1"></i> Refresh
        </button>
        <button onclick="dismissBanner()" class="text-gray-400 hover:text-white ml-1 shrink-0">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</div>

{{-- ── Active Signals Grid ── --}}
<div class="flex items-center justify-between mb-3">
    <h2 class="text-sm font-bold text-white flex items-center gap-2">
        <i class="fas fa-bolt text-[#D4AF37] text-xs"></i>
        Live Buy &amp; Sell Signals
    </h2>
    <a href="{{ route('signals.index') }}" class="text-[11px] text-[#D4AF37] hover:underline">View all <i class="fas fa-arrow-right text-[9px]"></i></a>
</div>

@if($activeSignals->isEmpty())
<div class="glass rounded-2xl p-10 text-center mb-6">
    <i class="fas fa-satellite-dish text-4xl text-gray-700 mb-4 block animate-pulse"></i>
    <p class="text-white font-semibold">AI is scanning the markets...</p>
    <p class="text-gray-500 text-sm mt-1">Fresh signals will appear here every 5 minutes.</p>
    <p class="text-gray-600 text-xs mt-3">Monitoring: {{ collect($watchedPairs)->pluck('display')->implode(', ') }}</p>
</div>
@else

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 mb-6">
@foreach($activeSignals as $signal)
@php
    $isBuy      = strtoupper($signal->type) === 'BUY';
    $entry      = (float) $signal->entry_price;
    $sl         = (float) $signal->stop_loss;
    $tp         = (float) $signal->take_profit;
    $potential  = $entry > 0 ? round(abs($tp - $entry) / $entry * 100, 2) : 0;
    $riskPct    = $entry > 0 ? round(abs($sl - $entry) / $entry * 100, 2) : 0;
    $rr         = $riskPct > 0 ? round($potential / $riskPct, 1) : 0;
    $confColor  = $signal->confidence >= 75 ? 'bg-green-500' : ($signal->confidence >= 60 ? 'bg-yellow-500' : 'bg-orange-500');
    $confText   = $signal->confidence >= 75 ? 'text-green-400' : ($signal->confidence >= 60 ? 'text-yellow-400' : 'text-orange-400');
    $decimals   = str_contains($signal->pair, 'JPY') ? 3 : (str_contains($signal->pair, 'USDT') || str_contains($signal->pair, 'XAU') ? 2 : 5);

    // Duration / expiry
    $durMins   = \App\Services\AITradeService::parseDurationMinutes($signal->duration ?? '');
    $expiresTs = $signal->created_at->addMinutes($durMins)->timestamp;
    $timeframe = match(true) {
        $durMins <= 30   => ['label' => 'SCALP',   'cls' => 'bg-purple-500/15 text-purple-400 border-purple-500/30'],
        $durMins <= 240  => ['label' => 'SHORT',   'cls' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
        $durMins <= 720  => ['label' => 'INTRADAY','cls' => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30'],
        $durMins <= 1440 => ['label' => 'DAY',     'cls' => 'bg-orange-500/15 text-orange-400 border-orange-500/30'],
        default          => ['label' => 'SWING',   'cls' => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30'],
    };

    // MT4 format
    $mt4Sym  = str_replace('/', '', $signal->pair);
    $mt4Text = "=== VoxTrade AI Signal ===\nSymbol: {$mt4Sym}\nAction: " . strtoupper($signal->type) . "\nEntry Price: " . number_format($entry, $decimals) . "\nStop Loss: " . number_format($sl, $decimals) . "\nTake Profit: " . number_format($tp, $decimals) . "\nHold Duration: {$signal->duration}\nAI Confidence: {$signal->confidence}%\nRisk Level: " . ucfirst($signal->risk_level ?? 'medium') . "\n=========================\nExecute manually on MetaTrader 4/5";
@endphp

<div data-signal-id="{{ $signal->id }}" class="glass rounded-2xl p-4 border {{ $isBuy ? 'border-green-500/20' : 'border-red-500/20' }} relative overflow-hidden">
    <div class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-5 blur-2xl pointer-events-none"
         style="background:{{ $isBuy ? '#22c55e' : '#ef4444' }};"></div>

    {{-- Header: pair + live dot + timeframe badge + countdown --}}
    <div class="flex items-start justify-between mb-2 relative z-10">
        <div>
            <div class="flex items-center gap-1.5 mb-1">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $isBuy ? 'bg-green-400' : 'bg-red-400' }}"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isBuy ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </span>
                <span class="font-black text-[#D4AF37] text-sm">{{ $signal->pair }}</span>
                <span class="badge border {{ $timeframe['cls'] }} text-[9px]">{{ $timeframe['label'] }}</span>
            </div>
            <div class="flex items-center gap-1">
                <i class="fas fa-clock text-[9px] text-gray-600"></i>
                <span class="text-[10px] font-semibold text-gray-400 countdown" data-expires="{{ $expiresTs }}">--</span>
            </div>
        </div>
        <span class="text-[10px] text-gray-600 shrink-0 ml-2">{{ $signal->created_at->diffForHumans() }}</span>
    </div>

    {{-- Direction ── the most important element ── --}}
    <div class="flex items-center gap-2 mb-3 relative z-10">
        <div class="{{ $isBuy ? 'bg-green-500/15 border-green-500/40' : 'bg-red-500/15 border-red-500/40' }} border rounded-xl px-3 py-1.5 flex items-center gap-2 flex-1">
            <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} text-lg {{ $isBuy ? 'text-green-400' : 'text-red-400' }}"></i>
            <div>
                <p class="text-[9px] text-gray-500 leading-none mb-0.5">AI recommendation</p>
                <p class="text-xl font-black {{ $isBuy ? 'text-green-400' : 'text-red-400' }} leading-none">{{ strtoupper($signal->type) }}</p>
            </div>
        </div>
        @if($rr > 0)
        <div class="text-center shrink-0">
            <p class="text-[9px] text-gray-500">R:R</p>
            <p class="text-sm font-black gold-text">1:{{ $rr }}</p>
        </div>
        @endif
    </div>

    {{-- Price Grid --}}
    <div class="grid grid-cols-3 gap-1 mb-3 relative z-10">
        <div class="bg-[#111]/70 rounded-lg p-2 text-center">
            <p class="text-[9px] text-gray-600 mb-0.5">Entry</p>
            <p class="text-[11px] font-bold text-white">{{ number_format($entry, $decimals) }}</p>
        </div>
        <div class="bg-red-900/10 rounded-lg p-2 text-center">
            <p class="text-[9px] text-gray-600 mb-0.5">Stop Loss</p>
            <p class="text-[11px] font-bold text-red-400">{{ number_format($sl, $decimals) }}</p>
            <p class="text-[9px] text-red-600">-{{ $riskPct }}%</p>
        </div>
        <div class="bg-green-900/10 rounded-lg p-2 text-center">
            <p class="text-[9px] text-gray-600 mb-0.5">Take Profit</p>
            <p class="text-[11px] font-bold text-green-400">{{ number_format($tp, $decimals) }}</p>
            <p class="text-[9px] text-green-600">+{{ $potential }}%</p>
        </div>
    </div>

    {{-- Confidence --}}
    <div class="mb-3 relative z-10">
        <div class="flex justify-between text-[10px] mb-1">
            <span class="text-gray-600">AI Confidence</span>
            <span class="{{ $confText }} font-bold">{{ $signal->confidence }}%</span>
        </div>
        <div class="h-1.5 rounded-full bg-[#1a1a1a] overflow-hidden">
            <div class="h-full rounded-full {{ $confColor }}" style="width:{{ $signal->confidence }}%"></div>
        </div>
    </div>

    {{-- Duration + Analysis snippet --}}
    @if($signal->duration || $signal->analysis_summary)
    <div class="mb-3 relative z-10">
        @if($signal->duration)
        <span class="inline-flex items-center gap-1 text-[10px] text-gray-400 bg-[#1a1a1a] border border-[#2a2a2a] rounded-full px-2 py-0.5 mb-1.5">
            <i class="fas fa-clock text-[#D4AF37] text-[8px]"></i> Hold: {{ $signal->duration }}
        </span>
        @endif
        @if($signal->analysis_summary)
        <p class="text-[10px] text-gray-500 leading-relaxed line-clamp-2">{{ $signal->analysis_summary }}</p>
        @endif
    </div>
    @endif

    {{-- Action buttons --}}
    <div class="flex gap-2 relative z-10">
        <a href="{{ route('signals.show', $signal) }}"
           class="flex-1 text-center py-2 rounded-xl text-[11px] font-semibold text-[#D4AF37] border border-[#D4AF37]/30 hover:bg-[#D4AF37]/10 transition">
            Full Analysis
        </a>
        <button onclick="copyMT4({{ $signal->id }}, this)"
                data-signal="{{ htmlspecialchars($mt4Text) }}"
                class="flex-1 text-center py-2 rounded-xl text-[11px] font-bold text-black transition hover:opacity-90"
                style="background:linear-gradient(135deg,#D4AF37,#FFD700);">
            <i class="fas fa-copy mr-1"></i> Copy MT4
        </button>
    </div>
</div>

@endforeach
</div>
@endif

{{-- ── Monitored Pairs ── --}}
<div class="glass rounded-2xl p-4 mb-6">
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
        <i class="fas fa-radar text-[#D4AF37]"></i> Pairs Under AI Watch
    </h3>
    <div class="flex flex-wrap gap-2">
        @foreach($watchedPairs as $pair)
        @php
            $hasActive = $activeSignals->where('pair', $pair['display'])->isNotEmpty();
        @endphp
        <span class="flex items-center gap-1.5 text-[11px] font-semibold px-3 py-1 rounded-full border
            {{ $hasActive ? 'border-green-500/40 bg-green-500/10 text-green-400' : 'border-[#2a2a2a] bg-[#111]/60 text-gray-500' }}">
            @if($hasActive)
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
            @else
            <span class="w-1.5 h-1.5 rounded-full bg-gray-600 inline-block"></span>
            @endif
            {{ $pair['display'] }}
        </span>
        @endforeach
    </div>
    <p class="text-[10px] text-gray-600 mt-3">
        <i class="fas fa-info-circle mr-1"></i>
        Green = active signal exists. AI scans every 5 minutes and only posts signals with ≥70% confidence.
    </p>
</div>

{{-- ── How to Execute (Quick Guide) ── --}}
<div class="glass rounded-2xl p-4 mb-2">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-book-open text-[#D4AF37]"></i> How to Execute Signals
        </h3>
        <a href="{{ route('how-it-works') }}" class="text-[11px] text-[#D4AF37] hover:underline">Full guide <i class="fas fa-arrow-right text-[9px]"></i></a>
    </div>
    <div class="space-y-3">
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-black text-xs font-black" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">1</div>
            <div>
                <p class="text-xs font-semibold text-white">See a signal above? Tap "Copy MT4"</p>
                <p class="text-[11px] text-gray-500 mt-0.5">The AI has already done the analysis — copy the signal details to your clipboard.</p>
            </div>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-black text-xs font-black" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">2</div>
            <div>
                <p class="text-xs font-semibold text-white">Open MetaTrader 4 or 5</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Find the pair (e.g. EURUSD), tap New Order, and enter the exact Entry, Stop Loss, and Take Profit values.</p>
            </div>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-black text-xs font-black" style="background:linear-gradient(135deg,#D4AF37,#FFD700);">3</div>
            <div>
                <p class="text-xs font-semibold text-white">Let the trade run to TP or SL</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Do not move your SL/TP. The AI has calculated the optimal levels. Monitor until the signal status updates.</p>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="copyToast"
     class="fixed bottom-20 left-1/2 -translate-x-1/2 bg-green-500 text-white text-xs font-semibold px-5 py-2.5 rounded-full shadow-lg opacity-0 transition-opacity duration-300 pointer-events-none z-50">
    <i class="fas fa-check mr-1"></i> MT4 signal copied!
</div>

@endsection

@push('scripts')
<script>
// ── MT4 Copy ──
function copyMT4(id, btn) {
    const text = btn.getAttribute('data-signal');
    const doFallback = () => {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    };
    const onCopied = () => {
        const toast = document.getElementById('copyToast');
        toast.style.opacity = '1';
        setTimeout(() => { toast.style.opacity = '0'; }, 2500);
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy mr-1"></i> Copy MT4'; }, 3000);
    };
    navigator.clipboard ? navigator.clipboard.writeText(text).then(onCopied).catch(doFallback) : doFallback();
    onCopied();
}

// ── Signal card IDs known on this page ──
const knownIds = new Set([{{ $activeSignals->pluck('id')->implode(',') }}]);

function animateCardOut(card) {
    card.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
    card.style.opacity    = '0';
    card.style.transform  = 'scale(0.9) translateY(-6px)';
    setTimeout(() => {
        card.style.overflow   = 'hidden';
        card.style.maxHeight  = card.offsetHeight + 'px';
        requestAnimationFrame(() => {
            card.style.transition += ', max-height 0.45s ease, margin 0.45s ease, padding 0.45s ease';
            card.style.maxHeight  = '0';
            card.style.margin     = '0';
            card.style.padding    = '0';
            setTimeout(() => card.remove(), 460);
        });
    }, 560);
}

function showNewSignalBanner(count) {
    const b = document.getElementById('newSignalBanner');
    if (!b) return;
    document.getElementById('newSignalCountText').textContent =
        `${count} new signal${count > 1 ? 's' : ''} available`;
    b.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-3');
    b.classList.add('opacity-100', 'translate-y-0');
}

function dismissBanner() {
    const b = document.getElementById('newSignalBanner');
    b.classList.add('opacity-0', 'pointer-events-none', '-translate-y-3');
    b.classList.remove('opacity-100', 'translate-y-0');
}

// ── Smart live polling (every 30s) ──
let lastCheckedSecs = 0;
setInterval(() => {
    lastCheckedSecs++;
    const el = document.getElementById('lastChecked');
    if (el) el.textContent = lastCheckedSecs < 60 ? `${lastCheckedSecs}s ago` : `${Math.floor(lastCheckedSecs / 60)}m ago`;
}, 1000);

async function pollSignals() {
    try {
        const res = await fetch('{{ route("signals.live") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();
        const serverIds = new Set(data.active_ids || []);
        let removedCount = 0, newCount = 0;

        // Animate out signals no longer active
        [...knownIds].forEach(id => {
            if (!serverIds.has(id)) {
                const card = document.querySelector(`[data-signal-id="${id}"]`);
                if (card) { animateCardOut(card); removedCount++; }
                knownIds.delete(id);
            }
        });

        // Detect new signals
        serverIds.forEach(id => { if (!knownIds.has(id)) { newCount++; knownIds.add(id); } });

        // Update active count stat
        const el = document.getElementById('activeCount');
        if (el) el.textContent = data.total;

        if (newCount > 0) showNewSignalBanner(newCount);

        lastCheckedSecs = 0;
        const lc = document.getElementById('lastChecked');
        if (lc) lc.textContent = 'just now';
    } catch(e) { /* silent */ }
}

setInterval(pollSignals, 30000);

// ── Signal countdown timers (also auto-remove on expiry) ──
document.querySelectorAll('.countdown[data-expires]').forEach(el => {
    const expiresAt = parseInt(el.dataset.expires) * 1000;
    function tick() {
        const diff = expiresAt - Date.now();
        if (diff <= 0) {
            el.textContent = 'Window closed';
            el.style.color = '#6b7280';
            // Animate the card out immediately
            const card = el.closest('[data-signal-id]');
            if (card) {
                const sigId = parseInt(card.dataset.signalId);
                setTimeout(() => {
                    animateCardOut(card);
                    knownIds.delete(sigId);
                    const countEl = document.getElementById('activeCount');
                    if (countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent || '0') - 1);
                }, 2000);
            }
            return;
        }
        const s = Math.floor(diff / 1000);
        const d = Math.floor(s / 86400);
        const h = Math.floor((s % 86400) / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        el.textContent = d > 0 ? `${d}d ${h}h left`
                       : h > 0 ? `${h}h ${m}m left`
                       : m > 0 ? `${m}m ${sec}s left`
                       : `${sec}s left`;
        el.style.color = s < 300 ? '#ef4444' : s < 3600 ? '#eab308' : '#22c55e';
    }
    tick();
    setInterval(tick, 1000);
});
</script>
@endpush
