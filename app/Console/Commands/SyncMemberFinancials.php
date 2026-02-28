<?php

namespace App\Console\Commands;

use App\Services\Financial\MemberFinancialSyncService;
use Illuminate\Console\Command;

class SyncMemberFinancials extends Command
{
    protected $signature = 'members:sync-financials {--force : Rebuild balances even when member has no completed transactions}';

    protected $description = 'Synchronize member financial snapshots (savings, savings_balance, balance, loan) from transactions and loans.';

    public function handle(MemberFinancialSyncService $service): int
    {
        $force = (bool) $this->option('force');
        $this->info('Synchronizing member financial snapshots...');

        $report = $service->syncAll($force);

        $this->line('Processed: ' . number_format((int) ($report['processed'] ?? 0)));
        $this->line('Updated: ' . number_format((int) ($report['changed'] ?? 0)));
        $this->info('Member financial synchronization complete.');

        return self::SUCCESS;
    }
}
