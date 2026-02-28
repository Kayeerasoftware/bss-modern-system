<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Financial\Transaction as FinancialTransaction;
use App\Observers\UserObserver;
use App\Observers\MemberObserver;
use App\Observers\GlobalAuditObserver;
use App\Observers\MemberFinancialLoanObserver;
use App\Observers\MemberFinancialTransactionObserver;
use App\Services\UserMemberSyncService;

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
        Loan::observe(MemberFinancialLoanObserver::class);
        Transaction::observe(MemberFinancialTransactionObserver::class);
        FinancialTransaction::observe(MemberFinancialTransactionObserver::class);

        // Auto-heal missing user/member links only when explicitly enabled.
        if ($this->app->isProduction()
            && !$this->app->runningInConsole()
            && filter_var(env('USER_MEMBER_AUTO_HEAL_ON_REQUEST', false), FILTER_VALIDATE_BOOL)) {
            app(UserMemberSyncService::class)->reconcileIfNeeded();
        }

        if (config('audit.model_events_enabled', false)) {
            // Capture model-level create/update/delete changes for full audit history.
            Event::listen('eloquent.created: *', function (string $eventName, array $data): void {
                $model = $data[0] ?? null;
                if ($model instanceof Model) {
                    GlobalAuditObserver::created($model);
                }
            });

            Event::listen('eloquent.updated: *', function (string $eventName, array $data): void {
                $model = $data[0] ?? null;
                if ($model instanceof Model) {
                    GlobalAuditObserver::updated($model);
                }
            });

            Event::listen('eloquent.deleted: *', function (string $eventName, array $data): void {
                $model = $data[0] ?? null;
                if ($model instanceof Model) {
                    GlobalAuditObserver::deleted($model);
                }
            });
        }
        
        // Share isCEO variable with all views
        view()->composer('*', function ($view) {
            $view->with('isCEO', auth()->check() && auth()->user()->role === 'ceo');
        });
    }
}
