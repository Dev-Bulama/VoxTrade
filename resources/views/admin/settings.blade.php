@extends('layouts.admin')
@section('title', 'System Settings')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold text-white">System Settings</h1>
        <p class="text-gray-500 text-sm mt-0.5">Configure AI engine and signal generation parameters</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf

        {{-- Refresh Interval --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-rotate text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-white font-semibold mb-1">Signal Refresh Interval</label>
                    <p class="text-gray-500 text-xs mb-3">How often the AI engine analyzes markets and generates new signals (in minutes). Lower = more signals but higher API usage.</p>
                    <div class="flex items-center gap-3">
                        <input type="number" name="refresh_interval" min="1" max="60" value="{{ $settings['refresh_interval'] ?? 5 }}"
                            class="w-24 bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm text-center font-bold focus:outline-none focus:border-[#D4AF37]/50">
                        <span class="text-gray-400 text-sm">minutes</span>
                    </div>
                    @error('refresh_interval')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-gray-600 text-xs mt-2">Range: 1–60 minutes. Default: 5 minutes.</p>
                </div>
            </div>
        </div>

        {{-- Default Risk Level --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-halved text-yellow-400"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-white font-semibold mb-1">Default Risk Level</label>
                    <p class="text-gray-500 text-xs mb-3">The default risk classification applied to AI-generated signals when not explicitly specified.</p>
                    <div class="flex gap-3">
                        @foreach(['low', 'medium', 'high'] as $level)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="default_risk_level" value="{{ $level }}" {{ ($settings['default_risk_level'] ?? 'medium') === $level ? 'checked' : '' }} class="accent-yellow-500">
                            <span class="text-sm capitalize {{ $level === 'low' ? 'text-green-400' : ($level === 'medium' ? 'text-yellow-400' : 'text-red-400') }}">{{ ucfirst($level) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('default_risk_level')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- AI Sensitivity --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-brain text-purple-400"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-white font-semibold mb-1">
                        AI Sensitivity — <span id="sensitivityVal" class="text-[#D4AF37]">{{ $settings['ai_sensitivity'] ?? 70 }}</span>%
                    </label>
                    <p class="text-gray-500 text-xs mb-3">Minimum confidence threshold for AI signals to be stored. Higher = only high-confidence signals. Lower = more signals but potentially lower quality.</p>
                    <input type="range" name="ai_sensitivity" min="1" max="100" value="{{ $settings['ai_sensitivity'] ?? 70 }}"
                        class="w-full accent-yellow-500" oninput="document.getElementById('sensitivityVal').textContent=this.value">
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>1% (Permissive)</span>
                        <span>100% (Strict)</span>
                    </div>
                    @error('ai_sensitivity')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-gray-600 text-xs mt-2">Recommended: 60–80%. Signals below this threshold are discarded.</p>
                </div>
            </div>
        </div>

        <button type="submit" class="px-6 py-3 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
            <i class="fas fa-save mr-2"></i> Save Settings
        </button>
    </form>
</div>
@endsection
