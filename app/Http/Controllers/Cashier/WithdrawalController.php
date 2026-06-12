<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))
            ->with(['member', 'transactionType', 'paymentMethod', 'statusRelation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('member_account_number', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->latest()->paginate(20)->appends($request->query());
        return view('cashier.withdrawals.index', compact('withdrawals'));
    }

    public function create()
    {
        $members = Member::all();
        return view('cashier.withdrawals.create', compact('members'));
    }

    public function store(Request $request, TransactionPostingService $postingService)
    {
        $request->merge([
            'payment_method' => $request->input('payment_method', 'cash'),
        ]);

        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'category' => 'nullable|string',
            'transaction_category_id' => 'nullable|exists:transaction_categories,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $transactionTypeId = TransactionType::query()->where('name', 'withdrawal')->value('id');
        if (!$transactionTypeId) {
            return back()->withErrors(['type' => 'Invalid transaction type.'])->withInput();
        }

        $categoryId = null;
        if (!empty($validated['transaction_category_id'])) {
            $category = TransactionCategory::find($validated['transaction_category_id']);
            if (!$category || (int) $category->transaction_type_id !== (int) $transactionTypeId) {
                return back()->withErrors([
                    'transaction_category_id' => 'Category must match withdrawal type.',
                ])->withInput();
            }
            $categoryId = $category->id;
        } else {
            $categoryId = TransactionCategory::query()
                ->where('name', $validated['category'] ?? 'savings_withdrawal')
                ->where('transaction_type_id', $transactionTypeId)
                ->value('id');
        }

        if (!$categoryId) {
            return back()->withErrors(['category' => 'Transaction category is required.'])->withInput();
        }

        $paymentMethodId = PaymentMethod::query()->where('name', $validated['payment_method'])->value('id');
        if (!$paymentMethodId) {
            return back()->withErrors(['payment_method' => 'Invalid payment method.'])->withInput();
        }

        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');
        if (!$currencyId) {
            return back()->withErrors(['currency' => 'Invalid currency.'])->withInput();
        }

        $member = Member::findOrFail($memberId);
        $completedStatusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $balanceBefore = (float) ($member->balance ?? 0);
        $withdrawalFee = ($validated['amount'] * setting('withdrawal_fee', 0)) / 100;
        $netAmount = max((float) ($validated['amount'] - $withdrawalFee), 0);
        $balanceAfter = $balanceBefore - $netAmount;

        try {
            DB::transaction(function () use (
                $memberId,
                $transactionTypeId,
                $categoryId,
                $completedStatusId,
                $validated,
                $currencyId,
                $balanceBefore,
                $balanceAfter,
                $paymentMethodId,
                $postingService,
                $netAmount,
                $withdrawalFee
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $memberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $completedStatusId,
                    'amount' => $validated['amount'],
                    'net_amount' => $netAmount,
                    'fee' => $withdrawalFee,
                    'tax_amount' => 0,
                    'commission' => 0,
                    'currency_id' => $currencyId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'payment_method_id' => $paymentMethodId,
                    'reference_number' => $validated['reference'] ?? null,
                    'description' => $validated['description'] ?? 'Withdrawal transaction',
                    'notes' => $validated['notes'] ?? null,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'transaction_date' => now(),
                    'value_date' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $validated);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('cashier.withdrawals.index')->with('success', 'Withdrawal recorded successfully');
    }

    public function show($id)
    {
        $transaction = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))
            ->with(['member', 'transactionType', 'paymentMethod', 'statusRelation'])
            ->findOrFail($id);
        return view('cashier.financial.transactions-show', compact('transaction'));
    }
}
