<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentSignals = Trade::latest()->limit(10)->get();
        $activeSignals = Trade::where('status', 'active')->count();
        $totalSignals = Trade::count();
        $winRate = $this->calculateWinRate();
        $disclaimer = Setting::get('disclaimer', 'This platform provides AI-assisted trade insights. Not financial advice.');
        $subscription = $user->subscription;
        return view('trader.dashboard', compact('recentSignals', 'activeSignals', 'totalSignals', 'winRate', 'disclaimer', 'subscription'));
    }

    public function performance()
    {
        $totalSignals = Trade::count();
        $tpHit = Trade::where('status', 'tp_hit')->count();
        $slHit = Trade::where('status', 'sl_hit')->count();
        $active = Trade::where('status', 'active')->count();
        $expired = Trade::where('status', 'expired')->count();
        $winRate = $totalSignals > 0 ? round(($tpHit / max($totalSignals - $active, 1)) * 100, 1) : 0;
        $forexSignals = Trade::where('category', 'forex')->count();
        $cryptoSignals = Trade::where('category', 'crypto')->count();
        $recentTrades = Trade::latest()->limit(20)->get();
        return view('trader.performance', compact('totalSignals', 'tpHit', 'slHit', 'active', 'expired', 'winRate', 'forexSignals', 'cryptoSignals', 'recentTrades'));
    }

    private function calculateWinRate(): float
    {
        $total = Trade::whereIn('status', ['tp_hit', 'sl_hit'])->count();
        if ($total === 0) return 0;
        $wins = Trade::where('status', 'tp_hit')->count();
        return round(($wins / $total) * 100, 1);
    }
}
