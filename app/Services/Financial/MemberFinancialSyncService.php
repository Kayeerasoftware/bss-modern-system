<?php

namespace App\Services\Financial;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Transaction;

class MemberFinancialSyncService
{
    public function getMemberFinancialSummary(Member $member): array
    {
        $transactionTotals = $this->transactionTotals($member->member_id);
        $loanOutstanding = $this->loanOutstanding($member->member_id);

        $netSavings = $transactionTotals['total_deposits'] - $transactionTotals['total_withdrawals'];
        $availableBalance = $netSavings - $transactionTotals['total_transfers'] - $transactionTotals['total_loan_payments'];

        $netSavings = max($netSavings, 0);
        $availableBalance = max($availableBalance, 0);

        $storedSavings = (float) ($member->savings ?? 0);
        $storedSavingsBalance = (float) ($member->savings_balance ?? 0);
        $storedBalance = (float) ($member->balance ?? 0);

        return [
            'member_id' => $member->member_id,
            'completed_transactions' => $transactionTotals['completed_transactions'],
            'total_deposits' => $transactionTotals['total_deposits'],
            'total_withdrawals' => $transactionTotals['total_withdrawals'],
            'total_transfers' => $transactionTotals['total_transfers'],
            'total_loan_payments' => $transactionTotals['total_loan_payments'],
            'net_savings' => round($netSavings, 2),
            'available_balance' => round($availableBalance, 2),
            'loan_outstanding' => round($loanOutstanding, 2),
            'available_after_loans' => round(max($availableBalance - $loanOutstanding, 0), 2),
            'stored_savings' => round($storedSavings, 2),
            'stored_savings_balance' => round($storedSavingsBalance, 2),
            'stored_balance' => round($storedBalance, 2),
            'is_synced' => $this->nearlyEqual($storedSavings, $netSavings)
                && $this->nearlyEqual($storedSavingsBalance, $netSavings)
                && $this->nearlyEqual($storedBalance, $availableBalance),
        ];
    }

    public function syncByMemberId(string $memberId, bool $force = false): ?array
    {
        $member = Member::query()->where('member_id', $memberId)->first();

        if (!$member) {
            return null;
        }

        return $this->syncMember($member, $force);
    }

    public function syncMember(Member $member, bool $force = false): array
    {
        $summary = $this->getMemberFinancialSummary($member);

        $updates = [
            'loan' => $summary['loan_outstanding'],
        ];

        if ($force || $summary['completed_transactions'] > 0) {
            $updates['savings'] = $summary['net_savings'];
            $updates['savings_balance'] = $summary['net_savings'];
            $updates['balance'] = $summary['available_balance'];
        }

        $changed = false;
        foreach ($updates as $column => $value) {
            if ($this->nearlyEqual((float) ($member->{$column} ?? 0), (float) $value)) {
                continue;
            }

            $member->{$column} = $value;
            $changed = true;
        }

        if ($changed) {
            $member->saveQuietly();
        }

        return array_merge($summary, [
            'changed' => $changed,
            'synced_fields' => array_keys($updates),
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

    private function transactionTotals(string $memberId): array
    {
        $amountSql = 'COALESCE(NULLIF(net_amount, 0), amount, 0)';

        $totals = Transaction::query()
            ->where('member_id', $memberId)
            ->where(function ($query): void {
                $query->where('status', 'completed')
                    ->orWhereNull('status');
            })
            ->selectRaw('COUNT(*) as completed_transactions')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN {$amountSql} ELSE 0 END), 0) as total_deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN {$amountSql} ELSE 0 END), 0) as total_withdrawals")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'transfer' THEN {$amountSql} ELSE 0 END), 0) as total_transfers")
            ->selectRaw("COALESCE(SUM(CASE WHEN type IN ('loan_payment', 'repayment') THEN {$amountSql} ELSE 0 END), 0) as total_loan_payments")
            ->first();

        return [
            'completed_transactions' => (int) ($totals->completed_transactions ?? 0),
            'total_deposits' => (float) ($totals->total_deposits ?? 0),
            'total_withdrawals' => (float) ($totals->total_withdrawals ?? 0),
            'total_transfers' => (float) ($totals->total_transfers ?? 0),
            'total_loan_payments' => (float) ($totals->total_loan_payments ?? 0),
        ];
    }

    private function loanOutstanding(string $memberId): float
    {
        $query = Loan::query()
            ->where('member_id', $memberId)
            ->whereIn('status', ['approved', 'active', 'disbursed', 'completed', 'paid']);

        if (Loan::hasPaymentTrackingColumn()) {
            $paidColumn = Loan::paymentTrackingColumn();

            $outstanding = $query
                ->selectRaw("COALESCE(SUM(GREATEST(COALESCE(amount, 0) - COALESCE({$paidColumn}, 0), 0)), 0) as outstanding")
                ->value('outstanding');

            return (float) ($outstanding ?? 0);
        }

        return (float) ($query->sum('amount') ?? 0);
    }

    private function nearlyEqual(float $a, float $b): bool
    {
        return abs($a - $b) < 0.01;
    }
}
