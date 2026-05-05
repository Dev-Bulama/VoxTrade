<?php

use App\Jobs\AnalyzeMarketJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run every minute; job skips pairs that still have unexpired active signals
Schedule::job(new AnalyzeMarketJob)->everyMinute();
