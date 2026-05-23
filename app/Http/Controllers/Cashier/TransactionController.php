<?php

namespace App\Http\Controllers\Cashier;

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
        $query = Transaction::with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('member_account_number', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('transactionType', fn ($q) => $q->where('name', $request->type));
        }

        if ($request->filled('status')) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
        }

        if ($request->filled('payment_method')) {
            $paymentMethodId = PaymentMethod::query()->where('name', $request->payment_method)->value('id');
            if ($paymentMethodId) {
                $query->where('payment_method_id', $paymentMethodId);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $categoryFilter = $request->get('category');
        if ($categoryFilter) {
            $categoryId = TransactionCategory::query()->where('name', $categoryFilter)->value('id');
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $categoryCounts = Transaction::query()
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = $request->search;
                $builder->where(function ($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($memberQuery) use ($search) {
                            $memberQuery->where('full_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('member', function ($memberQuery) use ($search) {
                            $memberQuery->where('member_account_number', 'like', "%{$search}%")
                                ->orWhere('member_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('type'), function ($builder) use ($request) {
                $builder->whereHas('transactionType', fn ($q) => $q->where('name', $request->type));
            })
            ->when($request->filled('status'), function ($builder) use ($request) {
                $builder->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
            })
            ->when($request->filled('payment_method'), function ($builder) use ($request) {
                $paymentMethodId = PaymentMethod::query()->where('name', $request->payment_method)->value('id');
                if ($paymentMethodId) {
                    $builder->where('payment_method_id', $paymentMethodId);
                }
            })
            ->when($request->filled('date_from'), function ($builder) use ($request) {
                $builder->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($builder) use ($request) {
                $builder->whereDate('created_at', '<=', $request->date_to);
            })
            ->select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $categories = TransactionCategory::query()
            ->whereIn('id', $categoryCounts->keys())
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();

        $transactions = $query->latest()->paginate(15)->appends($request->query());
        return view('cashier.financial.transactions', compact('transactions', 'categories', 'categoryCounts'));
    }

    public function create()
    {
        $members = Member::with(['loans', 'shares', 'dividends'])
            ->orderBy('full_name')
            ->get();

        $memberSummaries = $members->mapWithKeys(function ($member) {
            $loan = $member->loans
                ->filter(fn($loan) => $loan->status === 'approved' && ($loan->remaining_balance ?? 0) > 0)
                ->sortByDesc('created_at')
                ->first();

            $dividend = $member->dividends
                ->sortByDesc('paid_at')
                ->first();

            return [
                $member->member_id => [
                    'full_name' => $member->full_name,
                    'profile_picture_url' => $member->profile_picture_url,
                    'balance' => (float) ($member->balance ?? 0),
                    'savings' => (float) ($member->savings ?? 0),
                    'savings_balance' => (float) ($member->savings_balance ?? 0),
                    'loan' => $loan ? [
                        'loan_id' => $loan->loan_id,
                        'status' => $loan->status_label,
                        'remaining_balance' => (float) ($loan->remaining_balance ?? 0),
                        'monthly_payment' => (float) ($loan->monthly_payment ?? 0),
                        'amount' => (float) ($loan->amount ?? 0),
                        'interest' => (float) ($loan->interest ?? 0),
                        'processing_fee' => (float) ($loan->processing_fee ?? 0),
                    ] : null,
                    'shares' => [
                        'total_shares' => (float) ($member->total_shares ?? 0),
                        'total_value' => (float) ($member->total_share_value ?? 0),
                    ],
                    'dividend' => $dividend ? [
                        'amount' => (float) ($dividend->amount_per_share ?? 0),
                        'payment_date' => optional($dividend->paid_at)->format('Y-m-d'),
                        'shares_eligible' => (float) ($dividend->shares_eligible ?? 0),
                        'status' => $dividend->status ?? null,
                    ] : null,
                    'last_savings' => null,
                ],
            ];
        });

        return view('cashier.financial.transactions-create', compact('members', 'memberSummaries'));
    }

    public function store(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'category' => 'nullable|string|required_without:transaction_category_id',
            'transaction_category_id' => 'nullable|exists:transaction_categories,id|required_without:category',
            'amount' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string',
            'channel' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'reference' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'batch_id' => 'nullable|string',
            'location' => 'nullable|string',
            'transaction_date' => 'required|date',
            'scheduled_at' => 'nullable|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'notification_sent' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            'metadata.transfer_to_member_id' => 'required_if:type,transfer',
            'metadata.transfer_to_member_name' => 'nullable|string',
        ]);

        $resolved = $this->resolveTypeAndCategory($validated['type'], $validated['category'] ?? null);
        $validated['type'] = $resolved['type'];
        if ($resolved['category']) {
            $validated['category'] = $resolved['category'];
        }

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id');
        if (!$transactionTypeId) {
            return back()->withErrors(['type' => 'Invalid transaction type.'])->withInput();
        }

        $categoryId = null;
        if (!empty($validated['transaction_category_id'])) {
            $category = TransactionCategory::find($validated['transaction_category_id']);
            if (!$category || (int) $category->transaction_type_id !== (int) $transactionTypeId) {
                return back()->withErrors([
                    'transaction_category_id' => 'Category must match the selected transaction type.',
                ])->withInput();
            }
            $categoryId = $category->id;
        } elseif (!empty($validated['category'])) {
            $categoryId = TransactionCategory::query()
                ->where('name', $validated['category'])
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

        $currencyCode = $validated['currency'] ?? 'UGX';
        $currencyId = Currency::query()->where('code', $currencyCode)->value('id');
        if (!$currencyId) {
            return back()->withErrors(['currency' => 'Invalid currency.'])->withInput();
        }

        $member = Member::find($memberId);
        
        // Apply system settings
        if (!isset($validated['fee']) || $validated['fee'] == 0) {
            $validated['fee'] = ($validated['amount'] * setting('transaction_fee', 1)) / 100;
        }
        
        $completedStatusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $balanceBefore = (float) ($member->balance ?? 0);
        
        $totalCharges = ($validated['fee'] ?? 0) + ($validated['tax_amount'] ?? 0) + ($validated['commission'] ?? 0);
        $netAmount = $validated['amount'] - $totalCharges;
        
        if ($validated['type'] === 'withdrawal') {
            $withdrawalFee = ($validated['amount'] * setting('withdrawal_fee', 2)) / 100;
            $totalCharges += $withdrawalFee;
            $netAmount = $validated['amount'] - $totalCharges;
        } elseif ($validated['type'] === 'deposit') {
            $netAmount = $validated['amount'] - $totalCharges;
        }

        $netAmount = max((float) $netAmount, 0);
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'credit'
            ? $balanceBefore + $netAmount
            : $balanceBefore - $netAmount;

        $toMemberId = null;
        if ($validated['type'] === 'transfer') {
            $toMemberId = $validated['metadata']['transfer_to_member_id'] ?? null;
            if ($toMemberId) {
                $toMemberId = resolve_member_id($toMemberId);
            }
        }
        
        if ($request->notification_sent && setting('sms_notifications', 1)) {
            $validated['notification_sent_at'] = now();
        }
        
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
                $toMemberId,
                $request,
                $postingService,
                $netAmount
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $memberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $completedStatusId,
                    'amount' => $validated['amount'],
                    'net_amount' => $netAmount,
                    'fee' => $validated['fee'] ?? 0,
                    'tax_amount' => $validated['tax_amount'] ?? 0,
                    'commission' => $validated['commission'] ?? 0,
                    'currency_id' => $currencyId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'payment_method_id' => $paymentMethodId,
                    'reference_number' => $validated['reference'] ?? null,
                    'receipt_number' => $validated['receipt_number'] ?? null,
                    'channel' => $validated['channel'] ?? null,
                    'related_transfer_to_member_id' => $toMemberId ?? null,
                    'description' => $validated['description'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'metadata' => $validated['metadata'] ?? null,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'processed_ip' => $request->ip(),
                    'processed_location' => $validated['location'] ?? null,
                    'transaction_date' => $validated['transaction_date'] ?? now(),
                    'value_date' => $validated['transaction_date'] ?? now(),
                    'is_scheduled' => !empty($validated['scheduled_at']),
                    'scheduled_at' => $validated['scheduled_at'] ?? null,
                ]);

                $postingService->applyCategoryUpdates($transaction, $validated);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('cashier.financial.transactions')->with('success', 'Transaction created successfully');
    }

    public function storeTransfer(Request $request, TransactionPostingService $postingService)
    {
        $request->merge([
            'member_id' => $request->input('from_member_id'),
            'type' => 'transfer',
            'category' => $request->input('category', 'transfer'),
            'payment_method' => $request->input('payment_method', 'cash'),
            'transaction_date' => $request->input('transaction_date', now()->toDateString()),
            'metadata' => array_merge($request->input('metadata', []), [
                'transfer_to_member_id' => $request->input('to_member_id'),
            ]),
        ]);

        return $this->store($request, $postingService);
    }

    private function resolveTypeAndCategory(string $type, ?string $category): array
    {
        $type = strtolower(trim($type));
        $category = strtolower(trim((string) $category));

        if ($category === 'loan_repayment') {
            return ['type' => 'loan_repayment', 'category' => 'loan_principal'];
        }

        if ($category === 'shares') {
            return ['type' => 'share_purchase', 'category' => 'share_purchase'];
        }

        if ($category === 'dividend') {
            return ['type' => 'dividend', 'category' => 'share_dividend'];
        }

        if ($type === 'deposit') {
            return ['type' => 'deposit', 'category' => $category === 'savings' ? 'savings_deposit' : ($category ?: 'savings_deposit')];
        }

        if ($type === 'withdrawal') {
            return ['type' => 'withdrawal', 'category' => $category === 'savings' ? 'savings_withdrawal' : ($category ?: 'savings_withdrawal')];
        }

        if ($type === 'transfer') {
            return ['type' => 'transfer', 'category' => 'transfer_out'];
        }

        return ['type' => $type, 'category' => $category ?: null];
    }

    public function show($id)
    {
        $transaction = Transaction::with('member')->findOrFail($id);
        return view('cashier.financial.transactions-show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('cashier.financial.transactions-edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $validated = $request->validate([
            'type' => 'required|in:deposit,withdrawal,transfer',
            'category' => 'nullable|string|required_without:transaction_category_id',
            'transaction_category_id' => 'nullable|exists:transaction_categories,id|required_without:category',
            'amount' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'channel' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'reference' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,reversed',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id');
        $statusId = TransactionStatus::query()->where('name', $validated['status'])->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', $validated['payment_method'])->value('id');

        $categoryId = null;
        if (!empty($validated['transaction_category_id'])) {
            $category = TransactionCategory::find($validated['transaction_category_id']);
            if (!$category || (int) $category->transaction_type_id !== (int) $transactionTypeId) {
                return back()->withErrors([
                    'transaction_category_id' => 'Category must match the selected transaction type.',
                ])->withInput();
            }
            $categoryId = $category->id;
        } elseif (!empty($validated['category'])) {
            $categoryId = TransactionCategory::query()
                ->where('name', $validated['category'])
                ->where('transaction_type_id', $transactionTypeId)
                ->value('id');
        }

        $transaction->update([
            'transaction_type_id' => $transactionTypeId,
            'category_id' => $categoryId ?? $transaction->category_id,
            'status_id' => $statusId,
            'amount' => $validated['amount'],
            'fee' => $validated['fee'] ?? 0,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'commission' => $validated['commission'] ?? 0,
            'payment_method_id' => $paymentMethodId,
            'channel' => $validated['channel'] ?? null,
            'reference_number' => $validated['reference'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'processed_location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        return redirect()->route('cashier.financial.transactions')->with('success', 'Transaction updated successfully');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        
        return redirect()->route('cashier.financial.transactions')->with('success', 'Transaction deleted successfully');
    }

    public function deposits(Request $request)
    {
        $query = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
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

        $deposits = $query->latest()->paginate(15)->appends($request->query());
        return view('cashier.financial.deposits', compact('deposits'));
    }

    public function withdrawals(Request $request)
    {
        $query = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
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

        $withdrawals = $query->latest()->paginate(15)->appends($request->query());
        return view('cashier.financial.withdrawals', compact('withdrawals'));
    }

    public function transfers(Request $request)
    {
        $query = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'transfer'))->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
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

        $transactions = $query->latest()->paginate(15)->appends($request->query());
        return view('cashier.financial.transfers', compact('transactions'));
    }

    public function reports()
    {
        return redirect()->route('cashier.reports.index');
    }

    public function generateReport(Request $request)
    {
        // Implementation
    }
}
