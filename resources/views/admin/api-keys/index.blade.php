@extends('layouts.admin')
@section('title', 'API Keys')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">API Key Management</h1>
        <p class="text-gray-500 text-sm mt-0.5">Configure external service integrations — keys are encrypted at rest</p>
    </div>

    <div class="glass rounded-2xl p-4 border border-yellow-700/30 bg-yellow-900/10">
        <div class="flex gap-3">
            <i class="fas fa-shield-halved text-[#D4AF37] text-lg mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="text-[#D4AF37] font-semibold text-sm mb-1">Security Notice</p>
                <p class="text-gray-400 text-xs">All API keys are encrypted using AES-256 before storage. Keys are never exposed in logs or responses. Only add keys from trusted, official sources.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Add / Update Form --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
                <i class="fas fa-plus-circle text-[#D4AF37]"></i> Add / Update API Key
            </h3>
            <form method="POST" action="{{ route('admin.api-keys.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Service</label>
                    <select name="service_name" class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                        @foreach($services as $svc)
                        <option value="{{ $svc }}">{{ ucwords(str_replace('_', ' ', $svc)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">API Key <span class="text-red-400">*</span></label>
                    <input type="password" name="api_key" placeholder="Paste your API key here" required
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 font-mono">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">API Secret <span class="text-gray-600">(optional)</span></label>
                    <input type="password" name="api_secret" placeholder="API secret / webhook secret"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 font-mono">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Extra Config <span class="text-gray-600">(JSON, optional)</span></label>
                    <textarea name="extra_config" rows="2" placeholder='{"base_url":"https://...","model":"gpt-4o-mini"}'
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 font-mono resize-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                    <i class="fas fa-key mr-2"></i> Save API Key
                </button>
            </form>
        </div>

        {{-- Existing Keys --}}
        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[#D4AF37]/10">
                <h3 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-list text-[#D4AF37]"></i> Configured Services
                </h3>
            </div>
            <div class="divide-y divide-[#1a1a1a]">
                @forelse($apiKeys as $key)
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center">
                                @php $icons = ['openai'=>'fa-brain','binance'=>'fab fa-bitcoin','alpha_vantage'=>'fa-chart-line','tradingview'=>'fa-chart-candlestick','paystack'=>'fa-credit-card','flutterwave'=>'fa-bolt','telegram'=>'fab fa-telegram']; @endphp
                                <i class="{{ $icons[$key->service_name] ?? 'fa-key' }} text-[#D4AF37] text-xs {{ str_starts_with($icons[$key->service_name] ?? '', 'fab') ? '' : 'fas' }}"></i>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">{{ ucwords(str_replace('_', ' ', $key->service_name)) }}</p>
                                <p class="text-gray-600 text-xs font-mono">••••••••{{ substr($key->api_key ?? '', -4) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.api-keys.toggle', $key) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded-full font-medium {{ $key->is_active ? 'bg-green-900/30 text-green-400 border border-green-700/30' : 'bg-gray-900/30 text-gray-400 border border-gray-700/30' }} hover:opacity-80 transition">
                                    {{ $key->is_active ? 'Active' : 'Disabled' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.api-keys.destroy', $key) }}" onsubmit="return confirm('Remove this API key?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 flex items-center justify-center transition">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if($key->api_secret)
                    <p class="text-gray-600 text-xs font-mono mt-1 pl-11">Secret: ••••••••{{ substr($key->api_secret ?? '', -4) }}</p>
                    @endif
                    <p class="text-gray-600 text-xs mt-1 pl-11">Updated: {{ $key->updated_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <i class="fas fa-key text-3xl text-gray-700 mb-2 block"></i>
                    <p class="text-gray-500 text-sm">No API keys configured.</p>
                    <p class="text-gray-600 text-xs mt-1">Add your first key using the form.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Setup Guide --}}
    <div class="glass rounded-2xl p-5 border border-[#D4AF37]/10">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-circle-info text-[#D4AF37]"></i> Quick Setup Reference
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @foreach([
                ['OpenAI','Get from platform.openai.com → API Keys','fa-brain'],
                ['Binance','Get from binance.com → API Management (Read-Only permissions)','fab fa-bitcoin'],
                ['Alpha Vantage','Free key at alphavantage.co/support/#api-key','fa-chart-line'],
                ['Paystack','Get from dashboard.paystack.com → Settings → API Keys','fa-credit-card'],
            ] as $guide)
            <div class="flex gap-3 p-3 bg-[#1a1a1a] rounded-xl">
                <i class="{{ $guide[2] }} text-[#D4AF37] mt-0.5 flex-shrink-0 {{ str_starts_with($guide[2], 'fab') ? '' : 'fas' }}"></i>
                <div>
                    <p class="text-white font-medium">{{ $guide[0] }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $guide[1] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <p class="mt-4 text-center text-xs text-gray-600">
            <a href="{{ route('admin.guide') }}" class="text-[#D4AF37] hover:underline">
                <i class="fas fa-book mr-1"></i> View the full setup guide →
            </a>
        </p>
    </div>
</div>
@endsection
