<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;   // ← import Gate here
use App\Models\User;                    // ← import your User model

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Add this block:
        Gate::define('manage-users', function (User $user) {
            // Only role “admin” can manage roles/users/settings:
            return in_array(optional($user->role)->name, ['admin', 'hospital_admin']);
        });

        // Users with role 'registration_staff' must NOT view reports
        Gate::define('view-reports', function (User $user) {
            $roleName = optional($user->role)->name;
            if ($roleName === 'registration_staff') {
                return false;
            }
            return true;
        });
    }
}
