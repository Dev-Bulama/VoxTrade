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
