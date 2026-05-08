<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directives for permission checks
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if(auth()->check() && (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can($permission) && \App\Models\CustomPermission::where('name', $permission)->where('status', 1)->exists()))): ?>";
        });

        Blade::directive('endHasPermission', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hasAnyPermission', function ($permissions) {
            return "<?php if(auth()->check() && (auth()->user()->hasRole('SuperAdmin') || auth()->user()->hasAnyPermission($permissions))): ?>";
        });

        Blade::directive('endHasAnyPermission', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hasRole', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole($role)): ?>";
        });

        Blade::directive('endHasRole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hasAnyRole', function ($roles) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole($roles)): ?>";
        });

        Blade::directive('endHasAnyRole', function () {
            return "<?php endif; ?>";
        });
    }
}
