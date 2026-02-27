<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Member;
use App\Observers\UserObserver;
use App\Observers\MemberObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repository bindings
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);
        
        // Prevent lazy loading in development
        Model::preventLazyLoading(!$this->app->isProduction());
        
        // Set timezone
        date_default_timezone_set(config('app.timezone', 'Africa/Kampala'));

        // Ensure generated URLs/forms use HTTPS in production behind proxies.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
        
        // Register observers for user-member synchronization
        User::observe(UserObserver::class);
        Member::observe(MemberObserver::class);
        
        // Share isCEO variable with all views
        view()->composer('*', function ($view) {
            $view->with('isCEO', auth()->check() && auth()->user()->role === 'ceo');
        });
    }
}
