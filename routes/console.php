<?php

use App\Jobs\AnalyzeMarketJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate new signals every minute (skips pairs with unexpired active signal)
Schedule::job(new AnalyzeMarketJob)->everyMinute();

// Validate active signals against live prices every 2 minutes (expire stale + detect TP/SL hits)
Schedule::command('signals:validate')->everyTwoMinutes();
