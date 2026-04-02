<?php

use App\Jobs\CleanExpiredAccessGrants;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::job(new CleanExpiredAccessGrants)
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::critical(
            '[Scheduler] CleanExpiredAccessGrants a échoué.'
        );
    })
    ->name('clean-expired-grants');