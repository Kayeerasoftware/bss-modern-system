<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Models\Member;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function dashboard()
    {
        $viewStats = app(DashboardStatsService::class)->get();
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');

        return response()->json([
            'success' => true,
            'stats' => [
                'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
                'pendingLoans' => (int) ($viewStats['pending_loans_count'] ?? ($pendingStatusId ? Loan::where('status_id', $pendingStatusId)->count() : 0)),
                'todayDeposits' => Transaction::query()->ofType('deposit')->whereDate('created_at', today())->sum('amount'),
                'todayWithdrawals' => Transaction::query()->ofType('withdrawal')->whereDate('created_at', today())->sum('amount'),
            ],
        ]);
    }

    public function getRecentTransactions()
    {
        return response()->json([
            'success' => true,
            'transactions' => Transaction::latest()->take(20)->get(),
        ]);
    }

    public function processDeposit(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $resolvedMemberId = resolve_member_id($validated['member_id']);
        if (!$resolvedMemberId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid member ID',
            ], 422);
        }

        $member = Member::find($resolvedMemberId);
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found',
            ], 404);
        }

        $transactionTypeId = TransactionType::query()->where('name', 'deposit')->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'savings_deposit')->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $balanceBefore = (float) ($member->balance ?? 0);
        $netAmount = (float) $validated['amount'];
        $balanceAfter = $balanceBefore + $netAmount;

        $transaction = null;
        DB::transaction(function () use (
            $resolvedMemberId,
            $transactionTypeId,
            $categoryId,
            $statusId,
            $paymentMethodId,
            $currencyId,
            $validated,
            $balanceBefore,
            $balanceAfter,
            $netAmount,
            &$transaction,
            $postingService
        ): void {
            $transaction = Transaction::create([
                'member_id' => $resolvedMemberId,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'status_id' => $statusId,
                'amount' => $validated['amount'],
                'net_amount' => $netAmount,
                'currency_id' => $currencyId,
                'payment_method_id' => $paymentMethodId,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $validated['description'] ?? 'Deposit',
                'processed_by' => auth()->id() ?? User::query()->value('id'),
                'processed_at' => now(),
                'transaction_date' => now(),
                'value_date' => now(),
            ]);

            $postingService->applyCategoryUpdates($transaction, $validated);
        });

        return response()->json([
            'success' => true,
            'message' => 'Deposit processed successfully.',
            'transaction' => $transaction,
        ]);
    }

    public function processWithdrawal(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $resolvedMemberId = resolve_member_id($validated['member_id']);
        if (!$resolvedMemberId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid member ID',
            ], 422);
        }

        $member = Member::find($resolvedMemberId);
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found',
            ], 404);
        }

        $transactionTypeId = TransactionType::query()->where('name', 'withdrawal')->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'savings_withdrawal')->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $balanceBefore = (float) ($member->balance ?? 0);
        $withdrawalFee = ($validated['amount'] * setting('withdrawal_fee', 0)) / 100;
        $netAmount = max((float) ($validated['amount'] - $withdrawalFee), 0);
        $balanceAfter = $balanceBefore - $netAmount;

        $transaction = null;
        try {
            DB::transaction(function () use (
                $resolvedMemberId,
                $transactionTypeId,
                $categoryId,
                $statusId,
                $paymentMethodId,
                $currencyId,
                $validated,
                $balanceBefore,
                $balanceAfter,
                $netAmount,
                $withdrawalFee,
                &$transaction,
                $postingService
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $resolvedMemberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $validated['amount'],
                    'net_amount' => $netAmount,
                    'fee' => $withdrawalFee,
                    'currency_id' => $currencyId,
                    'payment_method_id' => $paymentMethodId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $validated['description'] ?? 'Withdrawal',
                    'processed_by' => auth()->id() ?? User::query()->value('id'),
                    'processed_at' => now(),
                    'transaction_date' => now(),
                    'value_date' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $validated);
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal processed successfully.',
            'transaction' => $transaction,
        ]);
    }
}
