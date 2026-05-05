@extends('layouts.admin')
@section('title', 'Setup Guide')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-bold text-white">How to Setup <span class="gold-text">VoxTrade</span></h1>
        <p class="text-gray-500 text-sm mt-0.5">Complete step-by-step guide to get your platform running</p>
    </div>

    @php
    $steps = [
        [
            'num' => '01',
            'icon' => 'fa-brain',
            'title' => 'Add Your OpenAI API Key',
            'color' => 'purple',
            'content' => [
                'Go to <strong class="text-white">platform.openai.com</strong> → Sign in → Click your profile → <strong class="text-white">API Keys</strong>',
                'Click <strong class="text-white">Create new secret key</strong> — copy the key immediately (shown only once)',
                'In VoxTrade Admin, go to <strong class="text-white">API Keys → Service: OpenAI</strong>',
                'Paste the key in the <strong class="text-white">API Key</strong> field and click Save',
                'The AI engine will now use <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">gpt-4o-mini</code> for signal generation',
            ],
            'note' => 'Ensure your OpenAI account has sufficient credits. Monitor usage at platform.openai.com/usage',
        ],
        [
            'num' => '02',
            'icon' => 'fab fa-bitcoin',
            'title' => 'Connect Binance API (Crypto Data)',
            'color' => 'orange',
            'content' => [
                'Go to <strong class="text-white">binance.com</strong> → Log in → Profile → <strong class="text-white">API Management</strong>',
                'Click <strong class="text-white">Create API</strong> → System Generated → Enter a label (e.g. "VoxTrade")',
                'Complete the security verification (email + 2FA)',
                '<strong class="text-red-400">Important:</strong> Enable <strong class="text-white">Read Info only</strong> — do NOT enable trading or withdrawal permissions',
                'Copy both the <strong class="text-white">API Key</strong> and <strong class="text-white">Secret Key</strong>',
                'In VoxTrade Admin → API Keys → Service: Binance → Paste Key + Secret',
            ],
            'note' => 'VoxTrade only reads market data from Binance. It cannot execute trades on your behalf.',
        ],
        [
            'num' => '03',
            'icon' => 'fa-chart-line',
            'title' => 'Add Alpha Vantage (Forex Data)',
            'color' => 'blue',
            'content' => [
                'Visit <strong class="text-white">alphavantage.co/support/#api-key</strong>',
                'Fill the free API key request form with your email',
                'You\'ll receive a key like <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">ABCDEF1234567890</code>',
                'In VoxTrade Admin → API Keys → Service: Alpha Vantage → Paste the key',
                'The free tier supports 5 API requests per minute and 500 per day',
            ],
            'note' => 'Free tier is sufficient for the default 5-minute refresh interval. For higher frequency, upgrade to a paid plan.',
        ],
        [
            'num' => '04',
            'icon' => 'fa-robot',
            'title' => 'Activate the AI Engine',
            'color' => 'green',
            'content' => [
                'With OpenAI + market API keys added, the AI engine is ready',
                'The scheduler runs <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">AnalyzeMarketJob</code> automatically every 5 minutes',
                'To start the scheduler, run on your server: <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">php artisan schedule:run</code>',
                'For production, add a cron job: <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1</code>',
                'To manually trigger signal generation: Admin → Trades → Click <strong class="text-white">Generate AI Signals</strong>',
                'For queued jobs, run: <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">php artisan queue:work</code>',
            ],
            'note' => 'Signals with confidence below your AI Sensitivity threshold (set in Settings) are automatically discarded.',
        ],
        [
            'num' => '05',
            'icon' => 'fa-gears',
            'title' => 'How Signals Are Generated',
            'color' => 'yellow',
            'content' => [
                '<strong class="text-white">Step 1:</strong> Fetch live OHLCV data from Binance (crypto) or Alpha Vantage (forex)',
                '<strong class="text-white">Step 2:</strong> Calculate technical indicators: RSI (14), EMA (9, 21), trend direction',
                '<strong class="text-white">Step 3:</strong> Build a structured prompt with all market data and send to OpenAI',
                '<strong class="text-white">Step 4:</strong> Parse the AI response — expecting JSON with pair, type, entry, SL, TP, confidence, duration',
                '<strong class="text-white">Step 5:</strong> Store signals with confidence ≥ threshold in the trades table',
                '<strong class="text-white">Step 6:</strong> Signals older than 24 hours are automatically marked as expired',
            ],
            'note' => 'Default pairs analyzed: BTC/USDT, ETH/USDT, BNB/USDT (crypto) + EUR/USD, GBP/USD, USD/JPY (forex)',
        ],
        [
            'num' => '06',
            'icon' => 'fa-credit-card',
            'title' => 'Setup Paystack Payments',
            'color' => 'teal',
            'content' => [
                'Create a Paystack account at <strong class="text-white">paystack.com</strong>',
                'Complete business verification to access live keys',
                'Go to Dashboard → Settings → API Keys & Webhooks',
                'Copy your <strong class="text-white">Public Key</strong> and <strong class="text-white">Secret Key</strong>',
                'Add to your <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">.env</code> file: <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">PAYSTACK_PUBLIC_KEY</code> and <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">PAYSTACK_SECRET_KEY</code>',
                'Also add Secret Key in Admin → API Keys → Service: Paystack',
                'Configure webhook URL in Paystack dashboard: <code class="text-[#D4AF37] bg-[#1a1a1a] px-1 rounded">https://yourdomain.com/subscription/webhook</code>',
            ],
            'note' => 'Use Paystack test keys during development. Switch to live keys when going to production.',
        ],
    ];
    @endphp

    <div class="space-y-4">
        @foreach($steps as $step)
        @php $colors = ['purple'=>['bg-purple-900/30','text-purple-400','border-purple-700/30'],'orange'=>['bg-orange-900/30','text-orange-400','border-orange-700/30'],'blue'=>['bg-blue-900/30','text-blue-400','border-blue-700/30'],'green'=>['bg-green-900/30','text-green-400','border-green-700/30'],'yellow'=>['bg-yellow-900/30','text-yellow-400','border-yellow-700/30'],'teal'=>['bg-teal-900/30','text-teal-400','border-teal-700/30']]; $c = $colors[$step['color']]; @endphp
        <div class="glass rounded-2xl overflow-hidden" x-data="{ open: false }">
            <button type="button" class="w-full px-5 py-4 flex items-center gap-4 text-left hover:bg-white/2 transition" onclick="this.nextElementSibling.classList.toggle('hidden')">
                <div class="w-10 h-10 rounded-xl {{ $c[0] }} flex items-center justify-center flex-shrink-0">
                    <i class="{{ str_starts_with($step['icon'], 'fab') ? $step['icon'] : 'fas '.$step['icon'] }} {{ $c[1] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">STEP {{ $step['num'] }}</span>
                    </div>
                    <p class="text-white font-semibold">{{ $step['title'] }}</p>
                </div>
                <i class="fas fa-chevron-down text-gray-500 text-sm transition-transform flex-shrink-0"></i>
            </button>
            <div class="hidden px-5 pb-5">
                <div class="border-t border-[#D4AF37]/10 pt-4">
                    <ol class="space-y-2.5">
                        @foreach($step['content'] as $i => $item)
                        <li class="flex gap-3 text-sm text-gray-300">
                            <span class="w-5 h-5 rounded-full {{ $c[0] }} {{ $c[1] }} flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                            <span>{!! $item !!}</span>
                        </li>
                        @endforeach
                    </ol>
                    @if(!empty($step['note']))
                    <div class="mt-4 p-3 rounded-xl {{ $c[0] }} border {{ $c[2] }} flex gap-2 text-xs {{ $c[1] }}">
                        <i class="fas fa-circle-info mt-0.5 flex-shrink-0"></i>
                        <span>{{ $step['note'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Links --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2"><i class="fas fa-link text-[#D4AF37]"></i> Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.api-keys.index') }}" class="flex flex-col items-center gap-2 p-4 bg-[#1a1a1a] rounded-xl hover:bg-[#222] transition text-center">
                <i class="fas fa-key text-[#D4AF37] text-xl"></i>
                <span class="text-xs text-gray-300">API Keys</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center gap-2 p-4 bg-[#1a1a1a] rounded-xl hover:bg-[#222] transition text-center">
                <i class="fas fa-gear text-[#D4AF37] text-xl"></i>
                <span class="text-xs text-gray-300">Settings</span>
            </a>
            <a href="{{ route('admin.trades.index') }}" class="flex flex-col items-center gap-2 p-4 bg-[#1a1a1a] rounded-xl hover:bg-[#222] transition text-center">
                <i class="fas fa-chart-candlestick text-[#D4AF37] text-xl"></i>
                <span class="text-xs text-gray-300">Trades</span>
            </a>
            <a href="{{ route('admin.cms.index') }}" class="flex flex-col items-center gap-2 p-4 bg-[#1a1a1a] rounded-xl hover:bg-[#222] transition text-center">
                <i class="fas fa-pen-to-square text-[#D4AF37] text-xl"></i>
                <span class="text-xs text-gray-300">CMS</span>
            </a>
        </div>
    </div>
</div>
@endsection
