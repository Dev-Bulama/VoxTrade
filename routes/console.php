<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate new signals every minute — runs directly (no queue worker needed).
// withoutOverlapping() prevents a new run starting if the previous is still running.
Schedule::command('signals:generate')->everyMinute()->withoutOverlapping();

// Expire stale signals + detect TP/SL hits against live prices every 2 minutes.
Schedule::command('signals:validate')->everyTwoMinutes()->withoutOverlapping();
