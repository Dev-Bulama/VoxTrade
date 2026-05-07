<?php

namespace App\Console\Commands;

use App\Services\AITradeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSignalsCommand extends Command
{
    protected $signature   = 'signals:generate';
    protected $description = 'Run AI market analysis and generate trade signals for all watched pairs';

    public function handle(AITradeService $service): int
    {
        $this->info('[' . now()->format('H:i:s') . '] Starting signal generation...');
        Log::info('signals:generate command started.');

        $trades = $service->generateSignals();

        $count = count($trades);
        $this->info("[" . now()->format('H:i:s') . "] Done. {$count} new signal(s) created.");
        Log::info("signals:generate: {$count} signal(s) created.");

        return 0;
    }
}
