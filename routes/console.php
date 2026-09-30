<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('seo:indexnow')->dailyAt('02:45');
Schedule::command('backup:database')->dailyAt('03:00');
Schedule::command('orders:escalate-overdue')->hourly();
Schedule::command('notifications:send')->everyFiveMinutes();
Schedule::command('sitemap:generate')->dailyAt('03:30');
Schedule::command('webhooks:retry')->everyFiveMinutes();
Schedule::command('payments:reconcile')->hourly();
Schedule::command('carts:abandoned')->dailyAt('09:00');
Schedule::command('flash-deals:publish')->everyFifteenMinutes();
Schedule::command('seo:broken-links')->weeklyOn(1, '04:00');
