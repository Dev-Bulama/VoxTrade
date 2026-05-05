<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::where('role', 'trader')->count(),
            'active_subscribers' => Subscription::where('status', 'active')->where('expires_at', '>', now())->count(),
            'total_signals'      => Trade::count(),
            'active_signals'     => Trade::where('status', 'active')->count(),
            'total_revenue'      => Payment::where('status', 'successful')->sum('amount'),
            'win_rate'           => $this->getWinRate(),
        ];
        $recentUsers    = User::where('role', 'trader')->latest()->limit(5)->get();
        $recentTrades   = Trade::latest()->limit(5)->get();
        $recentPayments = Payment::with('user')->latest()->limit(5)->get();
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTrades', 'recentPayments'));
    }

    public function guide()
    {
        return view('admin.guide');
    }

    private function getWinRate(): float
    {
        $total = Trade::whereIn('status', ['tp_hit', 'sl_hit'])->count();
        if ($total === 0) return 0;
        return round((Trade::where('status', 'tp_hit')->count() / $total) * 100, 1);
    }
}
