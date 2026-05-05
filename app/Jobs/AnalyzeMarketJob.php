<?php

namespace App\Jobs;

use App\Models\Trade;
use App\Services\AITradeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeMarketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Timeout in seconds.
     */
    public int $timeout = 120;

    /**
     * Execute the job.
     */
    public function handle(AITradeService $service): void
    {
        Log::info('AnalyzeMarketJob: starting market analysis.');

        // Generate new signals for all configured pairs
        $trades = $service->generateSignals();

        Log::info('AnalyzeMarketJob: signal generation complete.', [
            'signals_created' => count($trades),
            'trade_ids'       => array_map(fn($t) => $t->id, $trades),
        ]);

        // Expire active trades older than 24 hours
        $expired = Trade::where('status', 'active')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'expired']);

        Log::info("AnalyzeMarketJob: expired {$expired} stale trade(s) older than 24 hours.");
    }
}
