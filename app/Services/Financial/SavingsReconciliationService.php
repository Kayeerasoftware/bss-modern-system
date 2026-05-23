<?php

namespace App\Services\Financial;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SavingsReconciliationService
{
    private function getMemberBalanceColumn(): ?string
    {
        if (Schema::hasColumn('members', 'savings_balance')) {
            return 'savings_balance';
        }
        if (Schema::hasColumn('members', 'savings')) {
            return 'savings';
        }
        return null;
    }

    private function getAmountSql(): string
    {
        return 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';
    }

    private function getCategoryNames(): array
    {
        return ['savings_deposit', 'savings_withdrawal', 'transfer_in', 'transfer_out'];
    }

    public function getCombinedBalancesSubquery()
    {
        $memberBalanceColumn = $this->getMemberBalanceColumn();
        $amountSql = $this->getAmountSql();
        $categoryNames = $this->getCategoryNames();

        $accountsBalancesSub = DB::table('savings_accounts')
            ->selectRaw('member_id, SUM(current_balance) as balance')
            ->groupBy('member_id');

        $membersBalancesSub = $memberBalanceColumn
            ? DB::table('members')->selectRaw("id as member_id, COALESCE({$memberBalanceColumn}, 0) as balance")
            : DB::table('members')->selectRaw('id as member_id, 0 as balance');

        $txnBalancesSub = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->groupBy('transactions.member_id');

        return DB::table('members')
            ->leftJoinSub($accountsBalancesSub, 'acc', 'members.id', '=', 'acc.member_id')
            ->leftJoinSub($membersBalancesSub, 'mem', 'members.id', '=', 'mem.member_id')
            ->leftJoinSub($txnBalancesSub, 'txn', 'members.id', '=', 'txn.member_id')
            ->selectRaw('members.id as member_id')
            ->selectRaw('acc.member_id as acc_member_id, txn.member_id as txn_member_id')
            ->selectRaw('COALESCE(acc.balance, 0) as account_balance')
            ->selectRaw('COALESCE(mem.balance, 0) as member_balance')
            ->selectRaw('COALESCE(txn.balance, 0) as txn_balance')
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN acc.balance
                    WHEN mem.balance IS NOT NULL AND mem.balance <> 0 THEN mem.balance
                    WHEN txn.member_id IS NOT NULL THEN txn.balance
                    WHEN mem.balance IS NOT NULL THEN mem.balance
                    ELSE 0
                END as reported_balance
            ")
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN acc.balance
                    WHEN mem.balance IS NOT NULL AND mem.balance <> 0 THEN mem.balance
                    WHEN txn.member_id IS NOT NULL THEN txn.balance
                    WHEN mem.balance IS NOT NULL THEN mem.balance
                    ELSE 0
                END as balance
            ")
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN 'account'
                    WHEN mem.balance IS NOT NULL AND mem.balance <> 0 THEN 'member'
                    WHEN txn.member_id IS NOT NULL THEN 'transaction'
                    WHEN mem.balance IS NOT NULL THEN 'member'
                    ELSE 'none'
                END as balance_source
            ");
    }

