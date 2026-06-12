<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoansController extends Controller
{
    private function resolveLoanSettings(): array
    {
        return [
            'default_interest_rate' => (float) setting('default_interest_rate', 10),
            'min_loan_amount' => (float) setting('min_loan_amount', 10000),
            'max_loan_amount' => (float) setting('max_loan_amount', 10000000),
            'min_repayment_months' => (int) setting('min_repayment_months', 3),
            'max_repayment_months' => (int) setting('max_repayment_months', 60),
            'default_repayment_months' => (int) setting('default_repayment_months', 12),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            return redirect()->route('shareholder.dashboard')->with('error', 'Member profile not found.');
        }
        
        $applicationsQuery = LoanApplication::with(['member', 'statusRelation'])
            ->where('member_id', $member->id);

        if ($request->filled('app_search')) {
            $search = $request->app_search;
            $applicationsQuery->where(function ($q) use ($search): void {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('requested_amount', 'like', "%{$search}%");
            });
        }

        if ($request->filled('app_status')) {
            $applicationsQuery->status($request->app_status);
        }

        if ($request->filled('app_date_from')) {
            $applicationsQuery->whereDate('created_at', '>=', $request->app_date_from);
        }

        if ($request->filled('app_date_to')) {
            $applicationsQuery->whereDate('created_at', '<=', $request->app_date_to);
        }

        switch ($request->app_sort) {
            case 'amount_high':
                $applicationsQuery->orderBy('requested_amount', 'desc');
                break;
            case 'amount_low':
                $applicationsQuery->orderBy('requested_amount', 'asc');
                break;
            case 'oldest':
                $applicationsQuery->oldest();
                break;
            default:
                $applicationsQuery->latest();
                break;
        }

        $applicationsPerPage = (int) $request->get('applications_per_page', 10);
        if (!in_array($applicationsPerPage, [5, 10, 15, 20, 50], true)) {
            $applicationsPerPage = 10;
        }

        $applications = $applicationsQuery
            ->paginate($applicationsPerPage, ['*'], 'applications_page')
            ->appends($request->except('applications_page'));

        $applicationStats = [
            'total' => LoanApplication::where('member_id', $member->id)->count(),
            'pending' => LoanApplication::where('member_id', $member->id)->status('pending')->count(),
            'approved' => LoanApplication::where('member_id', $member->id)->status('approved')->count(),
            'rejected' => LoanApplication::where('member_id', $member->id)->status('rejected')->count(),
        ];

        return view('shareholder.loans', compact('applications', 'applicationStats'));
    }
    
    public function show($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $loan = Loan::with('member')
            ->where('member_id', $member->id)
            ->findOrFail($id);
            
        return view('shareholder.loans.show', compact('loan'));
    }
    
    public function applications(Request $request)
    {
        $query = array_filter($request->query(), static fn ($value) => $value !== null && $value !== '');

        if (isset($query['search']) && !isset($query['app_search'])) {
            $query['app_search'] = $query['search'];
            unset($query['search']);
        }

        if (isset($query['status']) && !isset($query['app_status'])) {
            $query['app_status'] = $query['status'];
            unset($query['status']);
        }

        if (isset($query['date_from']) && !isset($query['app_date_from'])) {
            $query['app_date_from'] = $query['date_from'];
            unset($query['date_from']);
        }

        if (isset($query['date_to']) && !isset($query['app_date_to'])) {
            $query['app_date_to'] = $query['date_to'];
            unset($query['date_to']);
        }

        if (isset($query['sort']) && !isset($query['app_sort'])) {
            $query['app_sort'] = $query['sort'];
            unset($query['sort']);
        }

        if (isset($query['per_page']) && !isset($query['applications_per_page'])) {
            $query['applications_per_page'] = $query['per_page'];
            unset($query['per_page']);
        }

        $query['tab'] = 'applications';

        return redirect()->to(route('shareholder.loans', $query) . '#loan-applications');
    }
    
    public function apply()
    {
        $loanSettings = $this->resolveLoanSettings();

        return view('shareholder.loans.apply', compact('loanSettings'));
    }
    
    public function storeApplication(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        $loanSettings = $this->resolveLoanSettings();

        if (!$request->filled('amount') && $request->filled('amount_display')) {
            $normalized = preg_replace('/[^\d.]/', '', (string) $request->amount_display);
            $request->merge(['amount' => $normalized]);
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:' . $loanSettings['min_loan_amount'] . '|max:' . $loanSettings['max_loan_amount'],
            'purpose' => 'required|string|max:255',
            'duration' => 'required|integer|min:' . $loanSettings['min_repayment_months'] . '|max:' . $loanSettings['max_repayment_months'],
        ]);
        
        $loanTypeId = LoanType::query()->where('is_active', 1)->value('id') ?? LoanType::query()->value('id');
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
        $interestRate = $loanSettings['default_interest_rate'];
        $interest = (float) $request->amount * ($interestRate / 100) * ((int) $request->duration / 12);
        $processingFeeRate = (float) setting('processing_fee_percentage', 2);
        $processingFee = (float) $request->amount * ($processingFeeRate / 100);

        DB::transaction(function () use (
            $member,
            $request,
            $loanTypeId,
            $pendingStatusId,
            $interestRate,
            $interest,
            $processingFee
        ): void {
            $application = LoanApplication::create([
                'member_id' => $member->id,
                'loan_type_id' => $loanTypeId,
                'requested_amount' => $request->amount,
                'purpose' => $request->purpose,
                'requested_tenure_months' => $request->duration,
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
                'repayment_months' => (int) $request->duration,
                'processing_fee' => round($processingFee, 2),
                'application_date' => now(),
                'status_id' => $pendingStatusId,
                'notes' => $request->purpose,
            ]);
            $application->update(['converted_to_loan_id' => $loan->id]);
        });
        
        return redirect()->to(route('shareholder.loans', ['tab' => 'applications']) . '#loan-applications')
            ->with('success', 'Loan application submitted successfully.');
    }
    
    public function showApplication($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $application = LoanApplication::with(['member', 'statusRelation'])
            ->where('member_id', $member->id)
            ->findOrFail($id);
            
        return view('shareholder.loans.application-details', compact('application'));
    }
    
    public function makePayment(Request $request, $id, TransactionPostingService $postingService)
    {
        $user = auth()->user();
        $member = $user->member;
        
        $loan = Loan::where('member_id', $member->id)->findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $loan->balance_due,
        ]);

        $transactionTypeId = TransactionType::query()->where('name', 'loan_payment')->value('id')
            ?? TransactionType::query()->where('name', 'loan_repayment')->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'loan_payment')->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

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
        
        return response()->json([
            'success' => true,
            'message' => 'Payment made successfully',
        ]);
    }
}
