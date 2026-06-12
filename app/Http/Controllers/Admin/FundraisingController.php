<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fundraising;
use App\Models\FundraisingStatus;
use App\Models\FundraisingContribution;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FundraisingController extends Controller
{
    public function index(Request $request)
    {
        $query = Fundraising::query()
            ->withCount('contributions')
            ->with('statusRelation');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('campaign_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $fundraisings = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.fundraising.index', compact('fundraisings'));
    }

    public function campaigns(Request $request)
    {
        $query = Fundraising::where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('campaign_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $campaigns = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.fundraising.campaigns', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.fundraising.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['campaign_number'] = 'FND' . str_pad(Fundraising::count() + 1, 6, '0', STR_PAD_LEFT);
        $validated['status_id'] = $this->resolveFundraisingStatusId('active');
        $validated['organizer_id'] = auth()->id();
        $validated['created_by'] = auth()->id();
        Fundraising::create($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign created successfully');
    }

    public function show($id)
    {
        $fundraising = Fundraising::with(['contributions', 'expenses'])->findOrFail($id);
        return view('admin.fundraising.show', compact('fundraising'));
    }

    public function contributions($id)
    {
        $fundraising = Fundraising::with(['contributions' => function ($q) {
            $q->with(['member', 'paymentMethod', 'transaction.transactionType'])->latest();
        }])->findOrFail($id);

        return view('admin.fundraising.contributions', compact('fundraising'));
    }

    public function contributionsCreate($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        $members = Member::with(['loans.statusRelation', 'shares', 'dividends'])
            ->orderBy('full_name')
            ->get();

        $users = User::query()
            ->select('id', 'username', 'email')
            ->orderBy('username')
            ->get();

        $paymentMethods = PaymentMethod::query()
            ->select('id', 'name', 'display_name')
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
        $internalPaymentMethodId = PaymentMethod::query()->where('name', 'internal')->value('id');

        $receiptIssuers = User::query()
            ->select('id', 'username', 'email')
            ->orderBy('username')
            ->get();

        $contributors = collect()
            ->merge($members->map(function (Member $member) {
                $name = trim((string) $member->full_name);
                $label = $name !== '' ? $name : ($member->member_number ?? 'Member');
                if ($member->member_number) {
                    $label .= ' (' . $member->member_number . ')';
                }
                return [
                    'name' => $name !== '' ? $name : $label,
                    'email' => $member->email,
                    'phone' => $member->primary_phone,
                    'label' => $label,
                    'member_id' => $member->id,
                ];
            }))
            ->merge($users->map(function (User $user) {
                return [
                    'name' => $user->username ?? 'User',
                    'email' => $user->email,
                    'phone' => null,
                    'label' => $user->username ?? 'User',
                    'member_id' => null,
                ];
            }))
            ->filter(fn ($item) => trim((string) $item['name']) !== '')
            ->values();

        $membersLite = $members->map(function (Member $member) {
            $name = trim((string) $member->full_name);
            return [
                'id' => $member->id,
                'name' => $name !== '' ? $name : ($member->member_number ?? ''),
                'email' => $member->email,
                'phone' => $member->primary_phone,
            ];
        })->values();

        $memberSummaries = $members->mapWithKeys(function ($member) {
            $loan = $member->loans
                ->filter(fn($loan) => $loan->status === 'approved' && ($loan->remaining_balance ?? 0) > 0)
                ->sortByDesc('created_at')
                ->first();

            $dividend = $member->dividends
                ->sortByDesc('paid_at')
                ->first();

            return [
                $member->id => [
                    'full_name' => $member->full_name,
                    'member_id' => $member->member_id,
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
                ],
            ];
        });

        return view('admin.fundraising.contributions-create', compact('fundraising', 'contributors', 'paymentMethods', 'receiptIssuers', 'members', 'membersLite', 'memberSummaries', 'internalPaymentMethodId'));
    }

    public function contributionsStore(Request $request, $id)
    {
        $fundraising = Fundraising::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'nullable|integer|exists:members,id|required_if:funding_source,savings_transfer',
            'funding_source' => 'required|in:deposit,savings_transfer',
            'contributor_name' => 'required|string|max:255',
            'contributor_email' => 'nullable|email|max:255',
            'contributor_phone' => 'nullable|string|max:50',
            'contributor_address' => 'nullable|string|max:2000',
            'is_anonymous' => 'nullable|boolean',
            'amount' => 'required|numeric|min:100',
            'contribution_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_issued' => 'nullable|boolean',
            'receipt_issued_at' => 'nullable|date',
            'receipt_issued_by' => 'nullable|exists:users,id',
            'thank_you_sent' => 'nullable|boolean',
            'thank_you_sent_at' => 'nullable|date',
            'message' => 'nullable|string|max:5000',
            'is_public_message' => 'nullable|boolean',
            'notes' => 'nullable|string|max:5000',
        ]);

        $memberId = null;
        if ($validated['funding_source'] === 'savings_transfer') {
            $memberId = !empty($validated['member_id']) ? (int) $validated['member_id'] : null;
            if (!$memberId) {
                return back()->withErrors(['member_id' => 'Member is required for savings transfer.'])->withInput();
            }
        } else {
            if (!empty($validated['member_id'])) {
                $memberId = (int) $validated['member_id'];
            } elseif (!empty($validated['contributor_email'])) {
                $memberId = Member::query()->where('email', $validated['contributor_email'])->value('id');
            } elseif (!empty($validated['contributor_phone'])) {
                $memberId = Member::query()->where('primary_phone', $validated['contributor_phone'])->value('id');
            } else {
                $memberId = Member::query()->where('full_name', $validated['contributor_name'])->value('id');
            }
            if (!$memberId) {
                $memberId = $this->getExternalContributorMemberId();
            }
        }

        if ($validated['funding_source'] === 'savings_transfer') {
            $member = Member::find($memberId);
            if (!$member) {
                return back()->withErrors(['member_id' => 'Selected member not found.'])->withInput();
            }
            $availableSavings = (float) ($member->savings_balance ?? 0);
            if ($availableSavings <= 0 && isset($member->savings)) {
                $availableSavings = (float) ($member->savings ?? 0);
            }
            if ($member && $availableSavings < $validated['amount']) {
                return back()->withErrors(['amount' => 'Insufficient savings balance for this transfer.'])->withInput();
            }
        }

        $transactionTypeName = $validated['funding_source'] === 'savings_transfer' ? 'transfer' : 'deposit';
        $transactionTypeId = TransactionType::query()->where('name', $transactionTypeName)->value('id')
            ?? TransactionType::query()->value('id');
        if ($validated['funding_source'] === 'savings_transfer') {
            $categoryId = TransactionCategory::query()->where('name', 'fundraising_transfer')->value('id')
                ?? TransactionCategory::query()->where('name', 'transfer_out')->value('id')
                ?? TransactionCategory::query()->where('name', 'savings_withdrawal')->value('id')
                ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
                ?? TransactionCategory::query()->value('id');
        } else {
            $categoryId = TransactionCategory::query()->where('name', 'fundraising_deposit')->value('id');
            if (!$categoryId) {
                return back()
                    ->withErrors(['funding_source' => 'Fundraising deposit category is not configured.'])
                    ->withInput();
            }
        }
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');

        $paymentMethodId = $validated['payment_method_id'];
        if ($validated['funding_source'] === 'savings_transfer') {
            $internalMethod = PaymentMethod::query()->where('name', 'internal')->value('id');
            if ($internalMethod) {
                $paymentMethodId = $internalMethod;
            }
        }

        $metadata = [
            'campaign_id' => $fundraising->id,
        ];
        if ($validated['funding_source'] === 'savings_transfer') {
            $metadata['affects_savings'] = true;
        }

        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');
        $member = $memberId ? Member::find($memberId) : null;
        $balanceBefore = (float) ($member?->balance ?? 0);
        $netAmount = (float) $validated['amount'];
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'debit'
            ? $balanceBefore - $netAmount
            : $balanceBefore + $netAmount;

        $transaction = Transaction::create([
            'member_id' => $memberId,
            'transaction_type_id' => $transactionTypeId,
            'category_id' => $categoryId,
            'status_id' => $statusId,
            'amount' => $validated['amount'],
            'net_amount' => $netAmount,
            'currency_id' => $currencyId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method_id' => $paymentMethodId,
            'transaction_date' => $validated['contribution_date'],
            'value_date' => $validated['contribution_date'],
            'description' => 'Fundraising contribution: ' . ($fundraising->title ?? $fundraising->campaign_id),
            'notes' => $validated['notes'] ?? null,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'processed_ip' => $request->ip(),
            'metadata' => $metadata,
        ]);

        if ($validated['funding_source'] === 'savings_transfer') {
            app(TransactionPostingService::class)->applyCategoryUpdates($transaction, ['metadata' => $metadata]);
        }

        $lastContribution = FundraisingContribution::latest('id')->value('contribution_number');
        $nextNumber = 1;
        if ($lastContribution && preg_match('/(\d+)$/', $lastContribution, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }
        $contributionNumber = 'CTB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $receiptIssued = !empty($validated['receipt_issued']);
        $thankYouSent = !empty($validated['thank_you_sent']);
        $receiptNumber = $validated['receipt_number'] ?? null;
        if (empty($receiptNumber)) {
            $receiptNumber = 'RCPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        }

        $fundraising->contributions()->create([
            'contribution_number' => $contributionNumber,
            'campaign_id' => $fundraising->id,
            'transaction_id' => $transaction->id,
            'member_id' => $memberId,
            'contributor_name' => $validated['contributor_name'],
            'contributor_email' => $validated['contributor_email'] ?? null,
            'contributor_phone' => $validated['contributor_phone'] ?? null,
            'contributor_address' => $validated['contributor_address'] ?? null,
            'is_anonymous' => !empty($validated['is_anonymous']),
            'amount' => $validated['amount'],
            'contribution_date' => $validated['contribution_date'],
            'payment_method_id' => $paymentMethodId,
            'receipt_number' => $receiptNumber,
            'receipt_issued' => $receiptIssued,
            'receipt_issued_at' => $receiptIssued ? ($validated['receipt_issued_at'] ?? now()) : null,
            'receipt_issued_by' => $receiptIssued ? ($validated['receipt_issued_by'] ?? auth()->id()) : null,
            'thank_you_sent' => $thankYouSent,
            'thank_you_sent_at' => $thankYouSent ? ($validated['thank_you_sent_at'] ?? now()) : null,
            'message' => $validated['message'] ?? null,
            'is_public_message' => !empty($validated['is_public_message']),
            'notes' => $validated['notes'] ?? null,
        ]);
        $fundraising->raised_amount += $validated['amount'];
        $fundraising->save();

        return redirect()
            ->route('admin.fundraising.contributions', $fundraising->id)
            ->with('success', 'Contribution added successfully');
    }

    public function contributionsShow($id, $contributionId)
    {
        $fundraising = Fundraising::findOrFail($id);
        $contribution = $fundraising->contributions()
            ->with(['member', 'paymentMethod', 'receiptIssuer', 'transaction.transactionType'])
            ->where('id', $contributionId)
            ->firstOrFail();

        return view('admin.fundraising.contributions-show', compact('fundraising', 'contribution'));
    }

    public function contributionsEdit($id, $contributionId)
    {
        $fundraising = Fundraising::findOrFail($id);
        $contribution = $fundraising->contributions()->with('transaction.transactionType')->where('id', $contributionId)->firstOrFail();

        $members = Member::with(['loans.statusRelation', 'shares', 'dividends'])
            ->orderBy('full_name')
            ->get();

        $users = User::query()
            ->select('id', 'username', 'email')
            ->orderBy('username')
            ->get();

        $paymentMethods = PaymentMethod::query()
            ->select('id', 'name', 'display_name')
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
        $internalPaymentMethodId = PaymentMethod::query()->where('name', 'internal')->value('id');

        $receiptIssuers = User::query()
            ->select('id', 'username', 'email')
            ->orderBy('username')
            ->get();

        $contributors = collect()
            ->merge($members->map(function (Member $member) {
                $nameParts = array_filter([$member->first_name, $member->middle_name, $member->last_name]);
                $name = trim(implode(' ', $nameParts));
                $label = $name !== '' ? $name : ($member->member_number ?? 'Member');
                if ($member->member_number) {
                    $label .= ' (' . $member->member_number . ')';
                }
                return [
                    'name' => $name !== '' ? $name : $label,
                    'email' => $member->email,
                    'phone' => $member->primary_phone,
                    'label' => $label,
                    'member_id' => $member->id,
                ];
            }))
            ->merge($users->map(function (User $user) {
                return [
                    'name' => $user->username ?? 'User',
                    'email' => $user->email,
                    'phone' => null,
                    'label' => $user->username ?? 'User',
                    'member_id' => null,
                ];
            }))
            ->filter(fn ($item) => trim((string) $item['name']) !== '')
            ->values();

        $membersLite = $members->map(function (Member $member) {
            $nameParts = array_filter([$member->first_name, $member->middle_name, $member->last_name]);
            $name = trim(implode(' ', $nameParts));
            return [
                'id' => $member->id,
                'name' => $name !== '' ? $name : ($member->member_number ?? ''),
                'email' => $member->email,
                'phone' => $member->primary_phone,
            ];
        })->values();

        $memberSummaries = $members->mapWithKeys(function ($member) {
            $loan = $member->loans
                ->filter(fn($loan) => $loan->status === 'approved' && ($loan->remaining_balance ?? 0) > 0)
                ->sortByDesc('created_at')
                ->first();

            $dividend = $member->dividends
                ->sortByDesc('paid_at')
                ->first();

            return [
                $member->id => [
                    'full_name' => $member->full_name,
                    'member_id' => $member->member_id,
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
                ],
            ];
        });

        $fundingSource = in_array(optional($contribution->transaction?->transactionType)->name, ['transfer', 'withdrawal'], true)
            ? 'savings_transfer'
            : 'deposit';

        return view('admin.fundraising.contributions-edit', compact('fundraising', 'contribution', 'contributors', 'paymentMethods', 'receiptIssuers', 'members', 'membersLite', 'memberSummaries', 'fundingSource', 'internalPaymentMethodId'));
    }

    public function contributionsUpdate(Request $request, $id, $contributionId)
    {
        $fundraising = Fundraising::findOrFail($id);
        $contribution = $fundraising->contributions()->where('id', $contributionId)->firstOrFail();

        $validated = $request->validate([
            'member_id' => 'nullable|integer|exists:members,id|required_if:funding_source,savings_transfer',
            'funding_source' => 'required|in:deposit,savings_transfer',
            'contributor_name' => 'required|string|max:255',
            'contributor_email' => 'nullable|email|max:255',
            'contributor_phone' => 'nullable|string|max:50',
            'contributor_address' => 'nullable|string|max:2000',
            'is_anonymous' => 'nullable|boolean',
            'amount' => 'required|numeric|min:100',
            'contribution_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_issued' => 'nullable|boolean',
            'receipt_issued_at' => 'nullable|date',
            'receipt_issued_by' => 'nullable|exists:users,id',
            'thank_you_sent' => 'nullable|boolean',
            'thank_you_sent_at' => 'nullable|date',
            'message' => 'nullable|string|max:5000',
            'is_public_message' => 'nullable|boolean',
            'notes' => 'nullable|string|max:5000',
        ]);

        $oldAmount = $contribution->amount;
        $receiptIssued = !empty($validated['receipt_issued']);
        $thankYouSent = !empty($validated['thank_you_sent']);

        $memberId = null;
        if ($validated['funding_source'] === 'savings_transfer') {
            $memberId = !empty($validated['member_id']) ? (int) $validated['member_id'] : null;
            if (!$memberId) {
                return back()->withErrors(['member_id' => 'Member is required for savings transfer.'])->withInput();
            }
        } else {
            if (!empty($validated['member_id'])) {
                $memberId = (int) $validated['member_id'];
            } elseif (!empty($validated['contributor_email'])) {
                $memberId = Member::query()->where('email', $validated['contributor_email'])->value('id');
            } elseif (!empty($validated['contributor_phone'])) {
                $memberId = Member::query()->where('primary_phone', $validated['contributor_phone'])->value('id');
            } else {
                $memberId = Member::query()->where('full_name', $validated['contributor_name'])->value('id');
            }
            if (!$memberId) {
                $memberId = $this->getExternalContributorMemberId();
            }
        }

        if ($validated['funding_source'] === 'savings_transfer') {
            $member = Member::find($memberId);
            if (!$member) {
                return back()->withErrors(['member_id' => 'Selected member not found.'])->withInput();
            }
            $availableSavings = (float) ($member->savings_balance ?? 0);
            if ($availableSavings <= 0 && isset($member->savings)) {
                $availableSavings = (float) ($member->savings ?? 0);
            }
            if ($member && $availableSavings < $validated['amount']) {
                return back()->withErrors(['amount' => 'Insufficient savings balance for this transfer.'])->withInput();
            }
        }

        $paymentMethodId = $validated['payment_method_id'];
        if ($validated['funding_source'] === 'savings_transfer') {
            $internalMethod = PaymentMethod::query()->where('name', 'internal')->value('id');
            if ($internalMethod) {
                $paymentMethodId = $internalMethod;
            }
        }

        $receiptNumber = $validated['receipt_number'] ?? null;
        if (empty($receiptNumber)) {
            $receiptNumber = 'RCPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        }

        $contribution->update([
            'member_id' => $memberId,
            'contributor_name' => $validated['contributor_name'],
            'contributor_email' => $validated['contributor_email'] ?? null,
            'contributor_phone' => $validated['contributor_phone'] ?? null,
            'contributor_address' => $validated['contributor_address'] ?? null,
            'is_anonymous' => !empty($validated['is_anonymous']),
            'amount' => $validated['amount'],
            'contribution_date' => $validated['contribution_date'],
            'payment_method_id' => $paymentMethodId,
            'receipt_number' => $receiptNumber,
            'receipt_issued' => $receiptIssued,
            'receipt_issued_at' => $receiptIssued ? ($validated['receipt_issued_at'] ?? $contribution->receipt_issued_at ?? now()) : null,
            'receipt_issued_by' => $receiptIssued ? ($validated['receipt_issued_by'] ?? $contribution->receipt_issued_by ?? auth()->id()) : null,
            'thank_you_sent' => $thankYouSent,
            'thank_you_sent_at' => $thankYouSent ? ($validated['thank_you_sent_at'] ?? $contribution->thank_you_sent_at ?? now()) : null,
            'message' => $validated['message'] ?? null,
            'is_public_message' => !empty($validated['is_public_message']),
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($oldAmount != $validated['amount']) {
            $fundraising->raised_amount = max(0, $fundraising->raised_amount - $oldAmount + $validated['amount']);
            $fundraising->save();
        }

        if ($contribution->transaction) {
            $transactionTypeName = $validated['funding_source'] === 'savings_transfer' ? 'transfer' : 'deposit';
            $transactionTypeId = TransactionType::query()->where('name', $transactionTypeName)->value('id')
                ?? TransactionType::query()->value('id');
            if ($validated['funding_source'] === 'savings_transfer') {
                $categoryId = TransactionCategory::query()->where('name', 'fundraising_transfer')->value('id')
                    ?? TransactionCategory::query()->where('name', 'transfer_out')->value('id')
                    ?? TransactionCategory::query()->where('name', 'savings_withdrawal')->value('id')
                    ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
                    ?? TransactionCategory::query()->value('id');
            } else {
                $categoryId = TransactionCategory::query()->where('name', 'fundraising_deposit')->value('id');
                if (!$categoryId) {
                    return back()
                        ->withErrors(['funding_source' => 'Fundraising deposit category is not configured.'])
                        ->withInput();
                }
            }
            $paymentMethodId = $validated['payment_method_id'];
            if ($validated['funding_source'] === 'savings_transfer') {
                $internalMethod = PaymentMethod::query()->where('name', 'internal')->value('id');
                if ($internalMethod) {
                    $paymentMethodId = $internalMethod;
                }
            }

            $contribution->transaction->update([
                'member_id' => $memberId,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'amount' => $validated['amount'],
                'net_amount' => $validated['amount'],
                'payment_method_id' => $paymentMethodId,
                'transaction_date' => $validated['contribution_date'],
                'value_date' => $validated['contribution_date'],
                'notes' => $validated['notes'] ?? null,
                'metadata' => $validated['funding_source'] === 'savings_transfer'
                    ? array_merge((array) ($contribution->transaction->metadata ?? []), ['affects_savings' => true, 'campaign_id' => $fundraising->id])
                    : array_merge((array) ($contribution->transaction->metadata ?? []), ['campaign_id' => $fundraising->id]),
            ]);

            if ($validated['funding_source'] === 'savings_transfer') {
                app(\App\Services\Financial\TransactionPostingService::class)
                    ->applyCategoryUpdates($contribution->transaction->fresh(), ['metadata' => ['affects_savings' => true, 'campaign_id' => $fundraising->id]]);
            }
        }

        return redirect()
            ->route('admin.fundraising.contributions.show', [$fundraising->id, $contribution->id])
            ->with('success', 'Contribution updated successfully');
    }

    public function contributionsDestroy($id, $contributionId)
    {
        $fundraising = Fundraising::findOrFail($id);
        $contribution = $fundraising->contributions()->where('id', $contributionId)->firstOrFail();

        $fundraising->raised_amount = max(0, $fundraising->raised_amount - $contribution->amount);
        $fundraising->save();

        $transaction = $contribution->transaction;
        $contribution->delete();
        if ($transaction) {
            $transaction->delete();
        }

        return redirect()
            ->route('admin.fundraising.contributions', $fundraising->id)
            ->with('success', 'Contribution deleted successfully');
    }

    public function contributionsPrint($id, $contributionId)
    {
        $fundraising = Fundraising::findOrFail($id);
        $contribution = $fundraising->contributions()
            ->with(['member', 'paymentMethod', 'receiptIssuer', 'transaction.transactionType'])
            ->where('id', $contributionId)
            ->firstOrFail();

        return view('admin.fundraising.contributions-print', compact('fundraising', 'contribution'));
    }

    private function getExternalContributorMemberId(): int
    {
        $member = Member::query()->where('member_number', 'EXT0001')->first();
        if ($member) {
            return (int) $member->id;
        }

        $user = User::query()->where('username', 'external_contributor')->first();
        if (!$user) {
            $roleId = \App\Models\Role::query()->where('name', 'client')->value('id')
                ?? \App\Models\Role::query()->value('id');
            $user = User::create([
                'username' => 'external_contributor',
                'email' => 'external_contributor@bss.local',
                'password' => bcrypt(Str::random(16)),
                'role_id' => $roleId,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);
        }

        if ($user->member) {
            return (int) $user->member->id;
        }

        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'EXT0001',
            'first_name' => 'External',
            'last_name' => 'Contributor',
            'join_date' => now()->toDateString(),
            'created_by' => auth()->id(),
        ]);

        return (int) $member->id;
    }

    public function edit($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        return view('admin.fundraising.edit', compact('fundraising'));
    }

    public function update(Request $request, $id)
    {
        $fundraising = Fundraising::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $validated['status_id'] = $this->resolveFundraisingStatusId($validated['status']) ?? $fundraising->status_id;
        unset($validated['status']);

        $fundraising->update($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign updated successfully');
    }

    public function destroy($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        $fundraising->delete();

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign deleted successfully');
    }

    private function resolveFundraisingStatusId(string $name): ?int
    {
        $statusId = FundraisingStatus::query()->where('name', $name)->value('id');
        if ($statusId) {
            return (int) $statusId;
        }

        $fallbackId = FundraisingStatus::query()->value('id');
        if ($fallbackId) {
            return (int) $fallbackId;
        }

        $defaults = [
            [
                'name' => 'active',
                'display_name' => 'Active',
                'description' => 'Active fundraising campaign',
                'color' => 'success',
            ],
            [
                'name' => 'completed',
                'display_name' => 'Completed',
                'description' => 'Completed fundraising campaign',
                'color' => 'secondary',
            ],
            [
                'name' => 'cancelled',
                'display_name' => 'Cancelled',
                'description' => 'Cancelled fundraising campaign',
                'color' => 'danger',
            ],
        ];

        foreach ($defaults as $row) {
            FundraisingStatus::query()->firstOrCreate(
                ['name' => $row['name']],
                $row
            );
        }

        $statusId = FundraisingStatus::query()->where('name', $name)->value('id')
            ?? FundraisingStatus::query()->value('id');

        return $statusId ? (int) $statusId : null;
    }
}
