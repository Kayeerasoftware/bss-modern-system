<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\Member\MemberCreated::class => [
            \App\Listeners\SendWelcomeEmail::class,
        ],
        \App\Events\Loan\LoanApproved::class => [
            \App\Listeners\SendLoanApprovalNotification::class,
        ],
        \App\Events\Financial\TransactionCreated::class => [
            \App\Listeners\LogTransaction::class,
            \App\Listeners\UpdateMemberBalance::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
