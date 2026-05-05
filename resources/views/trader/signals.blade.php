@extends('layouts.trader')
@section('title', 'Live Signals')

@section('content')

{{-- ── Page Heading ── --}}
<div class="mb-5">
    <div class="flex items-center gap-3 mb-1">
        <h1 class="text-2xl font-bold text-white">AI <span class="gold-text">Signals</span></h1>
        <span class="flex items-center gap-1.5 text-[11px] font-bold text-green-400 bg-green-500/10 border border-green-500/30 px-2.5 py-1 rounded-full">
            <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            LIVE
        </span>
    </div>
    <p class="text-gray-500 text-sm">AI-powered buy &amp; sell signals — act before the window closes</p>
</div>

{{-- ── Filter Bar ── --}}
<form method="GET" action="{{ route('signals.index') }}" id="filterForm">
    <div class="mb-4 -mx-4 px-4 overflow-x-auto" style="scrollbar-width:none;">
        <div class="flex gap-2 pb-2 min-w-max">

            {{-- Category --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'forex' => 'Forex', 'crypto' => 'Crypto'] as $val => $label)
                <button type="button"
                    onclick="setFilter('category','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition {{ (request('category','all')===$val) ? 'bg-[#D4AF37] text-black' : 'text-gray-400 hover:text-white' }}"
                    data-group="category" data-value="{{ $val }}">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Type --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'buy' => 'BUY', 'sell' => 'SELL'] as $val => $label)
                <button type="button"
                    onclick="setFilter('type','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition
                        {{ (request('type','all')===$val)
                            ? ($val==='buy' ? 'bg-green-500 text-white' : ($val==='sell' ? 'bg-red-500 text-white' : 'bg-[#D4AF37] text-black'))
                            : 'text-gray-400 hover:text-white' }}"
                    data-group="type" data-value="{{ $val }}">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Risk --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'low' => 'Low', 'medium' => 'Med', 'high' => 'High'] as $val => $label)
                <button type="button"
                    onclick="setFilter('risk','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition {{ (request('risk','all')===$val) ? 'bg-[#D4AF37] text-black' : 'text-gray-400 hover:text-white' }}"
                    data-group="risk" data-value="{{ $val }}">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'active' => 'Active', 'tp_hit' => 'TP Hit', 'sl_hit' => 'SL Hit'] as $val => $label)
                <button type="button"
                    onclick="setFilter('status','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition {{ (request('status','active')===$val) ? 'bg-[#D4AF37] text-black' : 'text-gray-400 hover:text-white' }}"
                    data-group="status" data-value="{{ $val }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <input type="hidden" name="category" id="f_category" value="{{ request('category','all') }}">
    <input type="hidden" name="type"     id="f_type"     value="{{ request('type','all') }}">
    <input type="hidden" name="risk"     id="f_risk"     value="{{ request('risk','all') }}">
    <input type="hidden" name="status"   id="f_status"   value="{{ request('status','active') }}">
</form>

<p class="text-xs text-gray-600 mb-4">
    Showing <span class="text-gray-400 font-semibold">{{ $signals->total() }}</span> signal{{ $signals->total() !== 1 ? 's' : '' }}
</p>

