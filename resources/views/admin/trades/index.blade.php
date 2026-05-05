@extends('layouts.admin')
@section('title', 'Trades')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Trade Signals</h1>
            <p class="text-gray-500 text-sm mt-0.5">Manage AI-generated and manual trade signals</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('admin.trades.generate') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                    <i class="fas fa-robot"></i> Generate AI Signals
                </button>
            </form>
            <a href="{{ route('admin.trades.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold glass border border-[#D4AF37]/30 text-[#D4AF37] hover:bg-white/5 transition">
                <i class="fas fa-plus"></i> Manual Signal
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="category" class="bg-[#1a1a1a] border border-[#D4AF37]/20 text-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            <option value="">All Markets</option>
            <option value="forex" {{ request('category') === 'forex' ? 'selected' : '' }}>Forex</option>
            <option value="crypto" {{ request('category') === 'crypto' ? 'selected' : '' }}>Crypto</option>
        </select>
        <select name="type" class="bg-[#1a1a1a] border border-[#D4AF37]/20 text-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            <option value="">BUY & SELL</option>
            <option value="BUY" {{ request('type') === 'BUY' ? 'selected' : '' }}>BUY</option>
            <option value="SELL" {{ request('type') === 'SELL' ? 'selected' : '' }}>SELL</option>
        </select>
        <select name="status" class="bg-[#1a1a1a] border border-[#D4AF37]/20 text-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="tp_hit" {{ request('status') === 'tp_hit' ? 'selected' : '' }}>TP Hit</option>
            <option value="sl_hit" {{ request('status') === 'sl_hit' ? 'selected' : '' }}>SL Hit</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-black" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">Filter</button>
        @if(request()->anyFilled(['category','type','status']))
            <a href="{{ route('admin.trades.index') }}" class="text-sm text-gray-400 hover:text-white transition">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#D4AF37]/10">
                        <th class="text-left px-5 py-4 text-gray-400 font-medium">Pair</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium">Type</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden md:table-cell">Entry / SL / TP</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden lg:table-cell">Confidence</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden lg:table-cell">Category</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium">Status</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden lg:table-cell">Created</th>
                        <th class="text-right px-5 py-4 text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a1a1a]">
                    @forelse($trades as $trade)
                    <tr class="hover:bg-white/2 transition">
                        <td class="px-5 py-4">
                            <span class="text-white font-bold">{{ $trade->pair }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $trade->type === 'BUY' ? 'bg-green-900/40 text-green-400 border border-green-700/30' : 'bg-red-900/40 text-red-400 border border-red-700/30' }}">{{ $trade->type }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-xs">
                            <div class="space-y-0.5">
                                <p class="text-gray-300">E: <span class="text-white font-medium">{{ number_format($trade->entry_price, 4) }}</span></p>
                                <p class="text-red-400">SL: {{ number_format($trade->stop_loss, 4) }}</p>
                                <p class="text-green-400">TP: {{ number_format($trade->take_profit, 4) }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-[#1e1e1e] rounded-full w-16">
                                    <div class="h-1.5 rounded-full" style="background:linear-gradient(90deg,#D4AF37,#FFD700);width:{{ $trade->confidence }}%"></div>
                                </div>
                                <span class="text-xs text-[#D4AF37] font-semibold w-8">{{ $trade->confidence }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $trade->category === 'crypto' ? 'bg-orange-900/30 text-orange-400' : 'bg-blue-900/30 text-blue-400' }}">{{ ucfirst($trade->category) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @php $statusCfg = ['active'=>['bg-yellow-900/30 text-yellow-400','Active'],'tp_hit'=>['bg-green-900/30 text-green-400','TP Hit'],'sl_hit'=>['bg-red-900/30 text-red-400','SL Hit'],'expired'=>['bg-gray-900/30 text-gray-400','Expired']]; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ ($statusCfg[$trade->status] ?? ['bg-gray-900/30 text-gray-400','Unknown'])[0] }}">{{ ($statusCfg[$trade->status] ?? ['','Unknown'])[1] }}</span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-gray-400 text-xs">{{ $trade->created_at->format('M d, H:i') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.trades.edit', $trade) }}" class="w-8 h-8 rounded-lg bg-blue-900/30 text-blue-400 hover:bg-blue-900/50 flex items-center justify-center transition">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.trades.destroy', $trade) }}" onsubmit="return confirm('Delete this signal?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 flex items-center justify-center transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-candlestick text-4xl mb-3 block opacity-20"></i>
                            No trade signals yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($trades->hasPages())
        <div class="px-5 py-4 border-t border-[#D4AF37]/10">{{ $trades->links() }}</div>
        @endif
    </div>
</div>
@endsection
