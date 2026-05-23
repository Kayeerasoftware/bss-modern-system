<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\LoanApplication;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\ProjectStatus;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\TransactionCategory;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\DashboardStatsService;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function getData(Request $request)
    {
        $role = $request->get('role', 'client');

        $cacheKey = 'dashboard:get_data:v1:'.$role;

        $data = Cache::remember($cacheKey, now()->addSeconds(30), static function () {
            $viewStats = app(DashboardStatsService::class)->get();
            $members = Member::query()
                ->select('id', 'member_number', 'full_name', 'email', 'membership_status', 'created_at')
                ->get()
                ->each
                ->append(['member_id', 'role', 'status', 'savings', 'balance', 'savings_balance']);

            $totalSavings = (float) ($viewStats['total_system_balance'] ?? DB::table('savings_accounts')->sum('current_balance'));

            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
            $disbursedStatusId = LoanStatus::query()->where('name', 'disbursed')->value('id');
            $activeLoanStatusIds = array_values(array_filter([$approvedStatusId, $disbursedStatusId]));
            $activeLoansQuery = Loan::query();
            if (!empty($activeLoanStatusIds)) {
                $activeLoansQuery->whereIn('status_id', $activeLoanStatusIds);
            }

            $activeLoansCount = (int) ($viewStats['active_loans_count'] ?? $activeLoansQuery->count());
            $totalLoanAmount = (float) ($viewStats['total_active_loans'] ?? $activeLoansQuery->sum('principal_amount'));

            $recentTransactions = Transaction::query()
                ->with('member')
                ->latest()
                ->take(5)
                ->get();

            $projects = Project::query()
                ->select('id', 'project_number', 'name', 'budget_amount', 'expected_end_date', 'status_id', 'progress_percentage', 'actual_roi', 'created_at')
                ->get()
                ->each
                ->append(['project_id', 'budget', 'status', 'progress', 'roi']);

            $pendingLoans = Loan::query()
                ->whereHas('statusRelation', fn ($q) => $q->where('name', 'pending'))
                ->with('member')
                ->get();

            $loans = Loan::query()
                ->with('member')
                ->get();

            return [
                'members' => $members,
                'total_savings' => $totalSavings,
                'totalSavings' => $totalSavings,
                'active_loans' => $activeLoansCount,
                'total_loan_amount' => $totalLoanAmount,
                'totalLoans' => $totalLoanAmount,
                'recent_transactions' => $recentTransactions,
                'projects' => $projects,
                'pending_loans' => $pendingLoans,
                'loans' => $loans,
                'condolenceFund' => $totalSavings * 0.05,
            ];
        });

        return response()->json($data);
    }

    public function applyLoan(Request $request)
    {
        if ($request->filled('duration_months') && !$request->filled('repayment_months')) {
            $request->merge(['repayment_months' => $request->input('duration_months')]);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'purpose' => 'required|string|max:255',
            'repayment_months' => 'required|integer|min:1|max:60'
        ]);

        $user = auth()->user();
        $member = Member::where('email', $user->email)->first();
        
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $interestRate = 10;
        $interest = $validated['amount'] * ($interestRate / 100) * ($validated['repayment_months'] / 12);
        $monthlyPayment = ($validated['amount'] + $interest) / $validated['repayment_months'];

        $loanTypeId = LoanType::query()
            ->where('is_active', 1)
            ->value('id') ?? LoanType::query()->value('id');

        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');

        $application = LoanApplication::create([
            'member_id' => $member->id,
            'loan_type_id' => $loanTypeId,
            'requested_amount' => $validated['amount'],
            'requested_tenure_months' => $validated['repayment_months'],
            'purpose' => $validated['purpose'],
            'monthly_income' => null,
            'status_id' => $pendingStatusId,
            'submission_date' => now(),
        ]);

        return response()->json(['success' => true, 'application' => $application]);
    }

    public function approveLoan($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $loan->update([
            'status_id' => $approvedStatusId,
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function recordTransaction(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|in:deposit,withdrawal',
            'description' => 'required|string'
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Invalid member'], 422);
        }

        $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id');
        $completedStatusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $categoryName = $validated['type'] === 'deposit' ? 'savings_deposit' : 'savings_withdrawal';
        $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $member = Member::find($memberId);
        $balanceBefore = (float) ($member?->balance ?? 0);
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
                $memberId,
                $transactionTypeId,
                $categoryId,
                $completedStatusId,
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
                    'member_id' => $memberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $completedStatusId,
                    'amount' => $validated['amount'],
                    'net_amount' => $netAmount,
                    'fee' => $withdrawalFee,
                    'currency_id' => $currencyId,
                    'payment_method_id' => $paymentMethodId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $validated['description'],
                    'reference_number' => 'TXN' . time(),
                    'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
                    'processed_at' => now(),
                    'transaction_date' => now(),
                    'value_date' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $validated);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    public function createProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $planningStatusId = ProjectStatus::query()->where('name', 'planning')->value('id');

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'budget_amount' => $validated['budget'],
            'start_date' => $validated['start_date'],
            'expected_end_date' => $validated['end_date'],
            'status_id' => $planningStatusId,
            'progress_percentage' => 0
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }
}
