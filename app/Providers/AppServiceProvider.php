<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        \App\Models\Visit::class => \App\Policies\VisitPolicy::class,
        \App\Models\Property::class => \App\Policies\PropertyPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->role === 'admin' ? true : null;
        });

        Schema::defaultStringLength(191);
    }
}
