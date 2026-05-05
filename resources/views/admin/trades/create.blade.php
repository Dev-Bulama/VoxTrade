@extends('layouts.admin')
@section('title', 'Create Signal')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.trades.index') }}" class="w-9 h-9 glass rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Create Manual Signal</h1>
            <p class="text-gray-500 text-sm">Add a trade signal manually</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.trades.store') }}" class="glass rounded-2xl p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm text-gray-400 mb-2">Pair <span class="text-red-400">*</span></label>
                <input type="text" name="pair" value="{{ old('pair') }}" placeholder="e.g. EUR/USD, BTC/USDT"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 @error('pair') border-red-500 @enderror">
                @error('pair')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Signal Type <span class="text-red-400">*</span></label>
                <select name="type" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="BUY" {{ old('type') === 'BUY' ? 'selected' : '' }}>BUY</option>
                    <option value="SELL" {{ old('type') === 'SELL' ? 'selected' : '' }}>SELL</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Entry Price <span class="text-red-400">*</span></label>
                <input type="number" step="0.00000001" name="entry_price" value="{{ old('entry_price') }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                @error('entry_price')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Stop Loss <span class="text-red-400">*</span></label>
                <input type="number" step="0.00000001" name="stop_loss" value="{{ old('stop_loss') }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                @error('stop_loss')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Take Profit <span class="text-red-400">*</span></label>
                <input type="number" step="0.00000001" name="take_profit" value="{{ old('take_profit') }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                @error('take_profit')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Confidence % <span class="text-red-400">*</span></label>
                <div class="flex items-center gap-3">
                    <input type="range" name="confidence" min="0" max="100" value="{{ old('confidence', 75) }}" id="confRange"
                        class="flex-1 accent-yellow-500" oninput="document.getElementById('confVal').textContent=this.value">
                    <span id="confVal" class="text-[#D4AF37] font-bold w-10 text-right">{{ old('confidence', 75) }}</span>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Duration <span class="text-red-400">*</span></label>
                <input type="text" name="duration" value="{{ old('duration', 'Intraday') }}" placeholder="e.g. Intraday, Swing, 4H"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Category <span class="text-red-400">*</span></label>
                <select name="category" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="forex" {{ old('category') === 'forex' ? 'selected' : '' }}>Forex</option>
                    <option value="crypto" {{ old('category') === 'crypto' ? 'selected' : '' }}>Crypto</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Risk Level <span class="text-red-400">*</span></label>
                <select name="risk_level" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="low" {{ old('risk_level') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('risk_level', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('risk_level') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-2">Analysis Summary <span class="text-gray-600">(optional)</span></label>
            <textarea name="analysis_summary" rows="3" placeholder="Brief AI analysis or notes..."
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 resize-none">{{ old('analysis_summary') }}</textarea>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-plus mr-2"></i> Create Signal
            </button>
            <a href="{{ route('admin.trades.index') }}" class="px-6 py-3 rounded-xl font-semibold text-sm text-gray-400 glass hover:text-white transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
