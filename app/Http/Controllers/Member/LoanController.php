<?php

namespace App\Http\Controllers\Member;

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
    private function resolveLoanSettings(): array
    {
        return [
            'default_interest_rate' => (float) setting('default_interest_rate', 10),
            'min_interest_rate' => (float) setting('min_interest_rate', 0),
            'max_interest_rate' => (float) setting('max_interest_rate', 100),
            'min_loan_amount' => (float) setting('min_loan_amount', 10000),
            'max_loan_amount' => (float) setting('max_loan_amount', 10000000),
            'min_repayment_months' => (int) setting('min_repayment_months', 3),
            'max_repayment_months' => (int) setting('max_repayment_months', 60),
            'default_repayment_months' => (int) setting('default_repayment_months', 12),
        ];
    }

    public function apply()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        $loanSettings = $this->resolveLoanSettings();
        
        return view('member.loans.apply', compact('member', 'loanSettings'));
    }

    public function myLoans(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
        $query = Loan::where('member_id', $member->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('principal_amount', 'like', "%{$search}%");
            });
        }

        $query->status($request->status);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->latest()->paginate(10)->appends($request->query());
        
        return view('member.loans.my-loans', compact('loans', 'member'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();
        
        return view('member.loans.show', compact('loan', 'member'));
    }

    public function store(Request $request)
    {
        $repaymentMonths = (int) ($request->repayment_months ?? $request->duration ?? 0);
        $loanSettings = $this->resolveLoanSettings();

        if (!$request->filled('amount') && $request->filled('amount_display')) {
            $normalized = preg_replace('/[^\d.]/', '', (string) $request->amount_display);
            $request->merge(['amount' => $normalized]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . $loanSettings['min_loan_amount'] . '|max:' . $loanSettings['max_loan_amount'],
            'purpose' => 'required|string',
            'repayment_months' => 'nullable|integer|min:' . $loanSettings['min_repayment_months'] . '|max:' . $loanSettings['max_repayment_months'],
            'duration' => 'nullable|integer|min:' . $loanSettings['min_repayment_months'] . '|max:' . $loanSettings['max_repayment_months'],
        ]);
        if ($repaymentMonths < 1) {
            return back()->withErrors(['repayment_months' => 'Repayment months is required.'])->withInput();
        }

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return back()->withErrors(['error' => 'Member profile not found'])->withInput();
        }

        $loanTypeId = LoanType::query()->where('is_active', 1)->value('id') ?? LoanType::query()->value('id');
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
        $interestRate = $loanSettings['default_interest_rate'];
        $interest = $member->calculateInterest($request->amount, $repaymentMonths, $interestRate);
        $processingFeeRate = (float) setting('processing_fee_percentage', 2);
        $processingFee = (float) $request->amount * ($processingFeeRate / 100);

        DB::transaction(function () use (
            $member,
            $request,
            $loanTypeId,
            $pendingStatusId,
            $interestRate,
            $interest,
            $processingFee,
            $repaymentMonths
        ): void {
            $application = LoanApplication::create([
                'member_id' => $member->id,
                'loan_type_id' => $loanTypeId,
                'requested_amount' => $request->amount,
                'requested_tenure_months' => $repaymentMonths,
                'purpose' => $request->purpose,
                'monthly_income' => $request->monthly_income ?? null,
                'status_id' => $pendingStatusId,
                'submission_date' => now(),
            ]);

            $loan = Loan::create([
                'application_id' => $application->id,
                'member_id' => $member->id,
                'loan_type_id' => $loanTypeId,
                'principal_amount' => $request->amount,
                'interest_rate' => $interestRate,
                'total_interest' => round($interest, 2),
                'repayment_months' => $repaymentMonths,
                'processing_fee' => round($processingFee, 2),
                'application_date' => now(),
                'status_id' => $pendingStatusId,
                'notes' => $request->purpose,
            ]);
            $application->update(['converted_to_loan_id' => $loan->id]);
        });

        return redirect()->route('member.loans.my-loans')->with('success', 'Loan application submitted successfully');
    }

    public function repay($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();
        
        return view('member.loans.repay', compact('loan', 'member'));
    }

    public function storeRepayment(Request $request, $id, TransactionPostingService $postingService)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return back()->withErrors(['error' => 'Member profile not found'])->withInput();
        }

        $loan = Loan::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        $maxPayable = max((float) ($loan->remaining_balance ?? 0), 1);
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $maxPayable,
        ]);

        $transactionTypeId = TransactionType::query()->where('name', 'loan_payment')->value('id')
            ?? TransactionType::query()->where('name', 'loan_repayment')->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'loan_payment')->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        try {
            DB::transaction(function () use (
                $loan,
                $member,
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
                    'payment_method_id' => $paymentMethodId,
                    'description' => 'Loan payment for loan #' . $loan->loan_id,
                    'metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => (float) $request->amount],
                    'transaction_date' => now(),
                    'value_date' => now(),
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, ['metadata' => ['loan_id' => $loan->id, 'loan_applied_amount' => (float) $request->amount]]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('member.loans.show', $id)->with('success', 'Repayment recorded successfully');
    }
}
