<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\Visit;
use App\Policies\PropertyPolicy;
use App\Policies\VisitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Visit::class => VisitPolicy::class,
        Property::class => PropertyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin = superuser
        Gate::before(function ($user, string $ability) {
            return $user->role === 'admin' ? true : null;
        });
    }
}
