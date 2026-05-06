<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Services\AITradeService;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    // Timeframe ranges in minutes
    private const TIMEFRAME_RANGES = [
        'scalp'    => [0,    30],
        'short'    => [31,   240],
        'intraday' => [241,  720],
        'day'      => [721,  1440],
        'swing'    => [1441, PHP_INT_MAX],
    ];

    public function index(Request $request)
    {
        $query     = Trade::latest();
        $category  = $request->input('category', 'all');
        $type      = $request->input('type', 'all');
        $risk      = $request->input('risk', 'all');
        $status    = $request->input('status', 'active');
        $pair      = trim($request->input('pair', ''));
        $timeframe = $request->input('timeframe', 'all');

        if ($category  && $category  !== 'all') $query->where('category', $category);
        if ($type      && $type      !== 'all') $query->where('type', strtoupper($type));
        if ($risk      && $risk      !== 'all') $query->where('risk_level', $risk);
        if ($status    && $status    !== 'all') $query->where('status', $status);
        if ($pair !== '')                        $query->where('pair', 'like', '%' . $pair . '%');

        // Timeframe filter: filter by duration string after fetching, OR do it in PHP
        if ($timeframe !== 'all' && isset(self::TIMEFRAME_RANGES[$timeframe])) {
            [$minMins, $maxMins] = self::TIMEFRAME_RANGES[$timeframe];
            // Fetch all matching records (before paginate) and filter in PHP
            $allSignals = $query->get();
            $filtered   = $allSignals->filter(function ($trade) use ($minMins, $maxMins) {
                $mins = AITradeService::parseDurationMinutes($trade->duration ?? '');
                return $mins >= $minMins && $mins <= $maxMins;
            })->values();

            // Manual paginate
            $perPage  = 12;
            $page     = $request->input('page', 1);
            $slice    = $filtered->slice(($page - 1) * $perPage, $perPage);
            $signals  = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice, $filtered->count(), $perPage, $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $signals = $query->paginate(12)->withQueryString();
        }

        return view('trader.signals', compact('signals'));
    }

    public function show(Trade $trade)
    {
        return view('trader.signal-detail', ['signal' => $trade]);
    }
}
