<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Gate::define('viewPulse', function (User $user) {
        //     return $user->isAdmin();
        // });

        if($this->app->environment('prod') || $this->app->environment('dev')) {
            URL::forceScheme('https');
        };
    }
}
