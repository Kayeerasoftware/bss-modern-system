<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\SavingsHistory;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Services\Financial\MemberFinancialSyncService;
use App\Services\Financial\SavingsReconciliationService;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientDashboardController extends Controller
{
    /**
     * Resolve member from authenticated user or request payload.
     */
    private function resolveMember(Request $request): ?Member
    {
        $user = Auth::user();

        if ($user) {
            $member = $user->member
                ?? Member::where('user_id', $user->id)->first()
                ?? Member::whereRaw('LOWER(email) = ?', [strtolower((string) $user->email)])->first();

            if ($member) {
                if (empty($member->user_id)) {
                    $member->forceFill(['user_id' => $user->id])->saveQuietly();
                }

                return $member;
            }
        }

        $memberId = (string) $request->input('member_id', '');
        if ($memberId !== '') {
            $resolvedMemberId = resolve_member_id($memberId);
            return Member::query()
                ->where('id', $resolvedMemberId ?? -1)
                ->orWhere('member_account_number', $memberId)
                ->orWhere('member_number', $memberId)
                ->first();
        }

        return null;
    }

    /**
     * Get client dashboard data
     */
    public function getClientData(Request $request)
    {
        try {
            $member = $this->resolveMember($request);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            // Get personal data
            $personalData = $this->getPersonalData($member);

            // Get analytics
            $analytics = $this->getAnalytics($member);

            // Get savings goals
            $savingsGoals = $this->getSavingsGoals($member);

            // Get monthly comparison
            $monthlyComparison = $this->getMonthlyComparison($member);

            // Get recent transactions
            $recentTransactions = $this->getRecentTransactions($member);
            $reconSnapshot = app(SavingsReconciliationService::class)->getMemberSnapshot($member->id);

            return response()->json([
                'success' => true,
                'memberData' => $member,
                'personalData' => $personalData,
                'analytics' => $analytics,
                'savingsGoals' => $savingsGoals,
                'monthlyComparison' => $monthlyComparison,
                'recentTransactions' => $recentTransactions,
                'savingsReconciliation' => $reconSnapshot
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member balance summary.
     */
    public function getBalance(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $summary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);

            $reconSnapshot = app(SavingsReconciliationService::class)->getMemberSnapshot($member->id);

            return response()->json([
                'success' => true,
                'member_id' => $member->member_id,
                'balance' => (float) $summary['available_balance'],
                'savings' => (float) $summary['net_savings'],
                'savings_balance' => (float) $summary['net_savings'],
                'loan_outstanding' => (float) $summary['loan_outstanding'],
                'available_funds' => (float) $summary['available_after_loans'],
                'total_deposits' => (float) $summary['total_deposits'],
                'total_withdrawals' => (float) $summary['total_withdrawals'],
                'total_transfers' => (float) $summary['total_transfers'],
                'total_loan_payments' => (float) $summary['total_loan_payments'],
                'savings_reconciliation' => $reconSnapshot,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member transactions.
     */
    public function getTransactions(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $query = Transaction::where('member_id', $member->id);

            if ($request->filled('type')) {
                $query->ofType($request->input('type'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            $perPage = (int) $request->input('per_page', 20);
            $transactions = $query->latest()->paginate(max(1, min($perPage, 100)));

            return response()->json([
                'success' => true,
                'member_id' => $member->member_id,
                'transactions' => $transactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member loans.
     */
    public function getLoans(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $loans = Loan::where('member_id', $member->id)
                ->latest()
                ->get()
                ->map(function ($loan) {
                    $paid = (float) ($loan->amount_paid ?? 0);
                    $amount = (float) ($loan->principal_amount ?? 0);
                    $outstanding = (float) ($loan->balance_due ?? max($amount - $paid, 0));
                    return [
                        'id' => $loan->id,
                        'loan_id' => $loan->loan_id,
                        'status' => $loan->status,
                        'amount' => $amount,
                        'paid_amount' => $paid,
                        'outstanding' => $outstanding,
                        'interest_rate' => (float) ($loan->interest_rate ?? 0),
                        'duration' => $loan->repayment_months,
                        'created_at' => $loan->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'member_id' => $member->member_id,
                'loans' => $loans,
                'summary' => [
                    'total' => $loans->count(),
                    'approved' => $loans->where('status', 'approved')->count(),
                    'pending' => $loans->where('status', 'pending')->count(),
                    'rejected' => $loans->where('status', 'rejected')->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading loans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit withdrawal request.
     */
    public function requestWithdrawal(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $request->validate([
                'amount' => 'required|numeric|min:1000',
                'description' => 'nullable|string|max:255',
            ]);

            $amount = (float) $request->input('amount');
            $summary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);
            $availableBalance = (float) ($summary['available_balance'] ?? 0);
            if ($amount > $availableBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 422);
            }

            $transactionTypeId = TransactionType::query()->where('name', 'withdrawal')->value('id');
            $statusId = TransactionStatus::query()->where('name', 'pending')->value('id');
            $categoryId = TransactionCategory::query()->where('name', 'savings_withdrawal')->value('id');
            $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
            $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

            $transaction = Transaction::create([
                'member_id' => $member->id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'status_id' => $statusId,
                'amount' => $amount,
                'net_amount' => $amount,
                'currency_id' => $currencyId,
                'payment_method_id' => $paymentMethodId,
                'description' => $request->input('description', 'Withdrawal request'),
                'transaction_date' => now(),
                'value_date' => now(),
                'processed_by' => Auth::id() ?? \App\Models\User::query()->value('id'),
                'processed_at' => now(),
                'balance_before' => (float) ($member->balance ?? 0),
                'balance_after' => (float) ($member->balance ?? 0),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing withdrawal request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get personal financial data
     */
    private function getPersonalData($member)
    {
        $summary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);

        // Calculate monthly deposits (last 30 days)
        $monthlyDeposits = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->where(function ($query): void {
                $query->ofStatus('completed')
                    ->orWhereNull('status_id');
            })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');

        return [
            'savings' => (float) $summary['net_savings'],
            'activeLoan' => (float) $summary['loan_outstanding'],
            'monthlyDeposits' => $monthlyDeposits,
            'balance' => (float) $summary['available_balance'],
        ];
    }

    /**
     * Get financial analytics
     */
    private function getAnalytics($member)
    {
        // Calculate savings growth rate
        $lastMonth = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(2),
                Carbon::now()->subMonths(1)
            ])
            ->sum('amount');

        $thisMonth = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->where('created_at', '>=', Carbon::now()->subMonths(1))
            ->sum('amount');

        $growthRate = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 12.5;

        // Calculate credit score (mock logic)
        $creditScore = $this->calculateCreditScore($member);

        // Calculate financial health
        $financialHealth = $this->calculateFinancialHealth($member, $growthRate);

        // Calculate predicted savings
        $predictedSavings = $this->calculatePredictedSavings($member, $growthRate);

        return [
            'savings_growth_rate' => round($growthRate, 1),
            'credit_score' => $creditScore,
            'financial_health' => $financialHealth,
            'predicted_savings' => $predictedSavings
        ];
    }

    /**
     * Calculate credit score based on member behavior
     */
    private function calculateCreditScore($member)
    {
        $baseScore = 600;

        // Add points for membership duration
        $monthsMember = $member->created_at->diffInMonths(Carbon::now());
        $score = $baseScore + ($monthsMember * 10);

        // Add points for consistent deposits
        $depositCount = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->count();
        $score += min($depositCount * 5, 100);

        // Subtract points for loan defaults
        $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
        $defaultedLoans = $rejectedStatusId
            ? Loan::where('member_id', $member->id)->where('status_id', $rejectedStatusId)->count()
            : 0;
        $score -= $defaultedLoans * 50;

        return max(300, min(850, $score));
    }

    /**
     * Calculate financial health score
     */
    private function calculateFinancialHealth($member, $growthRate)
    {
        $score = 50;
        $factors = [];

        // Base score for being a member
        $score += 20;
        $factors[] = 'Active membership';

        // Add points for positive savings
        $savings = $this->getPersonalData($member)['savings'];
        if ($savings > 100000) {
            $score += 20;
            $factors[] = 'Strong savings balance';
        } elseif ($savings > 50000) {
            $score += 10;
            $factors[] = 'Good savings balance';
        }

        // Add points for positive growth rate
        if ($growthRate > 5) {
            $score += 15;
            $factors[] = 'Positive growth rate';
        } elseif ($growthRate > 0) {
            $score += 5;
            $factors[] = 'Stable growth';
        }

        // Add points for no active loans
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $activeLoans = $approvedStatusId
            ? Loan::where('member_id', $member->id)->where('status_id', $approvedStatusId)->count()
            : 0;
        if ($activeLoans === 0) {
            $score += 15;
            $factors[] = 'No active debt';
        }

        // Add points for consistent transaction history
        $transactionCount = Transaction::where('member_id', $member->id)->count();
        if ($transactionCount > 10) {
            $score += 10;
            $factors[] = 'Active account usage';
        }

        // Determine rating
        if ($score >= 80) {
            $rating = 'Excellent';
        } elseif ($score >= 60) {
            $rating = 'Good';
        } elseif ($score >= 40) {
            $rating = 'Fair';
        } else {
            $rating = 'Poor';
        }

        return [
            'score' => $score,
            'rating' => $rating,
            'factors' => $factors
        ];
    }

    /**
     * Calculate predicted savings
     */
    private function calculatePredictedSavings($member, $growthRate)
    {
        $currentSavings = $this->getPersonalData($member)['savings'];

        // Calculate monthly growth amount
        $monthlyGrowth = $currentSavings * ($growthRate / 100);

        return [
            '3_months' => round($currentSavings + ($monthlyGrowth * 3)),
            '6_months' => round($currentSavings + ($monthlyGrowth * 6)),
            '12_months' => round($currentSavings + ($monthlyGrowth * 12))
        ];
    }

    /**
     * Get savings goals
     */
    private function getSavingsGoals($member)
    {
        // Mock savings goals - in a real system these would be stored in a goals table
        $currentSavings = $this->getPersonalData($member)['savings'];

        return [
            [
                'name' => 'Emergency Fund',
                'target' => 500000,
                'current' => min($currentSavings, 500000),
                'progress' => min(100, round(($currentSavings / 500000) * 100)),
                'deadline' => Carbon::now()->addMonths(6)->format('Y-m-d')
            ],
            [
                'name' => 'Investment Fund',
                'target' => 1000000,
                'current' => min($currentSavings, 1000000),
                'progress' => min(100, round(($currentSavings / 1000000) * 100)),
                'deadline' => Carbon::now()->addMonths(12)->format('Y-m-d')
            ]
        ];
    }

    /**
     * Get monthly comparison data
     */
    private function getMonthlyComparison($member)
    {
        $thisMonth = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $lastMonth = Transaction::where('member_id', $member->id)
            ->ofType('deposit')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $changePercent = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'change_percent' => round($changePercent, 1)
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions($member)
    {
        return Transaction::where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->created_at->format('Y-m-d'),
                    'type' => $transaction->type,
                    'amount' => $transaction->amount
                ];
            });
    }

    /**
     * Make a deposit
     */
    public function makeDeposit(Request $request, TransactionPostingService $postingService)
    {
        try {
            $request->validate([
                'member_id' => 'nullable|string',
                'amount' => 'required|numeric|min:1000',
                'description' => 'nullable|string|max:255'
            ]);

            $member = $this->resolveMember($request);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $transactionTypeId = TransactionType::query()->where('name', 'deposit')->value('id');
            $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
            $categoryId = TransactionCategory::query()->where('name', 'savings_deposit')->value('id');
            $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
            $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

            $balanceBefore = (float) ($member->balance ?? 0);
            $netAmount = (float) $request->amount;
            $balanceAfter = $balanceBefore + $netAmount;

            $transaction = null;
            DB::transaction(function () use (
                $member,
                $transactionTypeId,
                $statusId,
                $categoryId,
                $paymentMethodId,
                $currencyId,
                $balanceBefore,
                $balanceAfter,
                $netAmount,
                $request,
                &$transaction,
                $postingService
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $member->id,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $request->amount,
                    'net_amount' => $netAmount,
                    'currency_id' => $currencyId,
                    'payment_method_id' => $paymentMethodId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $request->description ?? 'Manual deposit',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'transaction_date' => now(),
                    'value_date' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $request->all());
            });

            $summary = app(MemberFinancialSyncService::class)->syncMember($member);

            return response()->json([
                'success' => true,
                'message' => 'Deposit successful',
                'transaction' => $transaction,
                'new_balance' => $summary['available_balance'],
                'new_savings' => $summary['net_savings'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing deposit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get savings history for chart
     */
    public function getSavingsHistory(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            $memberId = $member->id;
            $months = $request->input('months', 6);

            $history = [];
            $currentDate = Carbon::now();

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = $currentDate->copy()->subMonths($i);
                $monthStart = $month->startOfMonth();
                $monthEnd = $month->endOfMonth();

                $deposits = Transaction::where('member_id', $memberId)
                    ->ofType('deposit')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');

                $withdrawals = Transaction::where('member_id', $memberId)
                    ->ofType('withdrawal')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');

                $netSavings = $deposits - $withdrawals;

                $history[] = [
                    'month' => $month->format('M Y'),
                    'savings' => $netSavings,
                    'label' => $month->format('M')
                ];
            }

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading savings history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction distribution
     */
    public function getTransactionDistribution(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }
            $memberId = $member->id;

            $deposits = Transaction::where('member_id', $memberId)
                ->ofType('deposit')
                ->sum('amount');

            $withdrawals = Transaction::where('member_id', $memberId)
                ->ofType('withdrawal')
                ->sum('amount');

            $transfers = Transaction::where('member_id', $memberId)
                ->ofType('transfer')
                ->sum('amount');

            $total = $deposits + $withdrawals + $transfers;

            return response()->json([
                'success' => true,
                'distribution' => [
                    'deposits' => [
                        'amount' => $deposits,
                        'percentage' => $total > 0 ? round(($deposits / $total) * 100) : 0
                    ],
                    'withdrawals' => [
                        'amount' => $withdrawals,
                        'percentage' => $total > 0 ? round(($withdrawals / $total) * 100) : 0
                    ],
                    'transfers' => [
                        'amount' => $transfers,
                        'percentage' => $total > 0 ? round(($transfers / $total) * 100) : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction distribution: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get spending categories
     */
    public function getSpendingCategories(Request $request)
    {
        try {
            $member = $this->resolveMember($request);
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }
            $memberId = $member->id;

            // Mock spending categories based on transaction patterns
            $totalDeposits = Transaction::where('member_id', $memberId)
                ->ofType('deposit')
                ->sum('amount');

            $totalWithdrawals = Transaction::where('member_id', $memberId)
                ->ofType('withdrawal')
                ->sum('amount');

            if ($totalDeposits == 0) {
                return response()->json([
                    'success' => true,
                    'categories' => [
                        'savings' => ['percentage' => 100, 'amount' => 0],
                        'loans' => ['percentage' => 0, 'amount' => 0],
                        'transfers' => ['percentage' => 0, 'amount' => 0],
                        'fees' => ['percentage' => 0, 'amount' => 0]
                    ]
                ]);
            }

            // Calculate percentages based on typical patterns
            $savingsPercentage = 60;
            $loansPercentage = 20;
            $transfersPercentage = 15;
            $feesPercentage = 5;

            return response()->json([
                'success' => true,
                'categories' => [
                    'savings' => [
                        'percentage' => $savingsPercentage,
                        'amount' => round($totalDeposits * 0.6)
                    ],
                    'loans' => [
                        'percentage' => $loansPercentage,
                        'amount' => round($totalDeposits * 0.2)
                    ],
                    'transfers' => [
                        'percentage' => $transfersPercentage,
                        'amount' => round($totalDeposits * 0.15)
                    ],
                    'fees' => [
                        'percentage' => $feesPercentage,
                        'amount' => round($totalDeposits * 0.05)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading spending categories: ' . $e->getMessage()
            ], 500);
        }
    }
}
