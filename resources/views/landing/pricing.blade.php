<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing — VoxTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family:'Inter',sans-serif; background:#0a0a0a; color:#e5e5e5; }
        .gold-gradient { background:linear-gradient(135deg,#D4AF37 0%,#FFD700 50%,#B8960C 100%); }
        .gold-text { background:linear-gradient(135deg,#D4AF37,#FFD700); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .glass { background:rgba(255,255,255,0.04); backdrop-filter:blur(12px); border:1px solid rgba(212,175,55,0.15); }
        .gold-glow { box-shadow:0 0 40px rgba(212,175,55,0.2); }
    </style>
</head>
<body class="min-h-screen">
    <nav class="border-b border-[#D4AF37]/15 py-4 px-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 gold-gradient rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-black text-sm"></i>
                </div>
                <span class="font-bold text-lg gold-text">VoxTrade</span>
            </a>
            <div class="flex gap-4 items-center text-sm">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Login</a>
                <a href="{{ route('register') }}" class="gold-gradient text-black font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">Register</a>
            </div>
        </div>
    </nav>
    <main class="max-w-5xl mx-auto px-4 py-16">
        <div class="text-center mb-14">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ $settings['pricing_title'] ?? 'Simple, Transparent Pricing' }}</h1>
            <p class="text-gray-400">Access AI-powered trade signals at any budget</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['name'=>'Daily','price'=>'₦500','period'=>'/day','features'=>['All live signals','AI analysis','24hr access','Basic support'],'popular'=>false],
                ['name'=>'Monthly','price'=>'₦5,000','period'=>'/month','features'=>['All live signals','AI analysis','30-day access','Performance analytics','Priority support','Best value'],'popular'=>true],
                ['name'=>'Weekly','price'=>'₦2,000','period'=>'/week','features'=>['All live signals','AI analysis','7-day access','Performance analytics','Standard support'],'popular'=>false],
            ] as $p)
            <div class="{{ $p['popular'] ? 'gold-gradient p-0.5 rounded-2xl gold-glow relative' : 'glass rounded-2xl' }}">
                @if($p['popular'])<div class="absolute -top-3 left-1/2 -translate-x-1/2 gold-gradient text-black text-xs font-bold px-4 py-1 rounded-full z-10">MOST POPULAR</div>@endif
                <div class="{{ $p['popular'] ? 'bg-[#111] rounded-2xl' : '' }} p-6 h-full">
                    <h3 class="text-white font-bold text-xl mb-4 flex items-center gap-2"><i class="fas fa-crown text-[#D4AF37]"></i> {{ $p['name'] }}</h3>
                    <div class="mb-6"><span class="text-4xl font-black text-white">{{ $p['price'] }}</span><span class="text-gray-400 text-sm">{{ $p['period'] }}</span></div>
                    <ul class="space-y-3 mb-8">
                        @foreach($p['features'] as $f)
                        <li class="flex items-center gap-2 text-sm text-gray-300"><i class="fas fa-check text-[#D4AF37] text-xs"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="{{ $p['popular'] ? 'gold-gradient text-black' : 'border border-[#D4AF37]/40 text-[#D4AF37] hover:bg-white/5' }} block text-center font-bold py-3 rounded-xl transition">Get Started</a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-10 glass rounded-2xl p-6 text-center border border-[#D4AF37]/20">
            <p class="text-gray-400 text-sm"><i class="fas fa-triangle-exclamation text-[#D4AF37] mr-2"></i>{{ $settings['disclaimer'] ?? 'AI-assisted signals only. Not financial advice. Trading involves risk.' }}</p>
        </div>
    </main>
</body>
</html>
