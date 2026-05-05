@extends('layouts.admin')
@section('title', 'Edit Signal')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.trades.index') }}" class="w-9 h-9 glass rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Signal — <span class="gold-text">{{ $trade->pair }}</span></h1>
            <p class="text-gray-500 text-sm">Update trade signal details</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.trades.update', $trade) }}" class="glass rounded-2xl p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm text-gray-400 mb-2">Pair</label>
                <input type="text" name="pair" value="{{ old('pair', $trade->pair) }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Type</label>
                <select name="type" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="BUY" {{ old('type', $trade->type) === 'BUY' ? 'selected' : '' }}>BUY</option>
                    <option value="SELL" {{ old('type', $trade->type) === 'SELL' ? 'selected' : '' }}>SELL</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Entry Price</label>
                <input type="number" step="0.00000001" name="entry_price" value="{{ old('entry_price', $trade->entry_price) }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Stop Loss</label>
                <input type="number" step="0.00000001" name="stop_loss" value="{{ old('stop_loss', $trade->stop_loss) }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Take Profit</label>
                <input type="number" step="0.00000001" name="take_profit" value="{{ old('take_profit', $trade->take_profit) }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Confidence % — <span id="confVal2" class="text-[#D4AF37] font-bold">{{ old('confidence', $trade->confidence) }}</span></label>
                <input type="range" name="confidence" min="0" max="100" value="{{ old('confidence', $trade->confidence) }}"
                    class="w-full accent-yellow-500" oninput="document.getElementById('confVal2').textContent=this.value">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Duration</label>
                <input type="text" name="duration" value="{{ old('duration', $trade->duration) }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Category</label>
                <select name="category" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="forex" {{ old('category', $trade->category) === 'forex' ? 'selected' : '' }}>Forex</option>
                    <option value="crypto" {{ old('category', $trade->category) === 'crypto' ? 'selected' : '' }}>Crypto</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Risk Level</label>
                <select name="risk_level" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="low" {{ old('risk_level', $trade->risk_level) === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('risk_level', $trade->risk_level) === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('risk_level', $trade->risk_level) === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Status</label>
                <select name="status" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    <option value="active" {{ old('status', $trade->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="tp_hit" {{ old('status', $trade->status) === 'tp_hit' ? 'selected' : '' }}>TP Hit</option>
                    <option value="sl_hit" {{ old('status', $trade->status) === 'sl_hit' ? 'selected' : '' }}>SL Hit</option>
                    <option value="expired" {{ old('status', $trade->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-2">Analysis Summary</label>
            <textarea name="analysis_summary" rows="3"
                class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 resize-none">{{ old('analysis_summary', $trade->analysis_summary) }}</textarea>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-save mr-2"></i> Update Signal
            </button>
            <a href="{{ route('admin.trades.index') }}" class="px-6 py-3 rounded-xl font-semibold text-sm text-gray-400 glass hover:text-white transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
