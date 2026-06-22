<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        \Illuminate\Support\Facades\Gate::define('manager', function ($user) {
            return $user->role === 'manager';
        });

        \Illuminate\Support\Facades\Gate::define('admin_or_manager', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
    }
}
