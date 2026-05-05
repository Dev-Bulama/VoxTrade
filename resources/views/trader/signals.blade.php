@extends('layouts.trader')
@section('title', 'Signals')

@section('content')

{{-- ── Page Heading ── --}}
<div class="mb-5">
    <h1 class="text-2xl font-bold text-white">AI <span class="gold-text">Signals</span></h1>
    <p class="text-gray-500 text-sm mt-0.5">Live & historical trade signals</p>
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
                    data-group="category" data-value="{{ $val }}">
                    {{ $label }}
                </button>
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
                    data-group="type" data-value="{{ $val }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Risk --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'low' => 'Low', 'medium' => 'Med', 'high' => 'High'] as $val => $label)
                <button type="button"
                    onclick="setFilter('risk','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition {{ (request('risk','all')===$val) ? 'bg-[#D4AF37] text-black' : 'text-gray-400 hover:text-white' }}"
                    data-group="risk" data-value="{{ $val }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-1 bg-[#141414] border border-[#1e1e1e] rounded-full px-1 py-1">
                @foreach(['all' => 'All', 'active' => 'Active', 'tp_hit' => 'TP Hit', 'sl_hit' => 'SL Hit'] as $val => $label)
                <button type="button"
                    onclick="setFilter('status','{{ $val }}')"
                    class="filter-chip px-3 py-1 rounded-full text-xs font-semibold transition {{ (request('status','all')===$val) ? 'bg-[#D4AF37] text-black' : 'text-gray-400 hover:text-white' }}"
                    data-group="status" data-value="{{ $val }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Hidden inputs for active filters --}}
    <input type="hidden" name="category" id="f_category" value="{{ request('category','all') }}">
    <input type="hidden" name="type"     id="f_type"     value="{{ request('type','all') }}">
    <input type="hidden" name="risk"     id="f_risk"     value="{{ request('risk','all') }}">
    <input type="hidden" name="status"   id="f_status"   value="{{ request('status','all') }}">
</form>

{{-- ── Result count ── --}}
<p class="text-xs text-gray-600 mb-4">
    Showing <span class="text-gray-400 font-semibold">{{ $signals->total() }}</span> signal{{ $signals->total() !== 1 ? 's' : '' }}
</p>

{{-- ── Signals Grid ── --}}
@forelse($signals as $signal)
@php
    $isBuy = strtolower($signal->type) === 'buy';
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
    $riskCls = $riskMap[$signal->risk_level ?? 'medium'] ?? $riskMap['medium'];
    $confColor = $signal->confidence >= 75 ? 'bg-green-500' : ($signal->confidence >= 50 ? 'bg-yellow-500' : 'bg-red-500');
    $confText  = $signal->confidence >= 75 ? 'text-green-400' : ($signal->confidence >= 50 ? 'text-yellow-400' : 'text-red-400');
@endphp
<a href="{{ route('signals.show', $signal) }}" class="block mb-3">
    <div class="glass glass-hover rounded-xl p-4 transition-all duration-200">

        {{-- Header row --}}
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="text-base font-bold text-[#D4AF37] tracking-wide">{{ $signal->pair }}</span>
                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                    <span class="badge {{ $isBuy ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                        <i class="fas fa-{{ $isBuy ? 'arrow-trend-up' : 'arrow-trend-down' }} mr-1 text-[9px]"></i>
                        {{ strtoupper($signal->type) }}
                    </span>
                    <span class="badge border {{ $st['cls'] }}">{{ $st['label'] }}</span>
                </div>
            </div>
            <span class="text-[10px] text-gray-600 shrink-0 ml-2">{{ $signal->created_at->diffForHumans() }}</span>
        </div>

        {{-- Price grid --}}
        <div class="grid grid-cols-3 gap-2 mb-3">
            <div class="text-center bg-[#111]/60 rounded-lg py-2">
                <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-0.5">Entry</p>
                <p class="text-xs font-bold text-white">{{ number_format((float)$signal->entry_price, 5) }}</p>
            </div>
            <div class="text-center bg-red-900/10 rounded-lg py-2">
                <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-0.5">SL</p>
                <p class="text-xs font-bold text-red-400">{{ number_format((float)$signal->stop_loss, 5) }}</p>
            </div>
            <div class="text-center bg-green-900/10 rounded-lg py-2">
                <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-0.5">TP</p>
                <p class="text-xs font-bold text-green-400">{{ number_format((float)$signal->take_profit, 5) }}</p>
            </div>
        </div>

        {{-- Confidence --}}
        <div class="mb-3">
            <div class="flex justify-between text-[10px] mb-1">
                <span class="text-gray-500">Confidence</span>
                <span class="font-bold {{ $confText }}">{{ $signal->confidence }}%</span>
            </div>
            <div class="conf-bar">
                <div class="conf-fill {{ $confColor }}" style="width:{{ $signal->confidence }}%"></div>
            </div>
        </div>

        {{-- Meta chips --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            @if($signal->category)
            <span class="badge bg-[#1a1a1a] text-gray-400 border border-[#2a2a2a] capitalize">
                <i class="fas fa-{{ $signal->category === 'crypto' ? 'bitcoin-sign' : 'chart-line' }} mr-1 text-[9px]"></i>{{ $signal->category }}
            </span>
            @endif
            @if($signal->duration)
            <span class="badge bg-[#1a1a1a] text-gray-400 border border-[#2a2a2a]">
                <i class="fas fa-clock mr-1 text-[9px]"></i>{{ $signal->duration }}
            </span>
            @endif
            @if($signal->risk_level)
            <span class="badge border {{ $riskCls }} capitalize">
                <i class="fas fa-shield-halved mr-1 text-[9px]"></i>{{ $signal->risk_level }}
            </span>
            @endif
        </div>
    </div>
</a>
@empty
<div class="glass rounded-xl p-10 text-center">
    <i class="fas fa-satellite-dish text-4xl text-gray-700 mb-4 block"></i>
    <p class="text-gray-400 font-semibold">No signals match your filters</p>
    <p class="text-gray-600 text-sm mt-1">Try adjusting the filters above</p>
    <a href="{{ route('signals.index') }}" class="mt-4 inline-block text-[#D4AF37] text-sm hover:text-[#FFD700] transition">
        <i class="fas fa-rotate-left mr-1"></i> Clear Filters
    </a>
</div>
@endforelse

{{-- ── Pagination ── --}}
@if($signals->hasPages())
<div class="flex items-center justify-center gap-2 mt-6 pb-2">
    {{-- Previous --}}
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

    {{-- Page numbers --}}
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

    {{-- Next --}}
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
<p class="text-center text-[11px] text-gray-600 mt-2">
    Page {{ $signals->currentPage() }} of {{ $signals->lastPage() }}
</p>
@endif

@endsection

@push('scripts')
<script>
function setFilter(group, value) {
    document.getElementById('f_' + group).value = value;
    // Update chip styles
    document.querySelectorAll('[data-group="' + group + '"]').forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.classList.remove('bg-[#D4AF37]','text-black','bg-green-500','bg-red-500','text-white','text-gray-400');
        if (isActive) {
            if (group === 'type' && value === 'buy')  { btn.classList.add('bg-green-500','text-white'); }
            else if (group === 'type' && value === 'sell') { btn.classList.add('bg-red-500','text-white'); }
            else { btn.classList.add('bg-[#D4AF37]','text-black'); }
        } else {
            btn.classList.add('text-gray-400');
        }
    });
    document.getElementById('filterForm').submit();
}
</script>
@endpush
