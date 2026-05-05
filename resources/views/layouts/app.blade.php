<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VoxTrade') - AI Trade Intelligence</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    gold: { DEFAULT: '#D4AF37', light: '#FFD700', dark: '#B8960C', muted: '#9A7B1A' },
                    dark: { DEFAULT: '#0a0a0a', card: '#111111', border: '#1e1e1e', surface: '#161616' }
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; color: #e5e5e5; }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37 0%, #FFD700 50%, #B8960C 100%); }
        .gold-text { background: linear-gradient(135deg, #D4AF37, #FFD700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.15); }
        .gold-border { border: 1px solid rgba(212,175,55,0.3); }
        .gold-glow { box-shadow: 0 0 20px rgba(212,175,55,0.2); }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #D4AF37; border-radius: 2px; }
        .signal-card:hover { transform: translateY(-2px); transition: all 0.3s ease; }
        @keyframes pulse-gold { 0%,100%{opacity:1} 50%{opacity:0.5} }
        .animate-pulse-gold { animation: pulse-gold 2s cubic-bezier(0.4,0,0.6,1) infinite; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#0a0a0a] text-gray-200 min-h-screen">

<!-- Top Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-[#0a0a0a]/95 backdrop-blur-md border-b border-[#D4AF37]/20">
    <div class="flex items-center justify-between px-4 py-3 max-w-7xl mx-auto">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 gold-gradient rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-black text-sm"></i>
            </div>
            <span class="font-bold text-lg gold-text">VoxTrade</span>
        </a>

        <!-- Right side -->
        <div class="flex items-center gap-3">
            @if(auth()->check() && auth()->user()->subscription && auth()->user()->subscription->is_active)
                <span class="text-xs px-2 py-1 rounded-full bg-green-900/40 text-green-400 border border-green-700/30 hidden sm:flex items-center gap-1">
                    <i class="fas fa-circle text-[7px] animate-pulse-gold"></i> Active
                </span>
            @endif

            <!-- Notifications bell -->
            <button class="w-8 h-8 rounded-full bg-white/5 border border-[#D4AF37]/20 flex items-center justify-center text-gray-400 hover:text-[#D4AF37] transition relative">
                <i class="fas fa-bell text-sm"></i>
            </button>

            <!-- Avatar dropdown -->
            <div class="relative group">
                <button class="w-8 h-8 rounded-full gold-gradient flex items-center justify-center text-black font-bold text-sm cursor-pointer select-none">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </button>
                <div class="absolute right-0 top-10 w-52 glass-card rounded-xl shadow-2xl invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 z-50 pointer-events-none group-hover:pointer-events-auto">
                    <div class="p-3 border-b border-[#D4AF37]/20">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? '' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="p-2 space-y-0.5">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/5 text-sm text-gray-300 hover:text-white transition">
                            <i class="fas fa-user-circle text-[#D4AF37] w-4 text-center"></i> Profile
                        </a>
                        <a href="{{ route('subscription.status') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/5 text-sm text-gray-300 hover:text-white transition">
                            <i class="fas fa-crown text-[#D4AF37] w-4 text-center"></i> Subscription
                        </a>
                        <a href="{{ route('subscription.plans') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/5 text-sm text-gray-300 hover:text-white transition">
                            <i class="fas fa-star text-[#D4AF37] w-4 text-center"></i> Upgrade Plan
                        </a>
                        <div class="border-t border-white/5 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-red-900/20 text-sm text-red-400 hover:text-red-300 transition">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="pt-16 pb-20 min-h-screen">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mx-4 mt-4 p-3 rounded-xl bg-green-900/30 border border-green-700/40 text-green-400 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle flex-shrink-0"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-300 ml-auto">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="mx-4 mt-4 p-3 rounded-xl bg-red-900/30 border border-red-700/40 text-red-400 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-300 ml-auto">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif
    @if(session('warning'))
    <div class="mx-4 mt-4 p-3 rounded-xl bg-yellow-900/30 border border-yellow-700/40 text-yellow-400 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-triangle flex-shrink-0"></i>
        <span class="flex-1">{{ session('warning') }}</span>
        <button onclick="this.parentElement.remove()" class="text-yellow-600 hover:text-yellow-300 ml-auto">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif
    @if(session('info'))
    <div class="mx-4 mt-4 p-3 rounded-xl bg-blue-900/30 border border-blue-700/40 text-blue-400 text-sm flex items-center gap-2">
        <i class="fas fa-info-circle flex-shrink-0"></i>
        <span class="flex-1">{{ session('info') }}</span>
        <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-300 ml-auto">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif

    @yield('content')
</main>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-[#0a0a0a]/95 backdrop-blur-md border-t border-[#D4AF37]/20">
    <div class="flex items-center justify-around px-2 py-2 max-w-lg mx-auto">
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'text-[#D4AF37]' : 'text-gray-500 hover:text-[#D4AF37]' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        <a href="{{ route('signals.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all {{ request()->routeIs('signals.*') ? 'text-[#D4AF37]' : 'text-gray-500 hover:text-[#D4AF37]' }}">
            <i class="fas fa-bolt text-lg"></i>
            <span class="text-[10px] font-medium">Signals</span>
        </a>
        <a href="{{ route('performance') }}"
           class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all {{ request()->routeIs('performance') ? 'text-[#D4AF37]' : 'text-gray-500 hover:text-[#D4AF37]' }}">
            <i class="fas fa-chart-bar text-lg"></i>
            <span class="text-[10px] font-medium">Stats</span>
        </a>
        <a href="{{ route('subscription.plans') }}"
           class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all {{ request()->routeIs('subscription.*') ? 'text-[#D4AF37]' : 'text-gray-500 hover:text-[#D4AF37]' }}">
            <i class="fas fa-crown text-lg"></i>
            <span class="text-[10px] font-medium">Plans</span>
        </a>
        <a href="{{ route('profile.edit') }}"
           class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'text-[#D4AF37]' : 'text-gray-500 hover:text-[#D4AF37]' }}">
            <i class="fas fa-user text-lg"></i>
            <span class="text-[10px] font-medium">Profile</span>
        </a>
    </div>
</nav>

@stack('scripts')
</body>
</html>
