<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Member;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\TransactionCategory;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('member')->orderBy('created_at', 'desc')->get();
        return response()->json($transactions);
    }

    public function store(Request $request, TransactionPostingService $postingService)
    {
        $memberId = resolve_member_id($request->member_id);
        if (!$memberId) {
            return response()->json(['message' => 'Invalid member'], 422);
        }

        $transactionTypeId = TransactionType::query()->where('name', $request->type)->value('id');
        $completedStatusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $categoryName = match ($request->type) {
            'deposit' => 'savings_deposit',
            'withdrawal' => 'savings_withdrawal',
            'transfer' => 'transfer_out',
            default => 'savings_deposit',
        };
        $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $member = Member::find($memberId);
        $balanceBefore = (float) ($member?->balance ?? 0);
        $withdrawalFee = $request->type === 'withdrawal'
            ? ($request->amount * setting('withdrawal_fee', 0)) / 100
            : 0;
        $netAmount = max((float) ($request->amount - $withdrawalFee), 0);
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'credit'
            ? $balanceBefore + $netAmount
            : $balanceBefore - $netAmount;

        $transaction = null;
        try {
            DB::transaction(function () use (
                $memberId,
                $transactionTypeId,
                $categoryId,
                $completedStatusId,
                $paymentMethodId,
                $currencyId,
                $request,
                $balanceBefore,
                $balanceAfter,
                $netAmount,
                $withdrawalFee,
                &$transaction,
                $postingService
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $memberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $completedStatusId,
                    'amount' => $request->amount,
                    'net_amount' => $netAmount,
                    'fee' => $withdrawalFee,
                    'currency_id' => $currencyId,
                    'payment_method_id' => $paymentMethodId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $request->description,
                    'reference_number' => 'TXN' . time(),
                    'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
                    'processed_at' => now(),
                    'transaction_date' => now(),
                    'value_date' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $request->all());
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($transaction->load('member'));
    }

    public function getByMember($memberId)
    {
        $resolvedMemberId = resolve_member_id($memberId);
        if (!$resolvedMemberId) {
            return response()->json([]);
        }

        $transactions = Transaction::where('member_id', $resolvedMemberId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($transactions);
    }

    public function summary()
    {
        $summary = [
            'total_deposits' => Transaction::query()
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->where('ts.name', 'completed')
                ->whereIn('tc.name', ['savings_deposit', 'transfer_in', 'loan_disbursement'])
                ->sum(DB::raw('COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)')),
            'total_withdrawals' => Transaction::query()
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->where('ts.name', 'completed')
                ->whereIn('tc.name', ['savings_withdrawal', 'transfer_out', 'fundraising_transfer'])
                ->sum(DB::raw('COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)')),
            'total_transactions' => Transaction::count(),
            'recent_transactions' => Transaction::with('member')->latest()->take(10)->get()
        ];

        return response()->json($summary);
    }
}
