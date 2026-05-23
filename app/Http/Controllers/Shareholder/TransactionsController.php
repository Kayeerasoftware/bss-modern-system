<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\TransactionCategory;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            $transactions = collect();
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $summary = [
                'total_transactions' => 0,
                'completed_deposits' => 0,
                'completed_withdrawals' => 0,
                'completed_transfers' => 0,
                'pending_count' => 0,
                'net_flow' => 0,
            ];
            return view('shareholder.transactions', compact('transactions', 'summary'));
        }
        
        $query = Transaction::with('member')->where('member_id', $member->id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('receipt_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
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

        $completedQuery = (clone $query)->ofStatus('completed');

        $summary = [
            'total_transactions' => (int) (clone $query)->count(),
            'completed_deposits' => (float) (clone $completedQuery)->ofType('deposit')->sum('amount'),
            'completed_withdrawals' => (float) (clone $completedQuery)->ofType('withdrawal')->sum('amount'),
            'completed_transfers' => (float) (clone $completedQuery)->ofType('transfer')->sum('amount'),
            'pending_count' => (int) (clone $query)->ofStatus('pending')->count(),
        ];
        $summary['net_flow'] = $summary['completed_deposits'] - $summary['completed_withdrawals'] - $summary['completed_transfers'];

        $categoryCounts = Transaction::query()
            ->where('member_id', $member->id)
            ->when($request->filled('search'), function ($builder) use ($request) {
                $builder->where(function ($q) use ($request) {
                    $q->where('transaction_number', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                        ->orWhere('receipt_number', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('type'), function ($builder) use ($request) {
                $builder->ofType($request->type);
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

        $transactions = $query->latest('created_at')->paginate(20);

        return view('shareholder.transactions', compact('transactions', 'summary', 'categories', 'categoryCounts'));
    }

    public function create()
    {
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('shareholder.transactions')->with('error', 'Member profile not found');
        }
        
        $members = Member::with(['loans', 'shares', 'dividends'])
            ->where('id', $member->id)
            ->get();

        $savingsDateColumn = \Illuminate\Support\Facades\Schema::hasColumn('savings_transactions', 'transaction_date')
            ? 'transaction_date'
            : 'created_at';

        $lastSavings = \App\Models\SavingsHistory::forMember($member->id)
            ->orderByDesc($savingsDateColumn)
            ->first();

        $memberSummaries = $members->mapWithKeys(function ($member) use ($lastSavings) {
            $loan = $member->loans
                ->filter(fn($loan) => $loan->status === \App\Models\Loan::STATUS_APPROVED && $loan->remaining_balance > 0)
                ->sortByDesc('created_at')
                ->first();

            $dividend = $member->dividends
                ->sortByDesc('payment_date')
                ->first();

            return [
                $member->id => [
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
                        'amount' => (float) ($dividend->amount ?? 0),
                        'payment_date' => optional($dividend->payment_date)->format('Y-m-d'),
                        'dividend_rate' => (float) ($dividend->dividend_rate ?? 0),
                        'shares_eligible' => (float) ($dividend->shares_eligible ?? 0),
                        'status' => $dividend->status,
                    ] : null,
                    'last_savings' => $lastSavings ? [
                        'amount' => (float) ($lastSavings->amount ?? 0),
                        'balance_after' => (float) ($lastSavings->balance_after ?? 0),
                        'transaction_date' => optional($lastSavings->transaction_date)->format('Y-m-d'),
                    ] : null,
                ],
            ];
        });

        return view('shareholder.transactions-create', compact('members', 'member', 'memberSummaries'));
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
            'metadata.transfer_to_member_id' => 'required_if:type,transfer|exists:members,id',
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

        $transactionTypeId = \App\Models\TransactionType::query()->where('name', $validated['type'])->value('id');
        if (!$transactionTypeId) {
            return back()->withErrors(['type' => 'Invalid transaction type.'])->withInput();
        }

        $categoryId = null;
        if (!empty($validated['transaction_category_id'])) {
            $category = \App\Models\TransactionCategory::find($validated['transaction_category_id']);
            if (!$category || (int) $category->transaction_type_id !== (int) $transactionTypeId) {
                return back()->withErrors([
                    'transaction_category_id' => 'Category must match the selected transaction type.',
                ])->withInput();
            }
            $categoryId = $category->id;
        } elseif (!empty($validated['category'])) {
            $categoryId = \App\Models\TransactionCategory::query()
                ->where('name', $validated['category'])
                ->where('transaction_type_id', $transactionTypeId)
                ->value('id');
        }

        if (!$categoryId) {
            return back()->withErrors(['category' => 'Transaction category is required.'])->withInput();
        }

        if ($validated['type'] === 'transfer') {
            $toMemberId = $validated['metadata']['transfer_to_member_id'] ?? null;
            if ($toMemberId && $toMemberId === $validated['member_id']) {
                return back()->withErrors([
                    'metadata.transfer_to_member_id' => 'Recipient must be different from sender.',
                ])->withInput();
            }

            if (empty($validated['description']) && $toMemberId) {
                $validated['description'] = 'Transfer to ' . $toMemberId;
            }
        }

        $paymentMethodId = \App\Models\PaymentMethod::query()->where('name', $validated['payment_method'])->value('id');
        if (!$paymentMethodId) {
            return back()->withErrors(['payment_method' => 'Invalid payment method.'])->withInput();
        }

        $currencyCode = $validated['currency'] ?? 'UGX';
        $currencyId = \App\Models\Currency::query()->where('code', $currencyCode)->value('id');
        if (!$currencyId) {
            return back()->withErrors(['currency' => 'Invalid currency.'])->withInput();
        }

        $member = Member::find($memberId);
        $completedStatusId = \App\Models\TransactionStatus::query()->where('name', 'completed')->value('id');
        $balanceBefore = (float) ($member->balance ?? 0);
        $totalCharges = ($validated['fee'] ?? 0) + ($validated['tax_amount'] ?? 0) + ($validated['commission'] ?? 0);
        $netAmount = $validated['amount'] - $totalCharges;

        if ($validated['type'] === 'withdrawal') {
        } elseif ($validated['type'] === 'deposit') {
            $netAmount = $validated['amount'] - $totalCharges;
        }

        $netAmount = max((float) $netAmount, 0);
        $impact = \App\Models\TransactionType::query()->whereKey($transactionTypeId)->value('impact');
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

        return redirect()->route('shareholder.transactions')->with('success', 'Transaction created successfully');
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
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            abort(404);
        }
        
        $transaction = Transaction::with('member')->where('member_id', $member->id)->findOrFail($id);
        return view('shareholder.transactions-show', compact('transaction'));
    }
}
