<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Trade;
use App\Models\User;
use App\Models\Payment;
use App\Models\Setting;
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

        // System health checks for admin visibility
        $health = [
            'openai_key'    => !empty(ApiKey::getApiKey('openai'))   || !empty(config('services.openai.key')),
            'paystack_key'  => !empty(ApiKey::getApiKey('paystack')) || !empty(config('services.paystack.secret_key')),
            'ai_sensitivity'=> (int) Setting::get('ai_sensitivity', 70),
            'active_signals'=> Trade::where('status', 'active')->count(),
            'last_signal_at'=> Trade::latest()->value('created_at'),
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTrades', 'recentPayments', 'health'));
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
