<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VoxTrade') — VoxTrade</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            DEFAULT: '#D4AF37',
                            light:   '#FFD700',
                            dark:    '#B8962E',
                        },
                        brand: {
                            bg:      '#0a0a0a',
                            surface: '#111111',
                            card:    '#141414',
                            border:  '#1e1e1e',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    backdropBlur: {
                        xs: '2px',
                    },
                },
            },
        }
    </script>

    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background-color: #0a0a0a;
            color: #e5e5e5;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding-bottom: 4.5rem; /* space for bottom nav */
        }

        /* ── Glass card ── */
        .glass {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(212,175,55,0.20);
        }
        .glass-hover:hover {
            background: rgba(255,255,255,0.07);
            border-color: rgba(212,175,55,0.45);
            transform: translateY(-2px);
            transition: all .2s ease;
        }

        /* ── Gold gradient text ── */
        .gold-text {
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Bottom nav active ── */
        .nav-active { color: #D4AF37; }
        .nav-inactive { color: #6b7280; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #D4AF37; border-radius: 2px; }

        /* ── Confidence bar ── */
        .conf-bar { height: 6px; border-radius: 3px; background: #1e1e1e; }
        .conf-fill { height: 100%; border-radius: 3px; transition: width .5s ease; }

        /* ── Status chips ── */
        .badge { display:inline-flex; align-items:center; padding:.2rem .55rem; border-radius:9999px; font-size:.72rem; font-weight:600; letter-spacing:.02em; }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ── Top Bar ── -->
    <header class="sticky top-0 z-50 glass border-b border-[#D4AF37]/20 px-4 py-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="text-lg font-black gold-text tracking-tight">VOX<span class="text-white">TRADE</span></span>
        </a>
        <div class="flex items-center gap-3">
            @auth
            <span class="text-xs text-gray-400 hidden sm:block">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-red-400 transition text-sm" title="Logout">
                    <i class="fas fa-right-from-bracket"></i>
                </button>
            </form>
            @endauth
        </div>
    </header>

    <!-- ── Flash Messages ── -->
    @if(session('success'))
    <div class="mx-4 mt-3 p-3 rounded-lg border border-green-500/30 bg-green-500/10 text-green-400 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-4 mt-3 p-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm flex items-center gap-2">
        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    <!-- ── Main Content ── -->
    <main class="max-w-2xl mx-auto px-4 py-5">
        @yield('content')
    </main>

    <!-- ── Bottom Navigation ── -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 glass border-t border-[#D4AF37]/20">
        <div class="max-w-2xl mx-auto flex items-center justify-around px-2 py-2">
            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg transition {{ request()->routeIs('dashboard') ? 'nav-active' : 'nav-inactive hover:text-gray-300' }}">
                <i class="fas fa-house text-lg"></i>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="{{ route('signals.index') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg transition {{ request()->routeIs('signals.*') ? 'nav-active' : 'nav-inactive hover:text-gray-300' }}">
                <i class="fas fa-signal text-lg"></i>
                <span class="text-[10px] font-medium">Signals</span>
            </a>
            <a href="{{ route('performance') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg transition {{ request()->routeIs('performance') ? 'nav-active' : 'nav-inactive hover:text-gray-300' }}">
                <i class="fas fa-chart-line text-lg"></i>
                <span class="text-[10px] font-medium">Analytics</span>
            </a>
            <a href="{{ route('subscription.status') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg transition {{ request()->routeIs('subscription.*') ? 'nav-active' : 'nav-inactive hover:text-gray-300' }}">
                <i class="fas fa-crown text-lg"></i>
                <span class="text-[10px] font-medium">Plan</span>
            </a>
            <a href="{{ route('profile.edit') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg transition {{ request()->routeIs('profile.*') ? 'nav-active' : 'nav-inactive hover:text-gray-300' }}">
                <i class="fas fa-user text-lg"></i>
                <span class="text-[10px] font-medium">Profile</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
