<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $loans = Loan::with('member')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'loans' => $loans,
                'total_loans' => $loans->count(),
                'pending_loans' => $loans->where('status', 'pending')->count(),
                'approved_loans' => $loans->whereIn('status', ['approved', 'disbursed'])->count(),
                'disbursed_loans' => $loans->where('status', 'disbursed')->count(),
                'rejected_loans' => $loans->where('status', 'rejected')->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading loans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'member_id' => 'required|string',
                'amount' => 'required|numeric|min:1000',
                'purpose' => 'required|string|max:500',
                'status' => 'nullable|in:pending,approved,rejected,disbursed',
                'repayment_months' => 'nullable|integer|min:1|max:120',
                'interest_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $memberId = resolve_member_id($request->member_id);
            if (!$memberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid member ID'
                ], 422);
            }

            $repaymentMonths = (int) ($request->repayment_months ?? 12);
            $interestRate = (float) ($request->interest_rate ?? 10);
            $interest = (float) $request->amount * ($interestRate / 100) * ($repaymentMonths / 12);
            $monthlyPayment = ($request->amount + $interest) / max($repaymentMonths, 1);

            $loanTypeId = LoanType::query()->where('is_active', 1)->value('id')
                ?? LoanType::query()->value('id');
            $statusId = LoanStatus::query()->where('name', $request->status ?? 'pending')->value('id')
                ?? LoanStatus::query()->value('id');

            $loan = null;
            DB::transaction(function () use (
                $memberId,
                $loanTypeId,
                $request,
                $repaymentMonths,
                $statusId,
                $interestRate,
                $interest,
                &$loan
            ): void {
                $application = LoanApplication::create([
                    'member_id' => $memberId,
                    'loan_type_id' => $loanTypeId,
                    'requested_amount' => $request->amount,
                    'requested_tenure_months' => $repaymentMonths,
                    'purpose' => $request->purpose,
                    'status_id' => $statusId,
                    'submission_date' => now(),
                ]);

                $loan = Loan::create([
                    'application_id' => $application->id,
                    'member_id' => $memberId,
                    'loan_type_id' => $loanTypeId,
                    'principal_amount' => $request->amount,
                    'interest_rate' => $interestRate,
                    'total_interest' => $interest,
                    'repayment_months' => $repaymentMonths,
                    'application_date' => now()->toDateString(),
                    'status_id' => $statusId,
                    'notes' => $request->purpose,
                ]);
                $application->update(['converted_to_loan_id' => $loan->id]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Loan request created successfully',
                'loan' => $loan->load('member')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a loan application
     */
    public function approve($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if already approved
            if ($loan->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan is already approved'
                ], 400);
            }

            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
            $loan->update([
                'status_id' => $approvedStatusId ?? $loan->status_id,
                'approval_date' => now()->toDateString(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approved_ip' => request()->ip(),
            ]);

            if ($loan->application_id) {
                LoanApplication::query()
                    ->whereKey($loan->application_id)
                    ->update([
                        'status_id' => $approvedStatusId ?? $loan->status_id,
                        'decision_by' => Auth::id(),
                        'decision_date' => now(),
                    ]);
            }

            // Calculate monthly payment
            $monthlyRate = ($loan->interest_rate ?? 0) / 12 / 100;
            if ($monthlyRate > 0) {
                $monthlyPayment = $loan->principal_amount * $monthlyRate / (1 - pow(1 + $monthlyRate, -$loan->repayment_months));
            } else {
                $monthlyPayment = $loan->principal_amount / max((int) $loan->repayment_months, 1);
            }
            $loan->total_interest = $loan->total_interest ?? 0;
            $loan->save();

            return response()->json([
                'success' => true,
                'message' => 'Loan approved successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $loan = Loan::with('member')->findOrFail($id);

            return response()->json([
                'success' => true,
                'loan' => $loan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading loan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a loan application
     */
    public function reject($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if already processed
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan has already been processed'
                ], 400);
            }

            $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
            $loan->update([
                'status_id' => $rejectedStatusId ?? $loan->status_id,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approved_ip' => request()->ip(),
            ]);

            if ($loan->application_id) {
                LoanApplication::query()
                    ->whereKey($loan->application_id)
                    ->update([
                        'status_id' => $rejectedStatusId ?? $loan->status_id,
                        'decision_by' => Auth::id(),
                        'decision_date' => now(),
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Loan rejected successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Validate request
            $request->validate([
                'amount' => 'required|numeric|min:1000',
                'purpose' => 'required|string|max:500',
                'repayment_months' => 'required|integer|min:1|max:60',
                'status' => 'required|in:pending,approved,rejected,disbursed'
            ]);

            // Update loan
            $loan->principal_amount = $request->amount;
            $loan->notes = $request->purpose;
            $loan->repayment_months = $request->repayment_months;
            $loan->status_id = LoanStatus::query()->where('name', $request->status)->value('id') ?? $loan->status_id;

            // Recalculate monthly payment if approved
            if ($loan->status === 'approved') {
                $monthlyRate = ($loan->interest_rate ?? 0) / 12 / 100;
                if ($monthlyRate > 0) {
                    $monthlyPayment = $loan->principal_amount * $monthlyRate / (1 - pow(1 + $monthlyRate, -$loan->repayment_months));
                } else {
                    $monthlyPayment = $loan->principal_amount / max((int) $loan->repayment_months, 1);
                }
            }

            $loan->save();

            return response()->json([
                'success' => true,
                'message' => 'Loan updated successfully',
                'loan' => $loan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            // Check if loan can be deleted (only pending loans)
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending loans can be deleted'
                ], 400);
            }

            $loan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Loan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recordRepayment(Request $request, $id, TransactionPostingService $postingService)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $loan = Loan::findOrFail($id);
            $member = $loan->member;
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 422);
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

            DB::transaction(function () use (
                $member,
                $loan,
                $request,
                $transactionTypeId,
                $categoryId,
                $statusId,
                $paymentMethodId,
                $currencyId,
                $postingService
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $member->id,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $request->amount,
                    'net_amount' => $request->amount,
                    'currency_id' => $currencyId,
                    'balance_before' => (float) ($member->balance ?? 0),
                    'balance_after' => (float) ($member->balance ?? 0),
                    'payment_method_id' => $paymentMethodId,
                    'description' => 'Loan repayment #' . $loan->loan_number,
                    'transaction_date' => now(),
                    'value_date' => now(),
                    'processed_by' => Auth::id() ?? $member->user_id ?? \App\Models\User::query()->value('id'),
                    'processed_at' => now(),
                    'metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => (float) $request->amount],
                    'related_loan_id' => $loan->id,
                ]);

                $postingService->applyCategoryUpdates($transaction, ['metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => (float) $request->amount]]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Repayment recorded successfully.',
                'loan' => $loan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording repayment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function disburse(Request $request, $id, TransactionPostingService $postingService)
    {
        try {
            $loan = Loan::with('member')->findOrFail($id);
            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
            $disbursedStatusId = LoanStatus::query()->where('name', 'disbursed')->value('id') ?? $approvedStatusId;

            if ($loan->status_id !== $approvedStatusId) {
                return response()->json(['success' => false, 'message' => 'Only approved loans can be disbursed'], 422);
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
                    'processed_by' => Auth::id() ?? $loan->member?->user_id ?? \App\Models\User::query()->value('id'),
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
                    'disbursed_by' => Auth::id(),
                    'disbursed_at' => now(),
                ]);
            });

            return response()->json(['success' => true, 'loan' => $loan->fresh()]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error disbursing loan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
