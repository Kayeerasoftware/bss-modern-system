<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\System\AccountNumberService;

class BackfillSavingsAccounts extends Command
{
    protected $signature = 'savings:backfill-accounts {--source=auto : auto|members|transactions} {--dry-run : Show counts without writing}';

    protected $description = 'Create savings_accounts for members missing them using member balances or completed savings transactions.';

    public function handle(): int
    {
        $source = strtolower((string) $this->option('source'));
        $dryRun = (bool) $this->option('dry-run');

        $memberBalanceColumn = $this->resolveMemberBalanceColumn();
        $useMemberBalances = in_array($source, ['auto', 'members'], true) && $memberBalanceColumn;
        if ($source === 'transactions') {
            $useMemberBalances = false;
        }

        $balancesSub = $useMemberBalances
            ? $this->memberBalancesSub($memberBalanceColumn)
            : $this->transactionBalancesSub();

        $planId = $this->resolvePlanId();
        if (!$planId) {
            $this->error('Unable to resolve or create a savings plan.');
            return self::FAILURE;
        }

        $missingQuery = DB::table('members')
            ->leftJoin('savings_accounts', 'members.id', '=', 'savings_accounts.member_id')
            ->leftJoinSub($balancesSub, 'balances', 'members.id', '=', 'balances.member_id')
            ->whereNull('savings_accounts.id')
            ->select(
                'members.id',
                'members.member_number',
                'members.join_date',
                DB::raw('COALESCE(balances.balance, 0) as balance')
            );

        $missingCount = (int) (clone $missingQuery)->count();
        $this->info('Members missing savings accounts: ' . number_format($missingCount));

        if ($missingCount === 0) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->line('Dry run enabled. No accounts created.');
            return self::SUCCESS;
        }

        $inserted = 0;
        $now = now();

        $missingQuery->orderBy('members.id')->chunkById(200, function ($rows) use (&$inserted, $planId, $now): void {
            foreach ($rows as $row) {
                $balance = (float) ($row->balance ?? 0);
                $accountNumber = $this->buildAccountNumber();
                $status = $balance > 0 ? 'active' : 'dormant';
                $openingDate = $row->join_date ?? $now->toDateString();

                DB::table('savings_accounts')->insert([
                    'account_number' => $accountNumber,
                    'member_id' => $row->id,
                    'plan_id' => $planId,
                    'account_name' => 'Member Savings',
                    'opening_balance' => $balance,
                    'current_balance' => $balance,
                    'available_balance' => $balance,
                    'opening_date' => $openingDate,
                    'status' => $status,
                    'created_at' => $now,
                ]);
                $inserted++;
            }
        });

        $this->info('Savings accounts created: ' . number_format($inserted));
        return self::SUCCESS;
    }

    private function resolveMemberBalanceColumn(): ?string
    {
        if (Schema::hasColumn('members', 'savings_balance')) {
            return 'savings_balance';
        }
        if (Schema::hasColumn('members', 'savings')) {
            return 'savings';
        }
        return null;
    }

    private function memberBalancesSub(string $column)
    {
        return DB::table('members')
            ->selectRaw("members.id as member_id, COALESCE(members.{$column}, 0) as balance");
    }

    private function transactionBalancesSub()
    {
        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';

        return DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->where('ts.name', 'completed')
            ->where('tt.affects_savings', 1)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->groupBy('transactions.member_id');
    }

    private function resolvePlanId(): ?int
    {
        $planId = DB::table('savings_plans')->where('is_active', 1)->value('id');
        if ($planId) {
            return (int) $planId;
        }

        $planId = DB::table('savings_plans')->value('id');
        if ($planId) {
            return (int) $planId;
        }

        $planTypeId = DB::table('savings_plan_types')->where('is_active', 1)->value('id')
            ?? DB::table('savings_plan_types')->value('id');

        if (!$planTypeId) {
            $planTypeId = DB::table('savings_plan_types')->insertGetId([
                'name' => 'Default Savings',
                'description' => 'Auto-created savings plan type.',
                'min_balance' => 0,
                'interest_rate' => 0,
                'interest_calculation' => 'monthly',
                'withdrawal_fee_percentage' => 0,
                'withdrawal_fee_fixed' => 0,
                'is_taxable' => 0,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }

        return (int) DB::table('savings_plans')->insertGetId([
            'plan_type_id' => $planTypeId,
            'name' => 'Default Savings Plan',
            'description' => 'Auto-created savings plan.',
            'minimum_balance' => 0,
            'interest_rate' => 0,
            'interest_calculation' => 'monthly',
            'interest_payout' => 'compound',
            'monthly_fee' => 0,
            'withdrawal_fee_percentage' => 0,
            'withdrawal_fee_fixed' => 0,
            'early_withdrawal_penalty' => 0,
            'is_taxable' => 0,
            'tax_rate' => 0,
            'allows_overdraft' => 0,
            'is_active' => 1,
            'created_at' => now(),
        ]);
    }

    private function buildAccountNumber(): string
    {
        return AccountNumberService::generateSavingsAccountNumber();
    }
}
