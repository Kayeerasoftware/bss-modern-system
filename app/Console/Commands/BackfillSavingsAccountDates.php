<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSavingsAccountDates extends Command
{
    protected $signature = 'savings:backfill-account-dates {--dry-run : Show counts without writing}';

    protected $description = 'Backfill opening_date and updated_at for savings_accounts using member join_date or created_at.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $missingOpening = (int) DB::table('savings_accounts')
            ->whereNull('opening_date')
            ->count();
        $missingUpdated = (int) DB::table('savings_accounts')
            ->whereNull('updated_at')
            ->count();

        $this->info('Missing opening_date: ' . number_format($missingOpening));
        $this->info('Missing updated_at: ' . number_format($missingUpdated));

        if ($dryRun) {
            $this->line('Dry run enabled. No updates applied.');
            return self::SUCCESS;
        }

        if ($missingOpening > 0) {
            DB::table('savings_accounts as sa')
                ->join('members as m', 'm.id', '=', 'sa.member_id')
                ->whereNull('sa.opening_date')
                ->update([
                    'opening_date' => DB::raw("COALESCE(m.join_date, DATE(sa.created_at))"),
                ]);
        }

        if ($missingUpdated > 0) {
            DB::table('savings_accounts')
                ->whereNull('updated_at')
                ->update([
                    'updated_at' => DB::raw('created_at'),
                ]);
        }

        $this->info('Backfill complete.');
        return self::SUCCESS;
    }
}