{{-- ── Signals Grid: 1 col mobile → 2 col tablet → 3 col desktop ── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

@forelse($signals as $signal)
@php
    $isActive  = $signal->status === 'active';
    $isBuy     = strtoupper($signal->type) === 'BUY';
    $entry     = (float) $signal->entry_price;
    $sl        = (float) $signal->stop_loss;
    $tp        = (float) $signal->take_profit;
    $potential = $entry > 0 ? round(abs($tp - $entry) / $entry * 100, 2) : 0;
    $risk      = $entry > 0 ? round(abs($sl - $entry) / $entry * 100, 2) : 0;

    $statusMap = [
        'active'  => ['label' => 'Active',  'cls' => 'bg-green-500/15 text-green-400 border-green-500/30'],
        'tp_hit'  => ['label' => 'TP Hit',  'cls' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
        'sl_hit'  => ['label' => 'SL Hit',  'cls' => 'bg-red-500/15 text-red-400 border-red-500/30'],
        'expired' => ['label' => 'Expired', 'cls' => 'bg-gray-500/15 text-gray-400 border-gray-500/30'],
    ];
    $st = $statusMap[$signal->status] ?? $statusMap['expired'];

    $riskMap = [
        'low'    => 'text-green-400 bg-green-500/10 border-green-500/20',
        'medium' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20',
        'high'   => 'text-red-400 bg-red-500/10 border-red-500/20',
    ];
    $riskCls  = $riskMap[$signal->risk_level ?? 'medium'] ?? $riskMap['medium'];
    $confColor = $signal->confidence >= 75 ? 'bg-green-500' : ($signal->confidence >= 50 ? 'bg-yellow-500' : 'bg-red-500');
    $confText  = $signal->confidence >= 75 ? 'text-green-400' : ($signal->confidence >= 50 ? 'text-yellow-400' : 'text-red-400');
    $cardBorder = $isActive ? ($isBuy ? 'border-green-500/25' : 'border-red-500/25') : 'border-white/5';
@endphp

<a href="{{ route('signals.show', $signal) }}" class="block group">
<div class="glass glass-hover rounded-2xl p-4 transition-all duration-200 h-full border {{ $cardBorder }} relative overflow-hidden">

    {{-- Glow accent --}}
    @if($isActive)
    <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 blur-2xl pointer-events-none"
         style="background:{{ $isBuy ? '#22c55e' : '#ef4444' }};"></div>
    @endif

    {{-- ── Header: pair name + live dot + timestamp ── --}}
    <div class="flex items-start justify-between mb-3 relative z-10">
        <div class="flex items-center gap-2">
            @if($isActive)
            <span class="relative flex h-2.5 w-2.5 shrink-0 mt-0.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $isBuy ? 'bg-green-400' : 'bg-red-400' }}"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isBuy ? 'bg-green-500' : 'bg-red-500' }}"></span>
            </span>
            @endif
            <span class="text-lg font-black text-[#D4AF37] tracking-wide">{{ $signal->pair }}</span>
        </div>
        <span class="text-[10px] text-gray-600 shrink-0 ml-2">{{ $signal->created_at->diffForHumans() }}</span>
    </div>

    {{-- ── BIG Direction Badge ── --}}
    <div class="flex items-center gap-2 mb-4 relative z-10">
        <div class="flex items-center gap-2 {{ $isBuy ? 'bg-green-500/10 border border-green-500/30' : 'bg-red-500/10 border border-red-500/30' }} rounded-xl px-3 py-2">
            <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} text-xl {{ $isBuy ? 'text-green-400' : 'text-red-400' }}"></i>
            <div>
                <p class="text-[9px] text-gray-500 uppercase tracking-widest leading-none mb-0.5">AI Recommendation</p>
                <p class="text-xl font-black {{ $isBuy ? 'text-green-400' : 'text-red-400' }} leading-none">{{ strtoupper($signal->type) }}</p>
            </div>
        </div>
        <span class="badge border {{ $st['cls'] }} text-[10px]">{{ $st['label'] }}</span>
    </div>

    {{-- ── Price grid ── --}}
    <div class="grid grid-cols-3 gap-1.5 mb-3 relative z-10">
        <div class="text-center bg-[#111]/60 rounded-lg py-2 px-1">
            <p class="text-[9px] text-gray-600 uppercase tracking-wide mb-0.5">Entry</p>
            <p class="text-[11px] font-bold text-white">{{ number_format($entry, 5) }}</p>
        </div>
        <div class="text-center bg-red-900/10 rounded-lg py-2 px-1">
            <p class="text-[9px] text-gray-600 uppercase tracking-wide mb-0.5">Stop Loss</p>
            <p class="text-[11px] font-bold text-red-400">{{ number_format($sl, 5) }}</p>
            <p class="text-[9px] text-red-500 leading-none">-{{ $risk }}%</p>
        </div>
        <div class="text-center bg-green-900/10 rounded-lg py-2 px-1">
            <p class="text-[9px] text-gray-600 uppercase tracking-wide mb-0.5">Take Profit</p>
            <p class="text-[11px] font-bold text-green-400">{{ number_format($tp, 5) }}</p>
            <p class="text-[9px] text-green-600 leading-none">+{{ $potential }}%</p>
        </div>
    </div>

    {{-- ── Confidence bar ── --}}
    <div class="mb-3 relative z-10">
        <div class="flex justify-between text-[10px] mb-1">
            <span class="text-gray-500">AI Confidence</span>
            <span class="font-bold {{ $confText }}">{{ $signal->confidence }}%</span>
        </div>
        <div class="h-1.5 rounded-full bg-[#1e1e1e] overflow-hidden">
            <div class="h-full rounded-full {{ $confColor }} transition-all" style="width:{{ $signal->confidence }}%"></div>
        </div>
    </div>

    {{-- ── Meta row ── --}}
    <div class="flex items-center gap-1.5 flex-wrap mb-3 relative z-10">
        @if($signal->category)
        <span class="badge bg-[#1a1a1a] text-gray-400 border border-[#2a2a2a] capitalize text-[10px]">
            <i class="fas fa-{{ $signal->category === 'crypto' ? 'bitcoin-sign' : 'chart-line' }} mr-1 text-[9px]"></i>{{ $signal->category }}
        </span>
        @endif
        @if($signal->duration)
        <span class="badge bg-[#1a1a1a] text-gray-400 border border-[#2a2a2a] text-[10px]">
            <i class="fas fa-clock mr-1 text-[9px] text-[#D4AF37]"></i>Hold: {{ $signal->duration }}
        </span>
        @endif
        @if($signal->risk_level)
        <span class="badge border {{ $riskCls }} capitalize text-[10px]">
            <i class="fas fa-shield-halved mr-1 text-[9px]"></i>{{ $signal->risk_level }} risk
        </span>
        @endif
    </div>

    {{-- ── Analysis snippet ── --}}
    @if(!empty($signal->analysis_summary))
    <p class="text-[11px] text-gray-500 leading-relaxed line-clamp-2 relative z-10">{{ $signal->analysis_summary }}</p>
    @endif

    {{-- ── View detail CTA ── --}}
    <div class="mt-3 flex items-center justify-end relative z-10">
        <span class="text-[11px] text-[#D4AF37] group-hover:underline">View full analysis <i class="fas fa-arrow-right ml-0.5 text-[9px]"></i></span>
    </div>
