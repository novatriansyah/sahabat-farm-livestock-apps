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

Schedule::command('animals:update-status')->dailyAt('01:00');
