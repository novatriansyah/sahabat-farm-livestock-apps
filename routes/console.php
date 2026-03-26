<?php

use App\Actions\Finance\CalculateDailyHpp;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(CalculateDailyHpp::class)->execute();
})->dailyAt('00:00');

Schedule::command('animal:auto-status')->dailyAt('01:00');
Schedule::command('app:sync-notifications')->dailyAt('02:00');
Schedule::command('app:clean-old-records')->monthly();
