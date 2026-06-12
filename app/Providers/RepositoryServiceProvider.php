<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repositories to their implementations
        $this->app->bind(
            \App\Repositories\Interfaces\MemberRepositoryInterface::class,
            \App\Repositories\Eloquent\MemberRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\LoanRepositoryInterface::class,
            \App\Repositories\Eloquent\LoanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\TransactionRepositoryInterface::class,
            \App\Repositories\Eloquent\TransactionRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
