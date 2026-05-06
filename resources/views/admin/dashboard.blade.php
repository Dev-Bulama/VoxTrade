@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between pt-2">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-0.5">System overview and quick actions</p>
        </div>
        <span class="text-xs text-gray-500 flex items-center gap-1.5">
            <i class="fas fa-clock"></i> Last updated {{ now()->format('H:i') }}
        </span>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

        <!-- Total Traders -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Traders</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(212,175,55,0.12);">
                    <i class="fas fa-users text-sm" style="color:#D4AF37;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['total_users'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Registered users</p>
            </div>
        </div>

        <!-- Active Subscribers -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Subscribers</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(34,197,94,0.12);">
                    <i class="fas fa-crown text-sm" style="color:#22c55e;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['active_subscribers'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Active plans</p>
            </div>
        </div>

        <!-- Total Signals -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Signals</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(59,130,246,0.12);">
                    <i class="fas fa-bolt text-sm" style="color:#3b82f6;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['total_signals'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">All time</p>
            </div>
        </div>

        <!-- Active Signals -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Active Now</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(234,179,8,0.12);">
                    <i class="fas fa-circle-dot text-sm" style="color:#eab308;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['active_signals'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Live signals</p>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Revenue</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(168,85,247,0.12);">
                    <i class="fas fa-naira-sign text-sm" style="color:#a855f7;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">₦{{ number_format($stats['total_revenue'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total earnings</p>
            </div>
        </div>

        <!-- Win Rate -->
        <div class="glass rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Win Rate</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(212,175,55,0.12);">
                    <i class="fas fa-trophy text-sm" style="color:#D4AF37;"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ $stats['win_rate'] ?? 0 }}%</p>
                <p class="text-xs text-gray-500 mt-0.5">TP hit ratio</p>
            </div>
        </div>

    </div>

    <!-- Quick Actions -->
    <div class="glass rounded-xl p-4 flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-300 mr-1"><i class="fas fa-bolt text-[#D4AF37] mr-1.5"></i>Quick Actions</span>

        <form method="POST" action="{{ route('admin.trades.generate') }}" class="inline">
            @csrf
            <button type="submit"
                onclick="return confirm('Generate new AI signals now? This will call the AI engine.')"
                class="btn-gold">
                <i class="fas fa-wand-magic-sparkles"></i> Generate AI Signals
            </button>
        </form>

        <a href="{{ route('admin.users.index') }}" class="btn-outline">
            <i class="fas fa-users"></i> All Users
        </a>

        <a href="{{ route('admin.trades.index') }}" class="btn-outline">
            <i class="fas fa-bolt"></i> All Signals
        </a>

        <a href="{{ route('admin.trades.create') }}" class="btn-outline">
            <i class="fas fa-plus"></i> Add Signal
        </a>

        <a href="{{ route('admin.api-keys.index') }}" class="btn-outline">
            <i class="fas fa-key"></i> API Keys
        </a>
    </div>

    <!-- System Health Panel -->
    <div class="glass rounded-xl p-5 border {{ ($health['openai_key'] && $health['paystack_key']) ? 'border-green-500/20' : 'border-red-500/20' }}">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-heartbeat text-[#D4AF37] text-xs"></i> System Health
            </h3>
            <span class="text-[10px] text-gray-500">Last signal: {{ $health['last_signal_at'] ? \Carbon\Carbon::parse($health['last_signal_at'])->diffForHumans() : 'Never' }}</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            {{-- OpenAI Key --}}
            <div class="rounded-xl p-3 border {{ $health['openai_key'] ? 'border-green-500/30 bg-green-900/10' : 'border-red-500/30 bg-red-900/10' }}">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-brain text-xs {{ $health['openai_key'] ? 'text-green-400' : 'text-red-400' }}"></i>
                    <span class="text-xs font-semibold text-white">OpenAI Key</span>
                </div>
                <p class="text-[11px] {{ $health['openai_key'] ? 'text-green-400' : 'text-red-400' }} font-bold">
                    {{ $health['openai_key'] ? '✓ Configured' : '✗ MISSING' }}
                </p>
                @if(!$health['openai_key'])
                <a href="{{ route('admin.api-keys.index') }}" class="text-[10px] text-[#D4AF37] hover:underline">Add key →</a>
                @endif
            </div>
            {{-- Paystack Key --}}
            <div class="rounded-xl p-3 border {{ $health['paystack_key'] ? 'border-green-500/30 bg-green-900/10' : 'border-red-500/30 bg-red-900/10' }}">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-credit-card text-xs {{ $health['paystack_key'] ? 'text-green-400' : 'text-red-400' }}"></i>
                    <span class="text-xs font-semibold text-white">Paystack Key</span>
                </div>
                <p class="text-[11px] {{ $health['paystack_key'] ? 'text-green-400' : 'text-red-400' }} font-bold">
                    {{ $health['paystack_key'] ? '✓ Configured' : '✗ MISSING' }}
                </p>
                @if(!$health['paystack_key'])
                <a href="{{ route('admin.api-keys.index') }}" class="text-[10px] text-[#D4AF37] hover:underline">Add key →</a>
                @endif
            </div>
            {{-- AI Sensitivity --}}
            <div class="rounded-xl p-3 border border-[#D4AF37]/20 bg-[#D4AF37]/5">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-sliders text-xs text-[#D4AF37]"></i>
                    <span class="text-xs font-semibold text-white">AI Threshold</span>
                </div>
                <p class="text-[11px] text-[#D4AF37] font-bold">{{ $health['ai_sensitivity'] }}% confidence min</p>
                <a href="{{ route('admin.settings.index') }}" class="text-[10px] text-gray-500 hover:text-[#D4AF37]">Adjust →</a>
            </div>
            {{-- Active Signals --}}
            <div class="rounded-xl p-3 border border-blue-500/20 bg-blue-900/10">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-bolt text-xs text-blue-400"></i>
                    <span class="text-xs font-semibold text-white">Active Signals</span>
                </div>
                <p class="text-[11px] text-blue-400 font-bold">{{ $health['active_signals'] }} live</p>
                <a href="{{ route('admin.trades.index') }}" class="text-[10px] text-gray-500 hover:text-blue-400">View all →</a>
            </div>
        </div>
        {{-- Scheduler setup instructions --}}
        @if(!$health['openai_key'] || !$health['paystack_key'])
        <div class="rounded-xl p-3 border border-yellow-500/20 bg-yellow-900/10 mb-3">
            <p class="text-xs font-semibold text-yellow-400 mb-2"><i class="fas fa-triangle-exclamation mr-1"></i> Action Required</p>
            <ul class="text-[11px] text-gray-400 space-y-1 list-disc list-inside">
                @if(!$health['openai_key'])<li>Add <strong class="text-white">OpenAI</strong> key (service: <code class="text-[#D4AF37]">openai</code>) — needed for AI signal generation</li>@endif
                @if(!$health['paystack_key'])<li>Add <strong class="text-white">Paystack</strong> secret key (service: <code class="text-[#D4AF37]">paystack</code>) — needed for payments</li>@endif
            </ul>
        </div>
        @endif
        <div class="rounded-xl p-3 border border-[#2a2a2a] bg-[#111]/50">
            <p class="text-xs font-semibold text-gray-300 mb-1.5"><i class="fas fa-terminal text-[#D4AF37] mr-1.5"></i> Auto-Scheduler Setup</p>
            <p class="text-[11px] text-gray-500 mb-2">Add this cron job on your server to run the AI engine continuously:</p>
            <code class="block text-[11px] text-green-400 bg-[#0a0a0a] rounded-lg px-3 py-2 font-mono">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
            <p class="text-[11px] text-gray-600 mt-2">Or run manually for testing: <code class="text-[#D4AF37]">php artisan schedule:work</code></p>
        </div>
    </div>

    <!-- Bottom Grid: Tables -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Recent Users -->
        <div class="glass rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#D4AF37]/10 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-users text-[#D4AF37] text-xs"></i> Recent Users
                </h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-[#D4AF37] hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers ?? [] as $user)
                        <tr>
                            <td>
                                <div class="font-medium text-white text-xs">{{ $user->name }}</div>
                                <div class="text-gray-500 text-[10px]">{{ $user->email }}</div>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge" style="background:rgba(34,197,94,0.15);color:#22c55e;">Active</span>
                                @else
                                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;">Inactive</span>
                                @endif
                            </td>
                            <td class="text-gray-400 text-xs">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-gray-600 py-6 text-xs">No users yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Trades -->
        <div class="glass rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#D4AF37]/10 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-bolt text-[#D4AF37] text-xs"></i> Recent Signals
                </h3>
                <a href="{{ route('admin.trades.index') }}" class="text-xs text-[#D4AF37] hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pair</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTrades ?? [] as $trade)
                        <tr>
                            <td>
                                <div class="font-bold text-white text-xs">{{ $trade->pair }}</div>
                                <div class="text-gray-500 text-[10px]">{{ $trade->confidence }}% conf.</div>
                            </td>
                            <td>
                                @if(strtoupper($trade->type) === 'BUY')
                                    <span class="badge" style="background:rgba(34,197,94,0.15);color:#22c55e;">BUY</span>
                                @else
                                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;">SELL</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $sc = match($trade->status) {
                                        'active'  => ['bg:rgba(212,175,55,0.15)', '#D4AF37', 'Active'],
                                        'tp_hit'  => ['rgba(59,130,246,0.15)', '#3b82f6', 'TP Hit'],
                                        'sl_hit'  => ['rgba(239,68,68,0.15)', '#ef4444', 'SL Hit'],
                                        default   => ['rgba(107,114,128,0.15)', '#6b7280', ucfirst($trade->status)],
                                    };
                                @endphp
                                <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ $sc[2] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-gray-600 py-6 text-xs">No signals yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="glass rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#D4AF37]/10 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-naira-sign text-[#D4AF37] text-xs"></i> Recent Payments
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments ?? [] as $payment)
                        <tr>
                            <td>
                                <div class="font-medium text-white text-xs">{{ $payment->user->name ?? 'N/A' }}</div>
                                <div class="text-gray-500 text-[10px] capitalize">{{ $payment->plan ?? '' }}</div>
                            </td>
                            <td>
                                <span class="text-green-400 font-bold text-xs">₦{{ number_format($payment->amount) }}</span>
                            </td>
                            <td class="text-gray-400 text-xs">{{ $payment->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-gray-600 py-6 text-xs">No payments yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