    public function getSystemSummary(float $tolerance = 0.01): array
    {
        $memberBalanceColumn = $this->getMemberBalanceColumn();
        $amountSql = $this->getAmountSql();
        $categoryNames = $this->getCategoryNames();

        $accountsBalancesSub = DB::table('savings_accounts')
            ->selectRaw('member_id, SUM(current_balance) as balance')
            ->groupBy('member_id');

        $membersBalancesSub = $memberBalanceColumn
            ? DB::table('members')->selectRaw("id as member_id, COALESCE({$memberBalanceColumn}, 0) as balance")
            : DB::table('members')->selectRaw('id as member_id, 0 as balance');

        $txnBalancesSub = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->groupBy('transactions.member_id');

        $combinedBalancesSub = $this->getCombinedBalancesSubquery();

        $totalAccounts = (float) DB::query()->fromSub($accountsBalancesSub, 'acc')->sum('balance');
        $totalMembers = $memberBalanceColumn ? (float) DB::table('members')->sum($memberBalanceColumn) : 0.0;
        $totalTransactions = (float) DB::query()->fromSub($txnBalancesSub, 'txn')->sum('balance');
        $totalReconciled = (float) DB::query()->fromSub($combinedBalancesSub, 'balances')->sum('reported_balance');
        $totalSavers = (int) DB::query()->fromSub($combinedBalancesSub, 'balances')->where('reported_balance', '>', 0)->count();

        $balanceSourceCounts = DB::query()->fromSub($combinedBalancesSub, 'balances')
            ->selectRaw("SUM(CASE WHEN balance_source = 'account' THEN 1 ELSE 0 END) as account")
            ->selectRaw("SUM(CASE WHEN balance_source = 'member' THEN 1 ELSE 0 END) as member")
            ->selectRaw("SUM(CASE WHEN balance_source = 'transaction' THEN 1 ELSE 0 END) as transaction")
            ->selectRaw("SUM(CASE WHEN reported_balance > 0 THEN 1 ELSE 0 END) as savers")
            ->first();

        $balanceSourceCounts = $balanceSourceCounts ?: (object) [
            'account' => 0,
            'member' => 0,
            'transaction' => 0,
            'savers' => 0,
        ];

        $reconciliationStats = DB::query()->fromSub($combinedBalancesSub, 'balances')
            ->selectRaw("SUM(CASE WHEN acc_member_id IS NOT NULL THEN 1 ELSE 0 END) as accounts_present")
            ->selectRaw("SUM(CASE WHEN member_balance > 0 THEN 1 ELSE 0 END) as member_balance_present")
            ->selectRaw("SUM(CASE WHEN txn_member_id IS NOT NULL THEN 1 ELSE 0 END) as txn_present")
            ->selectRaw("SUM(CASE WHEN acc_member_id IS NOT NULL AND member_balance > 0 AND ABS(account_balance - member_balance) > ? THEN 1 ELSE 0 END) as acc_mem_mismatches", [$tolerance])
            ->selectRaw("AVG(CASE WHEN acc_member_id IS NOT NULL AND member_balance > 0 THEN ABS(account_balance - member_balance) END) as acc_mem_avg_gap")
            ->selectRaw("MAX(CASE WHEN acc_member_id IS NOT NULL AND member_balance > 0 THEN ABS(account_balance - member_balance) END) as acc_mem_max_gap")
            ->selectRaw("SUM(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL AND ABS(account_balance - txn_balance) > ? THEN 1 ELSE 0 END) as acc_txn_mismatches", [$tolerance])
            ->selectRaw("AVG(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL THEN ABS(account_balance - txn_balance) END) as acc_txn_avg_gap")
            ->selectRaw("MAX(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL THEN ABS(account_balance - txn_balance) END) as acc_txn_max_gap")
            ->selectRaw("SUM(CASE WHEN member_balance > 0 AND txn_member_id IS NOT NULL AND ABS(member_balance - txn_balance) > ? THEN 1 ELSE 0 END) as mem_txn_mismatches", [$tolerance])
            ->selectRaw("AVG(CASE WHEN member_balance > 0 AND txn_member_id IS NOT NULL THEN ABS(member_balance - txn_balance) END) as mem_txn_avg_gap")
            ->selectRaw("MAX(CASE WHEN member_balance > 0 AND txn_member_id IS NOT NULL THEN ABS(member_balance - txn_balance) END) as mem_txn_max_gap")
            ->first();

        $reconciliationStats = $reconciliationStats ?: (object) [
            'accounts_present' => 0,
            'member_balance_present' => 0,
            'txn_present' => 0,
            'acc_mem_mismatches' => 0,
            'acc_mem_avg_gap' => 0,
            'acc_mem_max_gap' => 0,
            'acc_txn_mismatches' => 0,
            'acc_txn_avg_gap' => 0,
            'acc_txn_max_gap' => 0,
            'mem_txn_mismatches' => 0,
            'mem_txn_avg_gap' => 0,
            'mem_txn_max_gap' => 0,
        ];

        return [
            'totals' => [
                'accounts' => $totalAccounts,
                'members' => $totalMembers,
                'transactions' => $totalTransactions,
                'reconciled' => $totalReconciled,
                'savers' => $totalSavers,
            ],
            'balance_sources' => $balanceSourceCounts,
            'reconciliation' => $reconciliationStats,
        ];
    }

    public function getMemberSnapshot(int $memberId): array
    {
        $memberBalanceColumn = $this->getMemberBalanceColumn();
        $amountSql = $this->getAmountSql();
        $categoryNames = $this->getCategoryNames();

        $accountBalance = (float) DB::table('savings_accounts')
            ->where('member_id', $memberId)
            ->sum('current_balance');
        $accountCount = (int) DB::table('savings_accounts')->where('member_id', $memberId)->count();

        $memberBalance = 0.0;
        if ($memberBalanceColumn) {
            $memberBalance = (float) (DB::table('members')->where('id', $memberId)->value($memberBalanceColumn) ?? 0);
        }

        $txnBalance = (float) DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->where('transactions.member_id', $memberId)
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->value('balance') ?? 0.0;

        $txnExists = (bool) DB::table('transactions')->where('member_id', $memberId)->exists();

        if ($accountCount > 0) {
            $reported = $accountBalance;
            $source = 'account';
        } elseif ($memberBalance !== 0.0) {
            $reported = $memberBalance;
            $source = 'member';
        } elseif ($txnExists) {
            $reported = $txnBalance;
            $source = 'transaction';
        } elseif ($memberBalanceColumn) {
            $reported = $memberBalance;
            $source = 'member';
        } else {
            $reported = 0.0;
            $source = 'none';
        }

        $maxGap = max(
            abs($accountBalance - $memberBalance),
            abs($accountBalance - $txnBalance),
            abs($memberBalance - $txnBalance)
        );

        return [
            'account_balance' => $accountBalance,
            'member_balance' => $memberBalance,
            'transaction_balance' => $txnBalance,
            'reported_balance' => $reported,
            'balance_source' => $source,
            'max_gap' => $maxGap,
        ];
    }
}
