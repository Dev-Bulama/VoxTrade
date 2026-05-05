<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — VoxTrade Admin</title>

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
                },
            },
        }
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background-color: #0a0a0a;
            color: #e5e5e5;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: #0d0d0d;
            border-right: 1px solid rgba(212,175,55,0.15);
            position: fixed;
            top: 0; left: 0;
            z-index: 40;
            transition: transform .25s ease;
        }
        #sidebar.collapsed { transform: translateX(-240px); }

        /* Main wrapper */
        #main-wrap {
            margin-left: 240px;
            transition: margin-left .25s ease;
        }
        #main-wrap.expanded { margin-left: 0; }

        /* Glass */
        .glass {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(212,175,55,0.20);
        }

        /* Gold text */
        .gold-text {
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Nav links */
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: .5rem .75rem; border-radius: .5rem;
            font-size: .85rem; font-weight: 500; color: #9ca3af;
            transition: all .15s ease; text-decoration: none;
        }
        .nav-link:hover { color: #D4AF37; background: rgba(212,175,55,0.08); }
        .nav-link.active { color: #D4AF37; background: rgba(212,175,55,0.12); border-left: 3px solid #D4AF37; padding-left: calc(.75rem - 3px); }
        .nav-link i { width: 16px; text-align: center; }

        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table thead th {
            background: rgba(212,175,55,0.08);
            color: #D4AF37; font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            padding: .65rem 1rem; text-align: left;
            border-bottom: 1px solid rgba(212,175,55,0.15);
        }
        .admin-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background .12s ease;
        }
        .admin-table tbody tr:nth-child(even) { background: rgba(255,255,255,0.015); }
        .admin-table tbody tr:hover { background: rgba(212,175,55,0.05); }
        .admin-table tbody td { padding: .6rem 1rem; font-size: .82rem; color: #d1d5db; vertical-align: middle; }

        /* Badges */
        .badge { display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:700; letter-spacing:.03em; }

        /* Inputs */
        .admin-input {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.2);
            border-radius: .5rem; padding: .55rem .85rem; color: #e5e5e5; font-size: .875rem;
            outline: none; transition: border-color .15s ease;
        }
        .admin-input:focus { border-color: rgba(212,175,55,0.6); box-shadow: 0 0 0 3px rgba(212,175,55,0.08); }
        .admin-input::placeholder { color: #6b7280; }
        select.admin-input option { background: #141414; color: #e5e5e5; }

        /* Btn */
        .btn-gold {
            display: inline-flex; align-items: center; gap: .4rem;
            background: linear-gradient(135deg, #D4AF37, #B8962E);
            color: #0a0a0a; font-weight: 700; font-size: .82rem;
            padding: .5rem 1.1rem; border-radius: .5rem; border: none;
            cursor: pointer; text-decoration: none; transition: opacity .15s ease;
        }
        .btn-gold:hover { opacity: .88; }
        .btn-outline {
            display: inline-flex; align-items: center; gap: .4rem;
            background: transparent; color: #9ca3af; font-size: .82rem; font-weight: 600;
            padding: .5rem 1.1rem; border-radius: .5rem; border: 1px solid rgba(255,255,255,0.12);
            cursor: pointer; text-decoration: none; transition: all .15s ease;
        }
        .btn-outline:hover { color: #e5e5e5; border-color: rgba(255,255,255,0.3); }
        .btn-danger {
            display: inline-flex; align-items: center; gap: .4rem;
            background: transparent; color: #f87171; font-size: .78rem; font-weight: 600;
            padding: .35rem .75rem; border-radius: .4rem; border: 1px solid rgba(248,113,113,0.3);
            cursor: pointer; text-decoration: none; transition: all .15s ease;
        }
        .btn-danger:hover { background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.6); }
        .btn-sm {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .75rem; font-weight: 600; padding: .3rem .65rem;
            border-radius: .4rem; border: 1px solid rgba(212,175,55,0.3);
            color: #D4AF37; background: transparent; text-decoration: none;
            cursor: pointer; transition: all .15s ease;
        }
        .btn-sm:hover { background: rgba(212,175,55,0.1); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #D4AF37; border-radius: 3px; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-240px); }
            #sidebar.open { transform: translateX(0); }
            #main-wrap { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ── Sidebar ── -->
<aside id="sidebar">
    <!-- Logo -->
    <div class="flex items-center gap-2 px-5 py-4 border-b border-[#D4AF37]/15">
        <span class="text-lg font-black gold-text tracking-tight">VOX<span class="text-white">TRADE</span></span>
        <span class="text-[10px] font-bold text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded ml-1">ADMIN</span>
    </div>

    <!-- Nav -->
    <nav class="p-3 space-y-0.5 mt-2">
        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest px-3 mb-2 mt-3">Overview</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i> Dashboard
        </a>

        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest px-3 mb-2 mt-4">Management</p>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="{{ route('admin.trades.index') }}" class="nav-link {{ request()->routeIs('admin.trades.*') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i> Signals / Trades
        </a>

        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest px-3 mb-2 mt-4">Configuration</p>
        <a href="{{ route('admin.api-keys.index') }}" class="nav-link {{ request()->routeIs('admin.api-keys.*') ? 'active' : '' }}">
            <i class="fas fa-key"></i> API Keys
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-sliders"></i> Settings
        </a>
        <a href="{{ route('admin.cms.index') }}" class="nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
            <i class="fas fa-pen-to-square"></i> CMS / Content
        </a>

        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest px-3 mb-2 mt-4">Help</p>
        <a href="{{ route('admin.guide') }}" class="nav-link {{ request()->routeIs('admin.guide') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i> Setup Guide
        </a>
    </nav>

    <!-- Admin user -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-[#D4AF37]/10 p-3">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#D4AF37] to-[#B8962E] flex items-center justify-center text-black text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[10px] text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left text-xs text-gray-500 hover:text-red-400 transition flex items-center gap-1.5 px-1 py-1">
                <i class="fas fa-right-from-bracket"></i> Sign out
            </button>
        </form>
    </div>
</aside>

<!-- ── Main Wrapper ── -->
<div id="main-wrap">

    <!-- Top bar -->
    <header class="sticky top-0 z-30 bg-[#0d0d0d] border-b border-[#D4AF37]/10 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" class="text-gray-400 hover:text-gold transition md:hidden" onclick="toggleSidebar()">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <button id="sidebar-toggle-desktop" class="text-gray-400 hover:text-[#D4AF37] transition hidden md:block" onclick="toggleSidebar()">
                <i class="fas fa-bars text-base"></i>
            </button>
            <h2 class="text-sm font-semibold text-gray-300">@yield('title', 'Admin Panel')</h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" target="_blank" class="text-xs text-gray-500 hover:text-[#D4AF37] transition flex items-center gap-1.5">
                <i class="fas fa-arrow-up-right-from-square"></i>
                <span class="hidden sm:inline">View Site</span>
            </a>
            <span class="w-px h-4 bg-gray-700"></span>
            <span class="text-xs text-gray-400 font-medium">{{ now()->format('d M Y') }}</span>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="px-6 pt-4">
        @if(session('success'))
        <div class="mb-4 p-3 rounded-lg border border-green-500/30 bg-green-500/10 text-green-400 text-sm flex items-center gap-2">
            <i class="fas fa-circle-check flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-500/60 hover:text-green-400"><i class="fas fa-xmark"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm flex items-center gap-2">
            <i class="fas fa-circle-exclamation flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-500/60 hover:text-red-400"><i class="fas fa-xmark"></i></button>
        </div>
        @endif
        @if(session('warning'))
        <div class="mb-4 p-3 rounded-lg border border-yellow-500/30 bg-yellow-500/10 text-yellow-400 text-sm flex items-center gap-2">
            <i class="fas fa-triangle-exclamation flex-shrink-0"></i>
            <span>{{ session('warning') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-yellow-500/60 hover:text-yellow-400"><i class="fas fa-xmark"></i></button>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm">
            <div class="flex items-center gap-2 mb-1.5">
                <i class="fas fa-circle-exclamation"></i> <strong>Please fix the following errors:</strong>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="px-6 pb-10">
        @yield('content')
    </main>
</div>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 hidden md:hidden" onclick="closeSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainWrap = document.getElementById('main-wrap');
        const overlay = document.getElementById('sidebar-overlay');
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        } else {
            sidebar.classList.toggle('collapsed');
            mainWrap.classList.toggle('expanded');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('open');
        overlay.classList.add('hidden');
    }

    // Confirm delete helper
    function confirmDelete(formId, msg) {
        if (confirm(msg || 'Are you sure you want to delete this? This action cannot be undone.')) {
            document.getElementById(formId).submit();
        }
    }
</script>
@stack('scripts')
</body>
</html>