</div>
</a>

@empty
<div class="col-span-full glass rounded-xl p-10 text-center">
    <i class="fas fa-satellite-dish text-4xl text-gray-700 mb-4 block"></i>
    <p class="text-gray-400 font-semibold">No signals match your filters</p>
    <p class="text-gray-600 text-sm mt-1">Try adjusting the filters above or check back shortly</p>
    <a href="{{ route('signals.index') }}" class="mt-4 inline-block text-[#D4AF37] text-sm hover:text-[#FFD700] transition">
        <i class="fas fa-rotate-left mr-1"></i> Reset Filters
    </a>
</div>
@endforelse

</div>{{-- end grid --}}

{{-- ── Pagination ── --}}
@if($signals->hasPages())
<div class="flex items-center justify-center gap-2 mt-8 pb-2">
    @if($signals->onFirstPage())
        <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#141414] border border-[#1e1e1e] text-gray-700 cursor-not-allowed">
            <i class="fas fa-chevron-left text-xs"></i>
        </span>
    @else
        <a href="{{ $signals->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#141414] border border-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/10 transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
    @endif

    @foreach($signals->getUrlRange(max(1,$signals->currentPage()-2), min($signals->lastPage(),$signals->currentPage()+2)) as $page => $url)
        @if($page === $signals->currentPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-black font-bold text-sm"
                  style="background:linear-gradient(135deg,#D4AF37,#FFD700);">{{ $page }}</span>
        @else
            <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#141414] border border-[#1e1e1e] text-gray-400 hover:border-[#D4AF37]/30 hover:text-[#D4AF37] transition text-sm">
                {{ $page }}
            </a>
        @endif
    @endforeach

    @if($signals->hasMorePages())
        <a href="{{ $signals->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#141414] border border-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/10 transition">
            <i class="fas fa-chevron-right text-xs"></i>
        </a>
    @else
        <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#141414] border border-[#1e1e1e] text-gray-700 cursor-not-allowed">
            <i class="fas fa-chevron-right text-xs"></i>
        </span>
    @endif
</div>
<p class="text-center text-[11px] text-gray-600 mt-2">Page {{ $signals->currentPage() }} of {{ $signals->lastPage() }}</p>
@endif

@endsection

@push('scripts')
<script>
function setFilter(group, value) {
    document.getElementById('f_' + group).value = value;
    document.querySelectorAll('[data-group="' + group + '"]').forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.classList.remove('bg-[#D4AF37]','text-black','bg-green-500','bg-red-500','text-white','text-gray-400','hover:text-white');
        if (isActive) {
            if (group === 'type' && value === 'buy')       btn.classList.add('bg-green-500','text-white');
            else if (group === 'type' && value === 'sell') btn.classList.add('bg-red-500','text-white');
            else                                            btn.classList.add('bg-[#D4AF37]','text-black');
        } else {
            btn.classList.add('text-gray-400','hover:text-white');
        }
    });
    document.getElementById('filterForm').submit();
}
</script>
@endpush
