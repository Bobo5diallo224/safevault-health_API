<?php

namespace App\Providers;

use App\Models\MedicalRecord;
use App\Models\AccessGrant;
use App\Policies\MedicalRecordPolicy;
use App\Policies\AccessGrantPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
        Gate::policy(AccessGrant::class, AccessGrantPolicy::class);
    }
}