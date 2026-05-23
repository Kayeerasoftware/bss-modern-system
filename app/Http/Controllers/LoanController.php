<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('member')->orderBy('created_at', 'desc')->get();
        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $repaymentMonths = (int) ($request->repayment_months ?? $request->duration_months ?? 12);
        $interestRate = (float) ($request->interest_rate ?? 10);
        $amount = (float) ($request->amount ?? 0);
        $interest = $amount * ($interestRate / 100) * ($repaymentMonths / 12);

        $memberId = resolve_member_id($request->member_id);
        if (!$memberId) {
            return response()->json(['message' => 'Invalid member'], 422);
        }

        $loanTypeId = LoanType::query()->where('is_active', 1)->value('id') ?? LoanType::query()->value('id');
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id')
            ?? LoanStatus::query()->value('id');

        $loan = null;
        DB::transaction(function () use (
            $memberId,
            $loanTypeId,
            $amount,
            $repaymentMonths,
            $interestRate,
            $interest,
            $pendingStatusId,
            $request,
            &$loan
        ): void {
            $application = \App\Models\LoanApplication::create([
                'member_id' => $memberId,
                'loan_type_id' => $loanTypeId,
                'requested_amount' => $amount,
                'requested_tenure_months' => $repaymentMonths,
                'purpose' => $request->purpose,
                'status_id' => $pendingStatusId,
                'submission_date' => now(),
            ]);

            $loan = Loan::create([
                'application_id' => $application->id,
                'member_id' => $memberId,
                'loan_type_id' => $loanTypeId,
                'principal_amount' => $amount,
                'interest_rate' => $interestRate,
                'repayment_months' => $repaymentMonths,
                'total_interest' => $interest,
                'notes' => $request->purpose,
                'status_id' => $pendingStatusId,
                'application_date' => now(),
            ]);
            $application->update(['converted_to_loan_id' => $loan->id]);
        });

        return response()->json($loan->load('member'));
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $loan->update([
            'status_id' => $approvedStatusId ?? $loan->status_id,
            'approval_date' => now()->toDateString(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_ip' => request()->ip(),
        ]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $approvedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }

        return response()->json($loan);
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
        $loan->update(['status_id' => $rejectedStatusId ?? $loan->status_id]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $rejectedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }
        return response()->json($loan);
    }

    public function repayment(Request $request, $id, TransactionPostingService $postingService)
    {
        $loan = Loan::findOrFail($id);
        $repaymentAmount = (float) $request->amount;
        $member = $loan->member;
        if (!$member || $repaymentAmount <= 0) {
            return response()->json(['message' => 'Invalid repayment'], 422);
        }

        $transactionTypeId = TransactionType::query()->where('name', 'loan_payment')->value('id')
            ?? TransactionType::query()->where('name', 'loan_repayment')->value('id')
            ?? TransactionType::query()->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'loan_payment')->value('id')
            ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
            ?? TransactionCategory::query()->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id')
            ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $transaction = null;
        DB::transaction(function () use (
            $member,
            $loan,
            $repaymentAmount,
            $transactionTypeId,
            $categoryId,
            $statusId,
            $paymentMethodId,
            $currencyId,
            $postingService,
            &$transaction
        ): void {
            $transaction = Transaction::create([
                'member_id' => $member->id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'status_id' => $statusId,
                'amount' => $repaymentAmount,
                'net_amount' => $repaymentAmount,
                'currency_id' => $currencyId,
                'balance_before' => (float) ($member->balance ?? 0),
                'balance_after' => (float) ($member->balance ?? 0),
                'payment_method_id' => $paymentMethodId,
                'description' => 'Loan repayment #' . $loan->loan_number,
                'transaction_date' => now(),
                'value_date' => now(),
                'processed_by' => auth()->id() ?? $member->user_id ?? \App\Models\User::query()->value('id'),
                'processed_at' => now(),
                'metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => $repaymentAmount],
                'related_loan_id' => $loan->id,
            ]);

            $postingService->applyCategoryUpdates($transaction, ['metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => $repaymentAmount]]);
        });

        return response()->json($loan);
    }

    public function disburse(Request $request, $id, TransactionPostingService $postingService)
    {
        $loan = Loan::with('member')->findOrFail($id);
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $disbursedStatusId = LoanStatus::query()->where('name', 'disbursed')->value('id') ?? $approvedStatusId;

        if ($loan->status_id !== $approvedStatusId) {
            return response()->json(['message' => 'Only approved loans can be disbursed'], 422);
        }

        $transactionTypeId = TransactionType::query()->where('name', 'loan_disbursement')->value('id')
            ?? TransactionType::query()->where('name', 'deposit')->value('id')
            ?? TransactionType::query()->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'loan_disbursement')->value('id')
            ?? TransactionCategory::query()->where('name', 'savings_deposit')->value('id')
            ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
            ?? TransactionCategory::query()->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');
        $paymentMethodId = $request->payment_method_id
            ?? $loan->disbursement_method_id
            ?? PaymentMethod::query()->where('name', 'cash')->value('id')
            ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $amount = (float) ($loan->principal_amount ?? 0);
        $balanceBefore = (float) ($loan->member?->balance ?? 0);
        $balanceAfter = $balanceBefore + $amount;
        $disbursementDate = $request->disbursement_date ?? now();

        DB::transaction(function () use (
            $loan,
            $transactionTypeId,
            $categoryId,
            $statusId,
            $paymentMethodId,
            $currencyId,
            $amount,
            $balanceBefore,
            $balanceAfter,
            $disbursementDate,
            $disbursedStatusId,
            $postingService
        ): void {
            $transaction = Transaction::create([
                'member_id' => $loan->member_id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'status_id' => $statusId,
                'amount' => $amount,
                'net_amount' => $amount,
                'currency_id' => $currencyId,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'payment_method_id' => $paymentMethodId,
                'description' => 'Loan disbursement #' . $loan->loan_number,
                'transaction_date' => $disbursementDate,
                'value_date' => $disbursementDate,
                'processed_by' => auth()->id() ?? $loan->member?->user_id ?? \App\Models\User::query()->value('id'),
                'processed_at' => now(),
                'metadata' => ['loan_id' => $loan->id],
                'related_loan_id' => $loan->id,
            ]);

            $postingService->applyCategoryUpdates($transaction, ['metadata' => ['loan_id' => $loan->id]]);

            $loan->update([
                'status_id' => $disbursedStatusId ?? $loan->status_id,
                'disbursement_date' => $disbursementDate instanceof \Carbon\Carbon ? $disbursementDate->toDateString() : (string) $disbursementDate,
                'disbursement_transaction_id' => $transaction->id,
                'disbursement_method_id' => $paymentMethodId,
                'disbursed_by' => auth()->id(),
                'disbursed_at' => now(),
            ]);
        });

        return response()->json($loan->fresh());
    }
}
