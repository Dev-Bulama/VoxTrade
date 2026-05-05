<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_name'] ?? 'VoxTrade' }} — AI-Powered Trade Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    gold: { DEFAULT: '#D4AF37', light: '#FFD700', dark: '#B8960C' }
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #e5e5e5; }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37 0%, #FFD700 50%, #B8960C 100%); }
        .gold-text { background: linear-gradient(135deg, #D4AF37, #FFD700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.15); }
        .gold-border { border: 1px solid rgba(212,175,55,0.35); }
        .gold-glow { box-shadow: 0 0 40px rgba(212,175,55,0.15); }
        .hero-bg { background: radial-gradient(ellipse at 50% 0%, rgba(212,175,55,0.08) 0%, transparent 60%), #0a0a0a; }
        ::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-thumb { background: #D4AF37; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-[#D4AF37]/15">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 gold-gradient rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-black text-sm"></i>
            </div>
            <span class="font-bold text-xl gold-text">VoxTrade</span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-400">
            <a href="#features" class="hover:text-[#D4AF37] transition">Features</a>
            <a href="#pricing" class="hover:text-[#D4AF37] transition">Pricing</a>
            <a href="{{ route('terms') }}" class="hover:text-[#D4AF37] transition">Terms</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition">Sign In</a>
                <a href="{{ route('register') }}" class="gold-gradient text-black text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">Get Started</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-bg min-h-screen flex items-center pt-20">
    <div class="max-w-6xl mx-auto px-4 py-20 text-center">
        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 text-xs text-[#D4AF37] mb-8 gold-border">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            AI Engine Active — Live Signals Running
        </div>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white leading-tight mb-6">
            {{ $settings['hero_title'] ?? 'Trade Smarter with' }}<br>
            <span class="gold-text">AI-Powered Signals</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            {{ $settings['hero_subtitle'] ?? 'Real-time Forex & Crypto trade signals powered by advanced AI analysis. Entry, Stop Loss, and Take Profit — all calculated for you.' }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="gold-gradient text-black font-bold px-8 py-4 rounded-xl text-lg hover:opacity-90 transition gold-glow w-full sm:w-auto">
                <i class="fas fa-rocket mr-2"></i> Start Trading Free
            </a>
            <a href="#pricing" class="glass gold-border text-[#D4AF37] font-semibold px-8 py-4 rounded-xl text-lg hover:bg-white/5 transition w-full sm:w-auto">
                <i class="fas fa-crown mr-2"></i> View Plans
            </a>
        </div>

        <!-- Hero Signal Card Preview -->
        <div class="mt-16 flex justify-center">
            <div class="glass gold-border rounded-2xl p-5 max-w-sm w-full text-left float gold-glow">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-white font-bold text-lg">BTC/USDT</span>
                    <span class="bg-green-500/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full border border-green-500/30">BUY</span>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Entry</p>
                        <p class="text-sm font-semibold text-white">65,200</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Stop Loss</p>
                        <p class="text-sm font-semibold text-red-400">64,100</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Take Profit</p>
                        <p class="text-sm font-semibold text-green-400">67,800</p>
                    </div>
                </div>
                <div class="mb-2 flex items-center justify-between text-xs">
                    <span class="text-gray-400">Confidence</span>
                    <span class="text-[#D4AF37] font-bold">87%</span>
                </div>
                <div class="w-full bg-[#1e1e1e] rounded-full h-2">
                    <div class="gold-gradient h-2 rounded-full" style="width:87%"></div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-gray-500"><i class="fas fa-clock mr-1"></i>Intraday</span>
                    <span class="text-xs text-[#D4AF37]">🤖 AI Generated</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<section class="bg-[#111111] border-y border-[#D4AF37]/10 py-8">
    <div class="max-w-4xl mx-auto px-4 grid grid-cols-3 gap-4 text-center">
        <div>
            <p class="text-2xl md:text-3xl font-black gold-text">500+</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1">Active Traders</p>
        </div>
        <div>
            <p class="text-2xl md:text-3xl font-black gold-text">87%</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1">Win Rate</p>
        </div>
        <div>
            <p class="text-2xl md:text-3xl font-black gold-text">24/7</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1">AI Analysis</p>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="py-20 max-w-6xl mx-auto px-4">
    <div class="text-center mb-14">
        <p class="text-[#D4AF37] text-sm font-semibold uppercase tracking-widest mb-3">Why VoxTrade</p>
        <h2 class="text-3xl md:text-4xl font-bold text-white">{{ $settings['features_title'] ?? 'The Edge Every Trader Needs' }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach([
            ['icon'=>'fa-brain','title'=>'AI-Powered Analysis','desc'=>'OpenAI-driven market analysis processes RSI, MACD, EMA and trend data to generate high-quality signals.'],
            ['icon'=>'fa-bolt','title'=>'Live Signals','desc'=>'Real-time BUY/SELL signals refreshed every 5 minutes. Never miss a market opportunity.'],
            ['icon'=>'fa-shield-halved','title'=>'Risk Management','desc'=>'Every signal includes precise Stop Loss and Take Profit levels calculated by AI.'],
            ['icon'=>'fa-globe','title'=>'Multi-Market','desc'=>'Covers major Forex pairs (EUR/USD, GBP/USD) and top Crypto assets (BTC, ETH) simultaneously.'],
            ['icon'=>'fa-chart-line','title'=>'Confidence Scoring','desc'=>'Each signal comes with a 0-100% confidence score so you know which signals to prioritize.'],
            ['icon'=>'fa-rotate','title'=>'Auto Refresh','desc'=>'Laravel scheduler automatically analyzes markets and updates signals every 5 minutes, 24/7.'],
        ] as $f)
        <div class="glass rounded-2xl p-6 hover:border-[#D4AF37]/30 transition-all duration-300 group">
            <div class="w-12 h-12 gold-gradient rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas {{ $f['icon'] }} text-black text-lg"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">{{ $f['title'] }}</h3>
            <p class="text-gray-400 text-sm leading-relaxed">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-20 bg-[#0d0d0d]">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <p class="text-[#D4AF37] text-sm font-semibold uppercase tracking-widest mb-3">Simple Process</p>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-14">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['step'=>'01','icon'=>'fa-user-plus','title'=>'Create Account','desc'=>'Sign up in seconds with your email and password.'],
                ['step'=>'02','icon'=>'fa-crown','title'=>'Subscribe','desc'=>'Choose a plan from Daily, Weekly or Monthly. Powered by Paystack.'],
                ['step'=>'03','icon'=>'fa-chart-candlestick','title'=>'Receive Signals','desc'=>'Get live AI-generated trade signals with entry, SL, TP and confidence score.'],
            ] as $s)
            <div class="text-center">
                <div class="relative inline-flex">
                    <div class="w-16 h-16 gold-gradient rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas {{ $s['icon'] }} text-black text-2xl"></i>
                    </div>
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-[#0a0a0a] border border-[#D4AF37] rounded-full text-[#D4AF37] text-xs font-bold flex items-center justify-center">{{ $s['step'] }}</span>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $s['title'] }}</h3>
                <p class="text-gray-400 text-sm">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- PRICING -->
<section id="pricing" class="py-20 max-w-5xl mx-auto px-4">
    <div class="text-center mb-14">
        <p class="text-[#D4AF37] text-sm font-semibold uppercase tracking-widest mb-3">Pricing</p>
        <h2 class="text-3xl md:text-4xl font-bold text-white">{{ $settings['pricing_title'] ?? 'Simple, Transparent Pricing' }}</h2>
        <p class="text-gray-400 mt-3">No hidden fees. Cancel anytime.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['name'=>'Daily','price'=>'₦500','period'=>'/day','features'=>['All live signals','AI analysis','24hr access','Basic support'],'popular'=>false,'plan'=>'daily'],
            ['name'=>'Monthly','price'=>'₦5,000','period'=>'/month','features'=>['All live signals','AI analysis','30-day access','Performance analytics','Priority support','Best value'],'popular'=>true,'plan'=>'monthly'],
            ['name'=>'Weekly','price'=>'₦2,000','period'=>'/week','features'=>['All live signals','AI analysis','7-day access','Performance analytics','Standard support'],'popular'=>false,'plan'=>'weekly'],
        ] as $p)
        <div class="rounded-2xl p-6 relative {{ $p['popular'] ? 'gold-gradient p-0.5 gold-glow' : 'glass' }}">
            @if($p['popular'])
            <div class="bg-[#111111] rounded-2xl p-6 h-full">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 gold-gradient text-black text-xs font-bold px-4 py-1 rounded-full">MOST POPULAR</div>
            @endif
                <div class="flex items-center gap-2 mb-6">
                    <i class="fas fa-crown text-[#D4AF37]"></i>
                    <h3 class="text-white font-bold text-xl">{{ $p['name'] }}</h3>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-black text-white">{{ $p['price'] }}</span>
                    <span class="text-gray-400 text-sm">{{ $p['period'] }}</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($p['features'] as $f)
                    <li class="flex items-center gap-2 text-sm text-gray-300">
                        <i class="fas fa-check text-[#D4AF37] text-xs"></i> {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="{{ $p['popular'] ? 'gold-gradient text-black' : 'glass gold-border text-[#D4AF37] hover:bg-white/5' }} block text-center font-bold py-3 rounded-xl transition">
                    Get Started
                </a>
            @if($p['popular'])
            </div>
            @endif
        </div>
        @endforeach
    </div>
</section>

<!-- DISCLAIMER -->
<section class="max-w-4xl mx-auto px-4 pb-16">
    <div class="glass gold-border rounded-2xl p-6 text-center">
        <i class="fas fa-triangle-exclamation text-[#D4AF37] text-2xl mb-3"></i>
        <p class="text-gray-400 text-sm leading-relaxed">
            {{ $settings['disclaimer'] ?? 'This platform provides AI-assisted trade insights. Not financial advice. Trading involves risk. Past performance does not guarantee future results.' }}
        </p>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-[#080808] border-t border-[#D4AF37]/10 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 gold-gradient rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-black text-sm"></i>
                </div>
                <span class="font-bold text-lg gold-text">VoxTrade</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition">Home</a>
                <a href="#pricing" class="hover:text-[#D4AF37] transition">Pricing</a>
                <a href="{{ route('terms') }}" class="hover:text-[#D4AF37] transition">Terms</a>
                <a href="{{ route('login') }}" class="hover:text-[#D4AF37] transition">Login</a>
            </div>
        </div>
        <div class="mt-6 pt-6 border-t border-[#D4AF37]/10 text-center text-xs text-gray-600">
            {{ $settings['footer_text'] ?? '© 2025 VoxTrade. AI-assisted trade insights. Not financial advice.' }}
        </div>
    </div>
</footer>

</body>
</html>
