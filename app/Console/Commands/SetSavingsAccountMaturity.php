<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetSavingsAccountMaturity extends Command
{
    protected $signature = 'savings:set-maturity {--account= : Savings account ID} {--member= : Member ID} {--date= : Maturity date (YYYY-MM-DD)} {--dry-run : Show what would change}';

    protected $description = 'Manually set maturity_date for savings accounts by account ID or member ID.';

    public function handle(): int
    {
        $accountId = $this->option('account');
        $memberId = $this->option('member');
        $date = $this->option('date');
        $dryRun = (bool) $this->option('dry-run');

        if (!$date) {
            $this->error('Missing --date (YYYY-MM-DD).');
            return self::FAILURE;
        }

        if (!$accountId && !$memberId) {
            $this->error('Provide either --account or --member.');
            return self::FAILURE;
        }

        $query = DB::table('savings_accounts');
        if ($accountId) {
            $query->where('id', $accountId);
        }
        if ($memberId) {
            $query->where('member_id', $memberId);
        }

        $count = (int) (clone $query)->count();
        if ($count === 0) {
            $this->info('No matching savings accounts found.');
            return self::SUCCESS;
        }

        $this->info('Accounts to update: ' . number_format($count));
        $this->line('Maturity date: ' . $date);

        if ($dryRun) {
            $this->line('Dry run enabled. No updates applied.');
            return self::SUCCESS;
        }

        $query->update(['maturity_date' => $date, 'updated_at' => now()]);
        $this->info('Maturity date updated.');

        return self::SUCCESS;
    }
}
