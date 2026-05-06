<?php

namespace App\Console\Commands;

use App\Services\AITradeService;
use Illuminate\Console\Command;

class ValidateSignalsCommand extends Command
{
    protected $signature   = 'signals:validate';
    protected $description = 'Expire stale signals and detect SL/TP hits against live market prices';

    public function handle(AITradeService $service): int
    {
        $service->validateAndCloseSignals();
        return 0;
    }
}
