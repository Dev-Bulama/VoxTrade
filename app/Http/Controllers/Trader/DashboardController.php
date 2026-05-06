<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\Setting;
use App\Services\AITradeService;

class DashboardController extends Controller
{
    public function index()
    {
        // Expire stale signals before querying — ensures page load never shows expired cards
        app(AITradeService::class)->expireStaleSignals();

        $user          = auth()->user();
        $activeSignals = Trade::where('status', 'active')
            ->orderByDesc('confidence')
            ->orderByDesc('created_at')
            ->get()
            // Belt-and-suspenders: filter out any that slipped through DB cleanup
            ->filter(function ($s) {
                $mins = AITradeService::parseDurationMinutes($s->duration ?? '');
                return $s->created_at->addMinutes($mins)->isFuture();
            });
        $activeCount   = $activeSignals->count();
        $totalSignals  = Trade::count();
        $winRate       = $this->calculateWinRate();
        $disclaimer    = Setting::get('disclaimer', 'AI-assisted insights only. Not financial advice. Trading involves risk.');
        $subscription  = $user->subscription;
        $watchedPairs  = AITradeService::watchedPairs();
        $lastSignal    = Trade::latest()->first();

        return view('trader.dashboard', compact(
            'activeSignals', 'activeCount', 'totalSignals',
            'winRate', 'disclaimer', 'subscription',
            'watchedPairs', 'lastSignal'
        ));
    }

    public function performance()
    {
        $totalSignals  = Trade::count();
        $tpHit         = Trade::where('status', 'tp_hit')->count();
        $slHit         = Trade::where('status', 'sl_hit')->count();
        $active        = Trade::where('status', 'active')->count();
        $expired       = Trade::where('status', 'expired')->count();
        $closed        = $tpHit + $slHit;
        $winRate       = $closed > 0 ? round(($tpHit / $closed) * 100, 1) : 0;
        $forexSignals  = Trade::where('category', 'forex')->count();
        $cryptoSignals = Trade::where('category', 'crypto')->count();
        $recentTrades  = Trade::latest()->limit(20)->get();

        return view('trader.performance', compact(
            'totalSignals', 'tpHit', 'slHit', 'active', 'expired',
            'winRate', 'forexSignals', 'cryptoSignals', 'recentTrades'
        ));
    }

    public function howItWorks()
    {
        $watchedPairs = AITradeService::watchedPairs();
        return view('trader.how-it-works', compact('watchedPairs'));
    }

    private function calculateWinRate(): float
    {
        $total = Trade::whereIn('status', ['tp_hit', 'sl_hit'])->count();
        if ($total === 0) return 0;
        $wins = Trade::where('status', 'tp_hit')->count();
        return round(($wins / $total) * 100, 1);
    }
}
