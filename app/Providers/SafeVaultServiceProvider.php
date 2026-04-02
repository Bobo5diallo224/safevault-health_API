<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\MedicalRecord\MedicalRecordService;
use App\Services\MedicalRecord\AccessGrantService;
use Illuminate\Support\ServiceProvider;

class SafeVaultServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(MedicalRecordService::class);
        $this->app->singleton(AccessGrantService::class);
    }

    public function boot(): void {}
}