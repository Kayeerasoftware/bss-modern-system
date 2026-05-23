<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanStatus;
use App\Models\Member;
use App\Models\LoanSetting;
use App\Models\LoanType;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoanController extends Controller
{
    private function getLoanSettings(): LoanSetting
    {
        if (!Schema::hasTable('loan_settings')) {
            return new LoanSetting($this->defaultLoanSettingsData());
        }

        return Cache::remember('loan_settings:default:v1', now()->addMinutes(5), function () {
            return LoanSetting::firstOrCreate([], $this->defaultLoanSettingsData());
        });
    }

    private function defaultLoanSettingsData(): array
    {
        return [
            'is_loan_available' => (bool) setting('is_loan_available', true),
            'default_interest_rate' => (float) setting('default_interest_rate', 10),
            'min_interest_rate' => (float) setting('min_interest_rate', 5),
            'max_interest_rate' => (float) setting('max_interest_rate', 30),
            'min_loan_amount' => (float) setting('min_loan_amount', 10000),
            'max_loan_amount' => (float) setting('max_loan_amount', 10000000),
            'max_loan_to_savings_ratio' => (float) setting('max_loan_to_savings_ratio', 300),
            'min_repayment_months' => (int) setting('min_repayment_months', 3),
            'max_repayment_months' => (int) setting('max_repayment_months', 60),
            'default_repayment_months' => (int) setting('default_repayment_months', 12),
            'processing_fee_percentage' => (float) setting('processing_fee_percentage', 2),
            'late_payment_penalty' => (float) setting('late_payment_penalty', 5),
            'grace_period_days' => (int) setting('grace_period_days', 7),
            'auto_approve_amount' => (float) setting('auto_approve_amount', 0),
            'require_guarantors' => (bool) setting('require_guarantors', false),
            'guarantors_required' => (int) setting('guarantors_required', 2),
            'email_notifications' => (bool) setting('email_notifications', true),
            'sms_notifications' => (bool) setting('sms_notifications', true),
            'payment_reminder_days' => (int) setting('payment_reminder_days', 3),
        ];
    }

    private function getLoanMembers()
    {
        return Cache::remember('loan_form:members:v1', now()->addMinutes(2), static function () {
            return Member::query()
                ->select('id', 'member_number', 'full_name', 'email', 'primary_phone', 'membership_status')
                ->orderBy('full_name')
                ->get()
                ->each
                ->append(['member_id', 'contact', 'savings', 'balance', 'status', 'loan']);
        });
    }

    public function index(Request $request)
    {
        $query = LoanApplication::with(['member', 'statusRelation']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('application_number', 'like', "%{$request->search}%")
                    ->orWhere('purpose', 'like', "%{$request->search}%")
                    ->orWhereHas('member', function($q) use ($request) {
                        $q->where('full_name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->status) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
        }

        if ($request->amount_min) {
            $query->where('requested_amount', '>=', $request->amount_min);
        }

        if ($request->amount_max) {
            $query->where('requested_amount', '<=', $request->amount_max);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->sort) {
            switch ($request->sort) {
                case 'amount_high':
                    $query->orderBy('requested_amount', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('requested_amount', 'asc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $applications = $query->paginate(15);

        return view('cashier.loans.index', compact('applications'));
    }

    public function create()
    {
        $settings = $this->getLoanSettings();
        
        if (!$settings->is_loan_available) {
            return redirect()->route('cashier.loans.index')->with('error', 'Loan applications are currently disabled.');
        }
        
        $members = $this->getLoanMembers();
        return view('cashier.loans.create', compact('members', 'settings'));
    }

    public function store(Request $request)
    {
        $settings = $this->getLoanSettings();
        
        if (!$settings->is_loan_available) {
            return redirect()->route('cashier.loans.index')->with('error', 'Loan applications are currently disabled.');
        }

        if (!$request->filled('amount') && $request->filled('amount_display')) {
            $normalized = preg_replace('/[^\d.]/', '', (string) $request->amount_display);
            $request->merge(['amount' => $normalized]);
        }
        
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:' . ($settings->min_loan_amount ?? 0) . '|max:' . ($settings->max_loan_amount ?? 999999999),
            'purpose' => 'required|string',
            'repayment_months' => 'required|integer|min:' . ($settings->min_repayment_months ?? 1) . '|max:' . ($settings->max_repayment_months ?? 120),
            'interest_rate' => 'nullable|numeric',
            'applicant_comment' => 'nullable|string',
            'guarantor_1_name' => $settings->require_guarantors ? 'required|string' : 'nullable|string',
            'guarantor_1_phone' => $settings->require_guarantors ? 'required|string' : 'nullable|string',
            'guarantor_2_name' => ($settings->require_guarantors && $settings->guarantors_required >= 2) ? 'required|string' : 'nullable|string',
            'guarantor_2_phone' => ($settings->require_guarantors && $settings->guarantors_required >= 2) ? 'required|string' : 'nullable|string',
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $loanTypeId = LoanType::query()->where('is_active', 1)->value('id') ?? LoanType::query()->value('id');
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');

        $minRate = (float) ($settings->min_interest_rate ?? 0);
        $maxRate = (float) ($settings->max_interest_rate ?? 100);
        $interestRate = (float) ($validated['interest_rate'] ?? $settings->default_interest_rate ?? 0);
        $interestRate = max($minRate, min($interestRate, $maxRate));
        $interest = $validated['amount'] * ($interestRate / 100) * ($validated['repayment_months'] / 12);
        $processingFee = $validated['amount'] * ($settings->processing_fee_percentage / 100);

        $statusId = $pendingStatusId;
        
        // Auto-approve if below threshold
        if ($settings->auto_approve_amount > 0 && $validated['amount'] <= $settings->auto_approve_amount) {
            $statusId = $approvedStatusId ?? $pendingStatusId;
        }

        $loanApplication = LoanApplication::create([
            'member_id' => $memberId,
            'loan_type_id' => $loanTypeId,
            'requested_amount' => $validated['amount'],
            'requested_tenure_months' => $validated['repayment_months'],
            'purpose' => $validated['purpose'],
            'applicant_comment' => $validated['applicant_comment'] ?? null,
            'status_id' => $statusId,
            'submission_date' => now(),
        ]);

        $loan = Loan::create([
            'application_id' => $loanApplication->id,
            'member_id' => $memberId,
            'loan_type_id' => $loanTypeId,
            'principal_amount' => $validated['amount'],
            'interest_rate' => $interestRate,
            'total_interest' => $interest,
            'repayment_months' => $validated['repayment_months'],
            'processing_fee' => $processingFee,
            'application_date' => now(),
            'status_id' => $statusId,
            'notes' => $validated['purpose'],
        ]);
        $loanApplication->update(['converted_to_loan_id' => $loan->id]);

        return redirect()->route('cashier.loans.index')->with('success', 'Loan created successfully');
    }

    public function show($id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        return view('cashier.loans.show', compact('loan'));
    }

    public function printPdf($id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        $pdf = \PDF::loadView('cashier.loans.pdf', compact('loan'));
        return $pdf->download('loan-application-' . $loan->loan_number . '.pdf');
    }

    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $members = $this->getLoanMembers();
        $settings = $this->getLoanSettings();
        return view('cashier.loans.edit', compact('loan', 'members', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $settings = $this->getLoanSettings();

        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'repayment_months' => 'required|integer|min:1',
            'status' => 'required|in:pending,approved,rejected,disbursed',
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $statusId = LoanStatus::query()->where('name', $validated['status'])->value('id');

        $interestRate = $loan->interest_rate ?? $settings->default_interest_rate ?? 10;
        $interest = $validated['amount'] * ($interestRate / 100) * ($validated['repayment_months'] / 12);

        $loan->update([
            'member_id' => $memberId,
            'principal_amount' => $validated['amount'],
            'repayment_months' => $validated['repayment_months'],
            'total_interest' => $interest,
            'status_id' => $statusId,
            'notes' => $validated['purpose'],
        ]);

        return redirect()->route('cashier.loans.index')->with('success', 'Loan updated successfully');
    }

    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return redirect()->route('cashier.loans.index')->with('success', 'Loan deleted successfully');
    }

    public function applications()
    {
        $applications = LoanApplication::whereHas('statusRelation', fn ($q) => $q->where('name', 'pending'))
            ->with('member')
            ->latest()
            ->paginate(15);
        return view('cashier.loans.applications', compact('applications'));
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $loan->update(['status_id' => $approvedStatusId ?? $loan->status_id]);

        if ($loan->application_id) {
            LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $approvedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }

        return redirect()->back()->with('success', 'Loan approved successfully');
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
        $loan->update(['status_id' => $rejectedStatusId ?? $loan->status_id]);

        if ($loan->application_id) {
            LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $rejectedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }

        return redirect()->back()->with('success', 'Loan rejected successfully');
    }

    public function disburse(Request $request, $id, TransactionPostingService $postingService)
    {
        $loan = Loan::with('member')->findOrFail($id);

        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $disbursedStatusId = LoanStatus::query()->where('name', 'disbursed')->value('id') ?? $approvedStatusId;

        if ($loan->status_id !== $approvedStatusId) {
            return redirect()->back()->withErrors(['loan' => 'Only approved loans can be disbursed.']);
        }

        if (!empty($loan->disbursement_transaction_id)) {
            return redirect()->back()->withErrors(['loan' => 'This loan has already been disbursed.']);
        }

        $validated = $request->validate([
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'disbursement_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        $transactionTypeId = TransactionType::query()->where('name', 'loan_disbursement')->value('id')
            ?? TransactionType::query()->where('name', 'deposit')->value('id')
            ?? TransactionType::query()->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'loan_disbursement')->value('id')
            ?? TransactionCategory::query()->where('name', 'savings_deposit')->value('id')
            ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
            ?? TransactionCategory::query()->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');
        $paymentMethodId = $validated['payment_method_id']
            ?? $loan->disbursement_method_id
            ?? PaymentMethod::query()->where('name', 'cash')->value('id')
            ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $amount = (float) ($loan->principal_amount ?? 0);
        $balanceBefore = (float) ($loan->member?->balance ?? 0);
        $balanceAfter = $balanceBefore + $amount;
        $disbursementDate = $validated['disbursement_date'] ?? now();

        $transaction = null;
        try {
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
                $validated,
                $disbursedStatusId,
                $postingService,
                &$transaction
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
                    'description' => $validated['description'] ?? ('Loan disbursement #' . $loan->loan_number),
                    'transaction_date' => $disbursementDate,
                    'value_date' => $disbursementDate,
                    'processed_by' => auth()->id(),
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
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['loan' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Loan disbursed successfully');
    }

    public function approvals()
    {
        $loans = Loan::whereHas('statusRelation', fn ($q) => $q->where('name', 'approved'))->with('member')->latest()->paginate(15);
        return view('cashier.loans.approvals', compact('loans'));
    }

    public function repayments()
    {
        $loans = Loan::whereHas('statusRelation', fn ($q) => $q->where('name', 'approved'))->with('member')->latest()->paginate(15);
        return view('cashier.loans.repayments', compact('loans'));
    }
}
