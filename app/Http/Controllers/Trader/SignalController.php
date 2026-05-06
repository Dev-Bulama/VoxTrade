<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Services\AITradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    // Timeframe bucket ranges in minutes [min, max]
    private const TIMEFRAME_RANGES = [
        'scalp'    => [0,    30],
        'short'    => [31,   240],
        'intraday' => [241,  720],
        'day'      => [721,  1440],
        'swing'    => [1441, PHP_INT_MAX],
    ];

    public function index(Request $request)
    {
        // Priority sort: highest confidence first, then most recent
        $query     = Trade::orderByDesc('confidence')->orderByDesc('created_at');
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

        if ($timeframe !== 'all' && isset(self::TIMEFRAME_RANGES[$timeframe])) {
            [$minMins, $maxMins] = self::TIMEFRAME_RANGES[$timeframe];
            $allSignals = $query->get();
            $filtered   = $allSignals->filter(function ($trade) use ($minMins, $maxMins) {
                $mins = AITradeService::parseDurationMinutes($trade->duration ?? '');
                return $mins >= $minMins && $mins <= $maxMins;
            })->values();

            $perPage = 12;
            $page    = $request->input('page', 1);
            $slice   = $filtered->slice(($page - 1) * $perPage, $perPage);
            $signals = new \Illuminate\Pagination\LengthAwarePaginator(
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

    /**
     * Lightweight JSON endpoint for frontend polling.
     * Returns active signal IDs + their expiry timestamps so the JS can animate out expired cards.
     */
    public function liveJson(): JsonResponse
    {
        $active = Trade::where('status', 'active')
            ->orderByDesc('confidence')
            ->orderByDesc('created_at')
            ->get(['id', 'pair', 'type', 'confidence', 'duration', 'created_at']);

        $now     = now();
        $mapped  = $active->map(function ($t) {
            $durMins   = AITradeService::parseDurationMinutes($t->duration ?? '');
            $expiresAt = $t->created_at->addMinutes($durMins);
            return [
                'id'         => $t->id,
                'expires_at' => $expiresAt->timestamp,
                'expired'    => $expiresAt->isPast(),
            ];
        })->filter(fn($t) => !$t['expired'])->values();

        return response()->json([
            'active_ids'  => $mapped->pluck('id')->values(),
            'expires_map' => $mapped->pluck('expires_at', 'id'),
            'total'       => $mapped->count(),
            'checked_at'  => $now->timestamp,
        ]);
    }
}
