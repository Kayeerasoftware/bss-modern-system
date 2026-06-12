<?php

namespace App\Services\Financial;

use App\Models\Loan;
use App\Models\Member;
use App\Models\SavingsHistory;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MemberFinancialSyncService
{
    public function getMemberFinancialSummary(Member $member): array
    {
        $transactionTotals = $this->transactionTotals($member->id);
        $loanOutstanding = $this->loanOutstanding($member->id);

        $netSavings = $transactionTotals['total_deposits'] - $transactionTotals['total_withdrawals'];
        if (Schema::hasTable('savings_accounts')) {
            $accountBalance = (float) DB::table('savings_accounts')->where('member_id', $member->id)->sum('current_balance');
            $accountCount = (int) DB::table('savings_accounts')->where('member_id', $member->id)->count();
        } else {
            $accountBalance = 0.0;
            $accountCount = 0;
        }
        $availableBalance = $accountCount > 0 ? $accountBalance : $netSavings;

        $netSavings = max($netSavings, 0);
        $availableBalance = max($availableBalance, 0);

        return [
            'member_id' => $member->member_account_number ?? $member->member_number,
            'completed_transactions' => $transactionTotals['completed_transactions'],
            'total_deposits' => $transactionTotals['total_deposits'],
            'total_withdrawals' => $transactionTotals['total_withdrawals'],
            'total_transfers' => $transactionTotals['total_transfers'],
            'total_loan_payments' => $transactionTotals['total_loan_payments'],
            'net_savings' => round($netSavings, 2),
            'available_balance' => round($availableBalance, 2),
            'loan_outstanding' => round($loanOutstanding, 2),
            'available_after_loans' => round(max($availableBalance - $loanOutstanding, 0), 2),
            'stored_savings' => 0,
            'stored_savings_balance' => 0,
            'stored_balance' => 0,
            'is_synced' => true,
        ];
    }

    public function syncByMemberId(string $memberId, bool $force = false): ?array
    {
        $member = Member::query()
            ->where('member_account_number', $memberId)
            ->orWhere('member_number', $memberId)
            ->first();

        if (!$member) {
            return null;
        }

        return $this->syncMember($member, $force);
    }

    public function syncMember(Member $member, bool $force = false): array
    {
        $summary = $this->getMemberFinancialSummary($member);

        $syncedFields = [];
        $changed = false;

        $updates = [];
        if (Schema::hasColumn('members', 'savings_balance')) {
            $current = (float) (DB::table('members')->where('id', $member->id)->value('savings_balance') ?? 0);
            if ($force || !$this->nearlyEqual($current, (float) $summary['available_balance'])) {
                $updates['savings_balance'] = $summary['available_balance'];
                $syncedFields[] = 'savings_balance';
                $changed = true;
            }
        }

        if (Schema::hasColumn('members', 'savings')) {
            $current = (float) (DB::table('members')->where('id', $member->id)->value('savings') ?? 0);
            if ($force || !$this->nearlyEqual($current, (float) $summary['net_savings'])) {
                $updates['savings'] = $summary['net_savings'];
                $syncedFields[] = 'savings';
                $changed = true;
            }
        }

        if (Schema::hasColumn('members', 'savings_transaction_id')) {
            $current = DB::table('members')->where('id', $member->id)->value('savings_transaction_id');
            $latestSavingsTransactionId = SavingsHistory::query()
                ->forMember($member->id)
                ->latest('id')
                ->value('id');

            if ($force || (string) $current !== (string) $latestSavingsTransactionId) {
                $updates['savings_transaction_id'] = $latestSavingsTransactionId;
                $syncedFields[] = 'savings_transaction_id';
                $changed = true;
            }
        }

        if (!empty($updates)) {
            DB::table('members')->where('id', $member->id)->update($updates);
        }

        return array_merge($summary, [
            'changed' => $changed,
            'synced_fields' => $syncedFields,
            'stored_savings' => $updates['savings'] ?? ($summary['stored_savings'] ?? 0),
            'stored_savings_balance' => $updates['savings_balance'] ?? ($summary['stored_savings_balance'] ?? 0),
            'stored_balance' => $updates['savings_balance'] ?? ($summary['stored_balance'] ?? 0),
            'is_synced' => !$changed || !empty($updates),
        ]);
    }

    public function syncAll(bool $force = false): array
    {
        $report = [
            'processed' => 0,
            'changed' => 0,
        ];

        Member::query()
            ->orderBy('id')
            ->chunkById(200, function ($members) use (&$report, $force): void {
                foreach ($members as $member) {
                    $report['processed']++;
                    $result = $this->syncMember($member, $force);

                    if (!empty($result['changed'])) {
                        $report['changed']++;
                    }
                }
            });

        return $report;
    }

    private function transactionTotals(int $memberId): array
    {
        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';

        $totals = Transaction::query()
            ->where('transactions.member_id', $memberId)
            ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->leftJoin('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->where('ts.name', 'completed')
            ->selectRaw('COUNT(*) as completed_transactions')
            ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ('savings_deposit', 'transfer_in', 'loan_disbursement') THEN {$amountSql} ELSE 0 END), 0) as total_deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ('savings_withdrawal', 'transfer_out', 'fundraising_transfer') THEN {$amountSql} ELSE 0 END), 0) as total_withdrawals")
            ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ('transfer_out', 'fundraising_transfer') THEN {$amountSql} ELSE 0 END), 0) as total_transfers")
            ->selectRaw("COALESCE(SUM(CASE WHEN tc.name = 'loan_payment' OR transaction_types.name IN ('loan_repayment', 'loan_payment', 'repayment') THEN {$amountSql} ELSE 0 END), 0) as total_loan_payments")
            ->first();

        return [
            'completed_transactions' => (int) ($totals->completed_transactions ?? 0),
            'total_deposits' => (float) ($totals->total_deposits ?? 0),
            'total_withdrawals' => (float) ($totals->total_withdrawals ?? 0),
            'total_transfers' => (float) ($totals->total_transfers ?? 0),
            'total_loan_payments' => (float) ($totals->total_loan_payments ?? 0),
        ];
    }

    private function loanOutstanding(int $memberId): float
    {
        return (float) Loan::query()
            ->where('member_id', $memberId)
            ->sum('balance_due');
    }

    private function nearlyEqual(float $a, float $b): bool
    {
        return abs($a - $b) < 0.01;
    }
}
