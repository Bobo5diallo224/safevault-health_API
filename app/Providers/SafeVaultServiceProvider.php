<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use Illuminate\Support\ServiceProvider;

class SafeVaultServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthService::class);
    }

    public function boot(): void {}
}