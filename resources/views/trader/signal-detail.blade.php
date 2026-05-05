@extends('layouts.trader')
@section('title', $signal->pair . ' Signal')

@section('content')

@php
    $isBuy     = strtolower($signal->type) === 'buy';
    $entry     = (float) $signal->entry_price;
    $sl        = (float) $signal->stop_loss;
    $tp        = (float) $signal->take_profit;
    $potential = $entry > 0 ? round(abs($tp - $entry) / $entry * 100, 2) : 0;
    $risk      = $entry > 0 ? round(abs($sl - $entry) / $entry * 100, 2) : 0;
    $rr        = $risk > 0  ? round($potential / $risk, 2) : 0;

    $statusMap = [
        'active'  => ['label' => 'Active',  'icon' => 'circle-dot',       'cls' => 'bg-green-500/15 text-green-400 border-green-500/30'],
        'tp_hit'  => ['label' => 'TP Hit',  'icon' => 'circle-check',     'cls' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
        'sl_hit'  => ['label' => 'SL Hit',  'icon' => 'circle-xmark',     'cls' => 'bg-red-500/15 text-red-400 border-red-500/30'],
        'expired' => ['label' => 'Expired', 'icon' => 'clock-rotate-left','cls' => 'bg-gray-500/15 text-gray-400 border-gray-500/30'],
    ];
    $st = $statusMap[$signal->status] ?? $statusMap['expired'];

    $riskLevelMap = [
        'low'    => ['label' => 'Low Risk',    'cls' => 'text-green-400 bg-green-500/10 border-green-500/25'],
        'medium' => ['label' => 'Medium Risk', 'cls' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/25'],
        'high'   => ['label' => 'High Risk',   'cls' => 'text-red-400 bg-red-500/10 border-red-500/25'],
    ];
    $rl = $riskLevelMap[$signal->risk_level ?? 'medium'] ?? $riskLevelMap['medium'];

    $confColor = $signal->confidence >= 75 ? '#22c55e' : ($signal->confidence >= 50 ? '#eab308' : '#ef4444');
    $confText  = $signal->confidence >= 75 ? 'text-green-400' : ($signal->confidence >= 50 ? 'text-yellow-400' : 'text-red-400');
    $confBg    = $signal->confidence >= 75 ? 'bg-green-500' : ($signal->confidence >= 50 ? 'bg-yellow-500' : 'bg-red-500');
@endphp

{{-- ── Back button ── --}}
<div class="mb-5">
    <a href="{{ route('signals.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#D4AF37] transition">
        <i class="fas fa-arrow-left text-xs"></i> Back to Signals
    </a>
</div>

{{-- ── Hero card ── --}}
<div class="glass rounded-2xl p-5 mb-4 relative overflow-hidden">
    {{-- Decorative glow --}}
    <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10 blur-3xl pointer-events-none"
         style="background:{{ $isBuy ? '#22c55e' : '#ef4444' }};"></div>

    <div class="relative z-10">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h1 class="text-3xl font-black text-white tracking-wide">{{ $signal->pair }}</h1>
                <div class="flex items-center gap-2 mt-2 flex-wrap">
                    <span class="badge text-sm px-3 py-1 {{ $isBuy ? 'bg-green-500/25 text-green-400 border border-green-500/40' : 'bg-red-500/25 text-red-400 border border-red-500/40' }}">
                        <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} mr-1.5"></i>
                        {{ strtoupper($signal->type) }}
                    </span>
                    <span class="badge text-sm px-3 py-1 border {{ $st['cls'] }}">
                        <i class="fas fa-{{ $st['icon'] }} mr-1.5"></i>{{ $st['label'] }}
                    </span>
                </div>
            </div>

            {{-- Share button (UI only) --}}
            <button onclick="shareSignal()"
                    class="w-10 h-10 rounded-full bg-white/5 border border-[#D4AF37]/20 flex items-center justify-center text-gray-400 hover:text-[#D4AF37] hover:border-[#D4AF37]/40 transition shrink-0">
                <i class="fas fa-share-nodes text-sm"></i>
            </button>
        </div>

        <p class="text-xs text-gray-500">
            <i class="fas fa-clock mr-1"></i>
            Posted {{ $signal->created_at->diffForHumans() }} &mdash; {{ $signal->created_at->format('M j, Y \a\t g:i A') }}
        </p>
    </div>
</div>

{{-- ── Price Details ── --}}
<div class="glass rounded-2xl p-5 mb-4">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Price Levels</h2>
    <div class="space-y-3">

        {{-- Entry --}}
        <div class="flex items-center justify-between py-3 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#1e1e1e] flex items-center justify-center">
                    <i class="fas fa-crosshairs text-[#D4AF37] text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Entry Price</p>
                    <p class="text-sm font-semibold text-white">Open position here</p>
                </div>
            </div>
            <span class="text-lg font-black text-white">{{ number_format($entry, 5) }}</span>
        </div>

        {{-- Stop Loss --}}
        <div class="flex items-center justify-between py-3 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-900/20 flex items-center justify-center">
                    <i class="fas fa-shield-xmark text-red-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Stop Loss</p>
                    <p class="text-sm font-semibold text-red-400">Risk: {{ $risk }}%</p>
                </div>
            </div>
            <span class="text-lg font-black text-red-400">{{ number_format($sl, 5) }}</span>
        </div>

        {{-- Take Profit --}}
        <div class="flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-900/20 flex items-center justify-center">
                    <i class="fas fa-bullseye text-green-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Take Profit</p>
                    <p class="text-sm font-semibold text-green-400">Potential: +{{ $potential }}%</p>
                </div>
            </div>
            <span class="text-lg font-black text-green-400">{{ number_format($tp, 5) }}</span>
        </div>
    </div>

    {{-- R:R ratio --}}
    @if($rr > 0)
    <div class="mt-4 p-3 rounded-xl bg-[#D4AF37]/5 border border-[#D4AF37]/20 flex items-center justify-between">
        <span class="text-xs text-gray-400 font-medium">Risk / Reward Ratio</span>
        <span class="text-base font-black gold-text">1 : {{ $rr }}</span>
    </div>
    @endif
</div>

{{-- ── Confidence Meter ── --}}
<div class="glass rounded-2xl p-5 mb-4">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">AI Confidence</h2>

    <div class="flex items-center justify-between mb-3">
        <span class="text-4xl font-black {{ $confText }}">{{ $signal->confidence }}<span class="text-2xl">%</span></span>
        <span class="badge text-sm px-3 py-1 border {{ $confText }} bg-white/5"
              style="border-color:{{ $confColor }}40">
            @if($signal->confidence >= 75) High Confidence
            @elseif($signal->confidence >= 50) Medium Confidence
            @else Low Confidence
            @endif
        </span>
    </div>

    {{-- Large progress bar --}}
    <div class="h-3 rounded-full bg-[#1e1e1e] overflow-hidden">
        <div class="h-full rounded-full transition-all duration-700 {{ $confBg }}" style="width:{{ $signal->confidence }}%"></div>
    </div>
    <div class="flex justify-between text-[10px] text-gray-700 mt-1">
        <span>0%</span>
        <span>50%</span>
        <span>100%</span>
    </div>
</div>

{{-- ── Signal Details ── --}}
<div class="glass rounded-2xl p-5 mb-4">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Signal Details</h2>
    <div class="grid grid-cols-2 gap-3">

        @if($signal->category)
        <div class="bg-[#111]/60 rounded-xl p-3">
            <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Market</p>
            <div class="flex items-center gap-2">
                <i class="fas fa-{{ $signal->category === 'crypto' ? 'bitcoin-sign' : 'chart-line' }} text-[#D4AF37] text-sm"></i>
                <span class="text-sm font-semibold text-white capitalize">{{ $signal->category }}</span>
            </div>
        </div>
        @endif

        @if($signal->duration)
        <div class="bg-[#111]/60 rounded-xl p-3">
            <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Duration</p>
            <div class="flex items-center gap-2">
                <i class="fas fa-hourglass-half text-[#D4AF37] text-sm"></i>
                <span class="text-sm font-semibold text-white">{{ $signal->duration }}</span>
            </div>
        </div>
        @endif

        @if($signal->risk_level)
        <div class="bg-[#111]/60 rounded-xl p-3">
            <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Risk Level</p>
            <span class="badge border {{ $rl['cls'] }}">{{ $rl['label'] }}</span>
        </div>
        @endif

        <div class="bg-[#111]/60 rounded-xl p-3">
            <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Direction</p>
            <span class="badge {{ $isBuy ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} mr-1"></i>
                {{ strtoupper($signal->type) }}
            </span>
        </div>
    </div>
</div>

{{-- ── Analysis Summary ── --}}
@if(!empty($signal->analysis))
<div class="glass rounded-2xl p-5 mb-4">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">AI Analysis</h2>
    <p class="text-sm text-gray-300 leading-relaxed">{{ $signal->analysis }}</p>
</div>
@endif

{{-- ── Disclaimer ── --}}
@if(!empty($disclaimer))
<div class="rounded-xl p-4 mb-6 border border-[#D4AF37]/20 bg-[#D4AF37]/5 flex gap-3 items-start">
    <i class="fas fa-triangle-exclamation text-[#D4AF37] mt-0.5 shrink-0"></i>
    <p class="text-xs text-gray-400 leading-relaxed">{{ $disclaimer }}</p>
</div>
@endif

{{-- ── Action buttons ── --}}
<div class="flex gap-3 pb-2">
    <a href="{{ route('signals.index') }}"
       class="flex-1 py-3 rounded-xl border border-[#D4AF37]/30 text-[#D4AF37] text-sm font-semibold text-center hover:bg-[#D4AF37]/10 transition">
        <i class="fas fa-arrow-left mr-1.5"></i> All Signals
    </a>
    <button onclick="shareSignal()"
            class="flex-1 py-3 rounded-xl text-black text-sm font-bold text-center transition hover:opacity-90"
            style="background:linear-gradient(135deg,#D4AF37,#FFD700);">
        <i class="fas fa-share-nodes mr-1.5"></i> Share Signal
    </button>
</div>

@endsection

@push('scripts')
<script>
function shareSignal() {
    const text = '{{ $signal->pair }} {{ strtoupper($signal->type) }} signal — Entry: {{ number_format($entry, 5) }} | SL: {{ number_format($sl, 5) }} | TP: {{ number_format($tp, 5) }} | {{ $signal->confidence }}% confidence | VoxTrade';
    if (navigator.share) {
        navigator.share({ title: 'VoxTrade Signal', text: text, url: window.location.href });
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Signal link copied to clipboard!');
        });
    }
}
</script>
@endpush
