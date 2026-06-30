<?php

namespace App\Providers;

use App\Models\Availability;
use App\Models\User;
use App\Policies\AvailabilityPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Availability::class, AvailabilityPolicy::class);
    }
}
