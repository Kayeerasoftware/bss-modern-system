<?php

namespace App\Services\Financial;

use App\Models\Loan;
use App\Models\Member;
use App\Models\MemberDividend;
use App\Models\SavingsAccount;
use App\Models\SavingsHistory;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\System\AccountNumberService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TransactionPostingService
{
    public function createOpeningSavingsTransaction(Member $member, float $amount, array $options = []): ?Transaction
    {
        $amount = round(max($amount, 0), 2);

        $completedStatusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $depositTypeId = TransactionType::query()->where('name', 'deposit')->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'savings_deposit')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id')
            ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id')
            ?? Currency::query()->value('id');

        if (!$completedStatusId || !$depositTypeId || !$categoryId) {
            return null;
        }

        $transaction = Transaction::create([
            'member_id' => $member->id,
            'transaction_type_id' => $depositTypeId,
            'category_id' => $categoryId,
            'status_id' => $completedStatusId,
            'amount' => $amount,
            'net_amount' => $amount,
            'fee' => 0,
            'tax_amount' => 0,
            'commission' => 0,
            'currency_id' => $currencyId,
            'payment_method_id' => $paymentMethodId,
            'balance_before' => 0,
            'balance_after' => $amount,
            'description' => $options['description'] ?? 'Opening savings balance',
            'notes' => $options['notes'] ?? 'Seeded automatically when the member was created.',
            'metadata' => array_merge([
                'opening_balance' => true,
            ], (array) ($options['metadata'] ?? [])),
            'transaction_date' => $options['transaction_date'] ?? now(),
            'value_date' => $options['value_date'] ?? now(),
            'processed_by' => $options['processed_by'] ?? auth()->id() ?? $member->user_id,
            'processed_at' => $options['processed_at'] ?? now(),
            'processed_ip' => $options['processed_ip'] ?? request()?->ip(),
        ]);

        $this->applyCategoryUpdates($transaction, [
            'category' => 'savings_deposit',
            'metadata' => $transaction->metadata ?? [],
        ]);

        return $transaction;
    }

    public function applyCategoryUpdates(Transaction $transaction, array $payload = []): void
    {
        if (!$this->shouldPost($transaction)) {
            return;
        }

        $this->updateCategoryBalance($transaction, $payload);

        $category = $this->resolveCategory($transaction, $payload);
        if (!$category) {
            return;
        }

        switch ($category) {
            case 'savings':
            case 'savings_deposit':
            case 'savings_withdrawal':
            case 'transfer_out':
            case 'transfer_in':
                $this->applySavings($transaction);
                if ($category === 'transfer_out') {
                    $this->ensureTransferIn($transaction, $payload);
                }
                break;
            case 'loan_repayment':
            case 'loan_principal':
            case 'loan_interest':
            case 'loan_penalty':
                $this->applyLoanRepayment($transaction, $payload);
                break;
            case 'loan_disbursement':
                $this->applySavings($transaction);
                $this->applyLoanDisbursement($transaction, $payload);
                break;
            case 'shares':
            case 'share_purchase':
                $this->applySharePurchase($transaction, $payload);
                break;
            case 'dividend':
            case 'share_dividend':
                $this->applyDividendPayment($transaction, $payload);
                break;
            default:
                // No dedicated table for this category.
                break;
        }

        $metadata = Arr::get($payload, 'metadata', []);
        if (!empty($metadata['affects_savings']) && !in_array($category, ['savings', 'savings_deposit', 'savings_withdrawal', 'transfer_out', 'transfer_in'], true)) {
            $this->applySavings($transaction);
        }
    }

    private function resolveCategory(Transaction $transaction, array $payload): ?string
    {
        $category = $payload['category'] ?? $transaction->category ?? null;
        if (!$category && !empty($payload['transaction_category_id'])) {
            $category = DB::table('transaction_categories')
                ->where('id', $payload['transaction_category_id'])
                ->value('name');
        }
        if (!$category && !empty($transaction->category_id)) {
            $category = DB::table('transaction_categories')
                ->where('id', $transaction->category_id)
                ->value('name');
        }

        $category = strtolower(trim((string) $category));
        return $category !== '' ? $category : null;
    }

    private function updateCategoryBalance(Transaction $transaction, array $payload): void
    {
        // Category balances are now derived from the transactions ledger via the
        // `member_category_balances` view. We keep transaction posting focused on
        // writing the canonical transaction row and let the database derive totals.
        return;
    }

    private function applyCategoryDelta(int $memberId, int $categoryId, float $amount, string $impact, int $transactionId): void
    {
        $amount = max($amount, 0);
        $isDebit = strtolower($impact) === 'debit';
        $delta = $isDebit ? -$amount : $amount;

        $existing = DB::table('member_category_balances')
            ->where('member_id', $memberId)
            ->where('category_id', $categoryId)
            ->first();

        if ($existing) {
            DB::table('member_category_balances')
                ->where('member_id', $memberId)
                ->where('category_id', $categoryId)
                ->update([
                    'balance' => (float) ($existing->balance ?? 0) + $delta,
                    'total_in' => (float) ($existing->total_in ?? 0) + ($isDebit ? 0 : $amount),
                    'total_out' => (float) ($existing->total_out ?? 0) + ($isDebit ? $amount : 0),
                    'last_transaction_id' => $transactionId,
                    'updated_at' => now(),
                ]);
            return;
        }

        DB::table('member_category_balances')->insert([
            'member_id' => $memberId,
            'category_id' => $categoryId,
            'balance' => $delta,
            'total_in' => $isDebit ? 0 : $amount,
            'total_out' => $isDebit ? $amount : 0,
            'last_transaction_id' => $transactionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function applySavings(Transaction $transaction): void
    {
        $member = $this->resolveMember($transaction);
        if (!$member) {
            return;
        }

        $account = $this->resolveSavingsAccount($member);
        if (!$account) {
            return;
        }

        $transaction->refresh();
        $impact = $this->resolveTransactionImpact($transaction);

        $netAmount = $this->netAmount($transaction);
        $delta = $impact === 'debit' ? -$netAmount : $netAmount;

        $account = SavingsAccount::query()
            ->whereKey($account->id)
            ->lockForUpdate()
            ->first();
        if (!$account) {
            return;
        }

        if (SavingsHistory::query()->where('transaction_id', $transaction->id)->exists()) {
            return;
        }

        $currentBalance = (float) ($account->current_balance ?? 0);
        $availableBalance = (float) ($account->available_balance ?? $currentBalance);
        $overdraftLimit = (float) ($account->overdraft_limit ?? 0);
        $overdraftUsed = (float) ($account->overdraft_used ?? 0);
        $remainingOverdraft = max($overdraftLimit - $overdraftUsed, 0);

        if ($impact === 'debit') {
            $availableFunds = $availableBalance + $remainingOverdraft;
            if ($netAmount > $availableFunds + 0.0001) {
                throw new \RuntimeException('Insufficient savings balance for this transaction.');
            }
        }

        $newBalance = $currentBalance + $delta;
        $newOverdraftUsed = max(-$newBalance, 0);

        $account->update([
            'current_balance' => $newBalance,
            'available_balance' => $newBalance,
            'overdraft_used' => $newOverdraftUsed,
        ]);

        SavingsHistory::create([
            'savings_account_id' => $account->id,
            'transaction_id' => $transaction->id,
            'amount' => $netAmount,
            'running_balance' => $newBalance,
            'transaction_type' => $this->resolveSavingsTransactionType($transaction),
            'notes' => $transaction->description,
        ]);

        if (Schema::hasColumn('members', 'savings_transaction_id')) {
            DB::table('members')
                ->where('id', $member->id)
                ->update(['savings_transaction_id' => SavingsHistory::query()->where('transaction_id', $transaction->id)->value('id')]);
        }
    }

    private function applyLoanRepayment(Transaction $transaction, array $payload): void
    {
        $member = $this->resolveMember($transaction);
        if (!$member) {
            return;
        }

        $metadata = Arr::get($payload, 'metadata', []);
        $loanIdentifier = Arr::get($metadata, 'loan_id');

        $loanQuery = Loan::query()->where('member_id', $member->id);
        if ($loanIdentifier) {
            if (is_numeric($loanIdentifier)) {
                $loanQuery->where('id', (int) $loanIdentifier);
            } else {
                $loanQuery->where('loan_number', $loanIdentifier);
            }
        }

        $loan = $loanQuery->orderByDesc('created_at')->first();
        if (!$loan) {
            return;
        }

        $appliedAmount = (float) (Arr::get($metadata, 'loan_applied_amount') ?? $transaction->amount ?? 0);
        if ($appliedAmount <= 0) {
            return;
        }

        $interestPortion = 0.0;
        $principalPortion = $appliedAmount;
        $totalAmount = (float) ($loan->total_amount ?? 0);
        $totalInterest = (float) ($loan->total_interest ?? 0);
        if ($totalAmount > 0 && $totalInterest > 0) {
            $interestPortion = round(($totalInterest / $totalAmount) * $appliedAmount, 2);
            $principalPortion = max($appliedAmount - $interestPortion, 0);
        }

        $repaymentNumber = $this->uniqueNumber('LRP', 'loan_repayments', 'repayment_number');
        DB::table('loan_repayments')->insert([
            'repayment_number' => $repaymentNumber,
            'loan_id' => $loan->id,
            'transaction_id' => $transaction->id,
            'amount' => $appliedAmount,
            'principal_applied' => $principalPortion,
            'interest_applied' => $interestPortion,
            'fee_applied' => 0,
            'penalty_applied' => 0,
            'payment_date' => $this->resolvePaymentDate($transaction),
            'receipt_number' => $transaction->receipt_number,
            'receipt_issued_by' => $transaction->processed_by,
            'receipt_issued_at' => now(),
            'notes' => $transaction->description,
            'created_at' => now(),
        ]);

        $newAmountPaid = (float) ($loan->amount_paid ?? 0) + $appliedAmount;
        $paymentsMade = (int) ($loan->payments_made ?? 0) + 1;
        $loanStatusCompletedId = DB::table('loan_statuses')->where('name', 'completed')->value('id');
        $isFullyPaid = (float) ($loan->total_amount ?? 0) > 0 && $newAmountPaid >= (float) $loan->total_amount;

        Loan::query()->whereKey($loan->id)->update([
            'amount_paid' => $newAmountPaid,
            'last_payment_date' => $this->resolvePaymentDate($transaction),
            'last_payment_amount' => $appliedAmount,
            'payments_made' => $paymentsMade,
            'completed_date' => $isFullyPaid ? $this->resolvePaymentDate($transaction) : $loan->completed_date,
            'status_id' => $isFullyPaid && $loanStatusCompletedId ? $loanStatusCompletedId : $loan->status_id,
        ]);

        $transaction->update([
            'related_loan_id' => $loan->id,
        ]);

        $excess = (float) (Arr::get($metadata, 'loan_excess_to_savings') ?? 0);
        if ($excess > 0) {
            $this->applyLoanExcessToSavings($transaction, $member, $excess);
        }
    }

    private function applyLoanDisbursement(Transaction $transaction, array $payload): void
    {
        $member = $this->resolveMember($transaction);
        if (!$member) {
            return;
        }

        $metadata = Arr::get($payload, 'metadata', []);
        $loanIdentifier = Arr::get($metadata, 'loan_id') ?? $transaction->related_loan_id;

        $loanQuery = Loan::query()->where('member_id', $member->id);
        if ($loanIdentifier) {
            if (is_numeric($loanIdentifier)) {
                $loanQuery->where('id', (int) $loanIdentifier);
            } else {
                $loanQuery->where('loan_number', $loanIdentifier);
            }
        }

        $loan = $loanQuery->orderByDesc('created_at')->first();
        if (!$loan) {
            return;
        }

        $disbursedStatusId = DB::table('loan_statuses')->where('name', 'disbursed')->value('id')
            ?? DB::table('loan_statuses')->where('name', 'approved')->value('id');
        $disbursementDate = $this->resolvePaymentDate($transaction);

        Loan::query()->whereKey($loan->id)->update([
            'disbursement_date' => $loan->disbursement_date ?? $disbursementDate,
            'first_payment_date' => $loan->first_payment_date ?? $disbursementDate,
            'disbursement_transaction_id' => $transaction->id,
            'disbursement_method_id' => $transaction->payment_method_id,
            'disbursed_by' => $transaction->processed_by,
            'disbursed_at' => $transaction->processed_at ?? now(),
            'status_id' => $disbursedStatusId ?? $loan->status_id,
        ]);
    }

    private function applySharePurchase(Transaction $transaction, array $payload): void
    {
        $member = $this->resolveMember($transaction);
        if (!$member) {
            return;
        }

        $shareClass = DB::table('share_classes')->where('is_active', 1)->orderBy('id')->first();
        if (!$shareClass) {
            $shareClass = DB::table('share_classes')->orderBy('id')->first();
        }
        if (!$shareClass) {
            return;
        }

        $statusId = DB::table('share_statuses')->where('name', 'active')->value('id')
            ?? DB::table('share_statuses')->value('id');
        if (!$statusId) {
            return;
        }

        $amount = (float) ($transaction->amount ?? 0);
        if ($amount <= 0) {
            return;
        }

        $parValue = (float) ($shareClass->par_value ?? 0);
        $sharesCount = 1;
        if ($parValue > 0 && $amount >= $parValue) {
            $sharesCount = (int) floor($amount / $parValue);
            $sharesCount = max($sharesCount, 1);
        }
        $pricePerShare = $amount / $sharesCount;

        $purchaseNumber = $this->uniqueNumber('SP', 'share_purchases', 'purchase_number');
        $certificateNumber = $this->uniqueNumber('CERT', 'share_purchases', 'certificate_number');

        $purchaseId = DB::table('share_purchases')->insertGetId([
            'purchase_number' => $purchaseNumber,
            'member_id' => $member->id,
            'share_issue_id' => null,
            'share_class_id' => $shareClass->id,
            'shares_count' => $sharesCount,
            'price_per_share' => $pricePerShare,
            'purchase_date' => $this->resolvePaymentDate($transaction),
            'transaction_id' => $transaction->id,
            'payment_method_id' => $transaction->payment_method_id,
            'is_fully_paid' => 1,
            'payment_plan' => null,
            'status_id' => $statusId,
            'certificate_number' => $certificateNumber,
            'certificate_issued_date' => $this->resolvePaymentDate($transaction),
            'certificate_issued_by' => $transaction->processed_by,
            'notes' => $transaction->description,
            'created_by' => $transaction->processed_by,
            'created_at' => now(),
        ]);

        $shareId = DB::table('shares')->insertGetId([
            'share_number' => $this->uniqueNumber('SHR', 'shares', 'share_number'),
            'certificate_number' => $certificateNumber,
            'member_id' => $member->id,
            'share_class_id' => $shareClass->id,
            'purchase_id' => $purchaseId,
            'shares_count' => $sharesCount,
            'purchase_price' => $pricePerShare,
            'current_value' => $pricePerShare,
            'purchase_date' => $this->resolvePaymentDate($transaction),
            'status_id' => $statusId,
            'notes' => $transaction->description,
            'created_at' => now(),
        ]);

        $transaction->update([
            'related_share_id' => $shareId,
        ]);
    }

    private function applyDividendPayment(Transaction $transaction, array $payload): void
    {
        $member = $this->resolveMember($transaction);
        if (!$member) {
            return;
        }

        $amount = (float) ($transaction->amount ?? 0);
        if ($amount <= 0) {
            return;
        }

        $pendingDividend = MemberDividend::query()
            ->where('member_id', $member->id)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->first();

        if (!$pendingDividend) {
            return;
        }

        $netAmount = (float) ($pendingDividend->net_amount ?? 0);
        if ($netAmount > 0 && $amount > $netAmount + 0.01) {
            return;
        }

        $pendingDividend->update([
            'transaction_id' => $transaction->id,
            'paid_at' => $transaction->processed_at ?? now(),
            'paid_by' => $transaction->processed_by,
            'status' => 'paid',
            'payment_method_id' => $transaction->payment_method_id,
            'notes' => $transaction->description,
        ]);

        $transaction->update([
            'related_dividend_id' => $pendingDividend->id,
        ]);
    }

    private function applyLoanExcessToSavings(Transaction $transaction, Member $member, float $amount): void
    {
        $account = $this->resolveSavingsAccount($member);
        if (!$account) {
            return;
        }

        $newBalance = (float) ($account->current_balance ?? 0) + $amount;
        $account->update([
            'current_balance' => $newBalance,
            'available_balance' => $newBalance,
        ]);

        SavingsHistory::create([
            'savings_account_id' => $account->id,
            'transaction_id' => $transaction->id,
            'amount' => $amount,
            'running_balance' => $newBalance,
            'transaction_type' => 'deposit',
            'notes' => 'Loan repayment excess applied to savings.',
        ]);

        if (Schema::hasColumn('members', 'savings_transaction_id')) {
            DB::table('members')
                ->where('id', $member->id)
                ->update(['savings_transaction_id' => SavingsHistory::query()->where('transaction_id', $transaction->id)->value('id')]);
        }
    }

    private function resolveMember(Transaction $transaction): ?Member
    {
        if ($transaction->relationLoaded('member')) {
            return $transaction->member;
        }

        return Member::query()->find($transaction->member_id);
    }

    private function resolveSavingsAccount(Member $member): ?SavingsAccount
    {
        $account = SavingsAccount::query()->where('member_id', $member->id)->orderBy('id')->first();
        if ($account) {
            return $account;
        }

        $planId = DB::table('savings_plans')->where('is_active', 1)->value('id')
            ?? DB::table('savings_plans')->value('id');
        if (!$planId) {
            $planTypeId = DB::table('savings_plan_types')->where('is_active', 1)->value('id')
                ?? DB::table('savings_plan_types')->value('id');
            if (!$planTypeId) {
                $planTypeId = DB::table('savings_plan_types')->insertGetId([
                    'name' => 'Standard Savings',
                    'description' => 'Default system savings plan type.',
                    'min_balance' => 0,
                    'interest_rate' => 0,
                    'interest_calculation' => 'monthly',
                    'withdrawal_fee_percentage' => 0,
                    'withdrawal_fee_fixed' => 0,
                    'is_taxable' => 0,
                    'tax_rate' => 0,
                    'is_active' => 1,
                    'created_at' => now(),
                ]);
            }

            $planId = DB::table('savings_plans')->insertGetId([
                'plan_type_id' => $planTypeId,
                'name' => 'Default Savings',
                'description' => 'Auto-created savings plan.',
                'minimum_balance' => 0,
                'interest_rate' => 0,
                'interest_calculation' => 'monthly',
                'interest_payout' => 'compound',
                'monthly_fee' => 0,
                'withdrawal_fee_percentage' => 0,
                'withdrawal_fee_fixed' => 0,
                'early_withdrawal_penalty' => 0,
                'min_deposit' => 0,
                'max_deposit' => null,
                'min_withdrawal' => null,
                'max_withdrawal' => null,
                'withdrawal_limit_period' => null,
                'withdrawal_limit_count' => null,
                'min_duration_months' => null,
                'max_duration_months' => null,
                'is_taxable' => 0,
                'tax_rate' => 0,
                'allows_overdraft' => 0,
                'overdraft_limit' => null,
                'overdraft_interest_rate' => null,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }
        if (!$planId) {
            return null;
        }

        $accountNumber = AccountNumberService::generateSavingsAccountNumber();
        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';
        $startingBalance = (float) DB::table('transactions')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->where('transactions.member_id', $member->id)
            ->whereIn('tc.name', ['savings_deposit', 'savings_withdrawal', 'transfer_out', 'transfer_in', 'fundraising_transfer', 'loan_disbursement'])
            ->selectRaw("COALESCE(SUM(CASE WHEN tt.impact = 'debit' THEN -{$amountSql} ELSE {$amountSql} END), 0) as balance")
            ->value('balance');

        return SavingsAccount::create([
            'account_number' => $accountNumber,
            'member_id' => $member->id,
            'plan_id' => $planId,
            'account_name' => $member->full_name . ' Savings',
            'opening_balance' => $startingBalance,
            'current_balance' => $startingBalance,
            'available_balance' => $startingBalance,
            'opening_date' => now()->toDateString(),
        ]);
    }

    private function resolveTransactionImpact(Transaction $transaction): ?string
    {
        $typeId = $transaction->transaction_type_id;
        if (!$typeId) {
            return null;
        }

        return TransactionType::query()->whereKey($typeId)->value('impact');
    }

    private function resolveSavingsTransactionType(Transaction $transaction): string
    {
        $categoryName = $transaction->category;
        if ($categoryName && str_starts_with($categoryName, 'transfer_')) {
            return 'transfer';
        }

        $typeName = TransactionType::query()->whereKey($transaction->transaction_type_id)->value('name');
        $typeName = strtolower((string) $typeName);

        if (in_array($typeName, ['deposit', 'withdrawal', 'transfer'], true)) {
            return $typeName;
        }

        return 'deposit';
    }

    private function resolvePaymentDate(Transaction $transaction): string
    {
        if ($transaction->transaction_date) {
            return $transaction->transaction_date->toDateString();
        }

        return now()->toDateString();
    }

    private function netAmount(Transaction $transaction): float
    {
        $amount = (float) ($transaction->amount ?? 0);
        $net = $transaction->net_amount;
        if ($net === null) {
            $net = $amount - (float) ($transaction->fee ?? 0) - (float) ($transaction->tax_amount ?? 0) - (float) ($transaction->commission ?? 0);
        }
        return max((float) $net, 0.0);
    }

    private function shouldPost(Transaction $transaction): bool
    {
        if (!empty($transaction->is_reversal) || !empty($transaction->reversed_at)) {
            return false;
        }

        if ($transaction->relationLoaded('statusRelation')) {
            return ($transaction->statusRelation?->name ?? '') === 'completed';
        }

        $statusId = $transaction->status_id;
        if (!$statusId) {
            return false;
        }

        $statusName = DB::table('transaction_statuses')->where('id', $statusId)->value('name');
        return $statusName === 'completed';
    }

    private function ensureTransferIn(Transaction $transaction, array $payload): void
    {
        $metadata = Arr::get($payload, 'metadata', []);
        if (!empty($metadata['suppress_transfer_in'])) {
            return;
        }

        $toMemberId = $metadata['transfer_to_member_id'] ?? null;
        if (!$toMemberId) {
            return;
        }

        $toMember = Member::query()->find($toMemberId);
        if (!$toMember || (int) $toMember->id === (int) $transaction->member_id) {
            return;
        }

        $transferInCategoryId = DB::table('transaction_categories')->where('name', 'transfer_in')->value('id');
        $depositTypeId = DB::table('transaction_types')->where('name', 'deposit')->value('id');
        $completedStatusId = DB::table('transaction_statuses')->where('name', 'completed')->value('id');

        if (!$transferInCategoryId || !$depositTypeId || !$completedStatusId) {
            return;
        }

        $netAmount = $this->netAmount($transaction);
        if ($netAmount <= 0) {
            return;
        }

        $balanceBefore = (float) ($toMember->balance ?? 0);
        $balanceAfter = $balanceBefore + $netAmount;

        $transferIn = Transaction::create([
            'member_id' => $toMember->id,
            'transaction_type_id' => $depositTypeId,
            'category_id' => $transferInCategoryId,
            'status_id' => $completedStatusId,
            'amount' => $netAmount,
            'net_amount' => $netAmount,
            'fee' => 0,
            'tax_amount' => 0,
            'commission' => 0,
            'currency_id' => $transaction->currency_id,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method_id' => $transaction->payment_method_id,
            'reference_number' => $transaction->reference_number,
            'receipt_number' => $transaction->receipt_number,
            'channel' => $transaction->channel,
            'description' => $transaction->description
                ? 'Transfer from ' . ($transaction->member?->full_name ?? 'member') . ': ' . $transaction->description
                : 'Transfer from ' . ($transaction->member?->full_name ?? 'member'),
            'notes' => $transaction->notes,
            'metadata' => [
                'transfer_from_member_id' => $transaction->member_id,
                'source_transaction_id' => $transaction->id,
                'suppress_transfer_in' => true,
            ],
            'processed_by' => $transaction->processed_by,
            'processed_at' => $transaction->processed_at ?? now(),
            'processed_ip' => $transaction->processed_ip,
            'processed_location' => $transaction->processed_location,
            'transaction_date' => $transaction->transaction_date ?? now(),
            'value_date' => $transaction->value_date ?? $transaction->transaction_date ?? now(),
            'parent_transaction_id' => $transaction->id,
        ]);

        $this->applyCategoryUpdates($transferIn, ['metadata' => ['suppress_transfer_in' => true]]);
    }

    private function uniqueNumber(string $prefix, string $table, string $column): string
    {
        $date = now()->format('Ymd');
        do {
            $suffix = strtoupper(Str::random(6));
            $value = $prefix . '-' . $date . '-' . $suffix;
            $exists = DB::table($table)->where($column, $value)->exists();
        } while ($exists);

        return $value;
    }
}
