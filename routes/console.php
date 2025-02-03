<?php

use App\Console\Commands\ResetDiscounts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(ResetDiscounts::class)
    ->timezone('Asia/Yangon')
    ->dailyAt("00:00");
