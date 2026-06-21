<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Models\Member;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->latest();

        if ($request->filled('member_id')) {
            $resolvedMemberId = resolve_member_id($request->member_id);
            if ($resolvedMemberId) {
                $query->where('member_id', $resolvedMemberId);
            }
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(25),
        ]);
    }

    public function store(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:deposit,withdrawal,transfer',
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

        $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $categoryName = match ($validated['type']) {
            'deposit' => 'savings_deposit',
            'withdrawal' => 'savings_withdrawal',
            'transfer' => 'transfer_out',
            default => 'savings_deposit',
        };
        $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id');

        $balanceBefore = (float) ($member->balance ?? 0);
        $withdrawalFee = $validated['type'] === 'withdrawal'
            ? ($validated['amount'] * setting('withdrawal_fee', 0)) / 100
            : 0;
        $netAmount = max((float) ($validated['amount'] - $withdrawalFee), 0);
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'credit'
            ? $balanceBefore + $netAmount
            : $balanceBefore - $netAmount;

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
                    'description' => $validated['description'] ?? null,
                    'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
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
            'data' => $transaction,
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => Transaction::findOrFail($id),
        ]);
    }
}
