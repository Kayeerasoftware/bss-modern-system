<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\SavingsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientDashboardController extends Controller
{
    /**
     * Get client dashboard data
     */
    public function getClientData(Request $request)
    {
        try {
            // Get member data - assuming we're using member_id from auth or request
            $memberId = $request->input('member_id', 'MEM240001'); // Default test member

            $member = Member::where('member_id', $memberId)->first();

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

            return response()->json([
                'success' => true,
                'memberData' => $member,
                'personalData' => $personalData,
                'analytics' => $analytics,
                'savingsGoals' => $savingsGoals,
                'monthlyComparison' => $monthlyComparison,
                'recentTransactions' => $recentTransactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get personal financial data
     */
    private function getPersonalData($member)
    {
        // Calculate total savings from transactions
        $totalSavings = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->sum('amount');

        // Calculate total withdrawals
        $totalWithdrawals = Transaction::where('member_id', $member->member_id)
            ->where('type', 'withdrawal')
            ->sum('amount');

        // Calculate current balance
        $currentBalance = $totalSavings - $totalWithdrawals;

        // Get active loans
        $activeLoan = Loan::where('member_id', $member->member_id)
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate monthly deposits (last 30 days)
        $monthlyDeposits = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');

        return [
            'savings' => $totalSavings,
            'activeLoan' => $activeLoan,
            'monthlyDeposits' => $monthlyDeposits,
            'balance' => $currentBalance
        ];
    }

    /**
     * Get financial analytics
     */
    private function getAnalytics($member)
    {
        // Calculate savings growth rate
        $lastMonth = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(2),
                Carbon::now()->subMonths(1)
            ])
            ->sum('amount');

        $thisMonth = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
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
        $depositCount = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->count();
        $score += min($depositCount * 5, 100);

        // Subtract points for loan defaults
        $defaultedLoans = Loan::where('member_id', $member->member_id)
            ->where('status', 'rejected')
            ->count();
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
        $activeLoans = Loan::where('member_id', $member->member_id)
            ->where('status', 'approved')
            ->count();
        if ($activeLoans === 0) {
            $score += 15;
            $factors[] = 'No active debt';
        }

        // Add points for consistent transaction history
        $transactionCount = Transaction::where('member_id', $member->member_id)->count();
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
        $thisMonth = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $lastMonth = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
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
        return Transaction::where('member_id', $member->member_id)
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
    public function makeDeposit(Request $request)
    {
        try {
            $request->validate([
                'member_id' => 'required|string',
                'amount' => 'required|numeric|min:1000',
                'description' => 'nullable|string|max:255'
            ]);

            $member = Member::where('member_id', $request->member_id)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            // Create transaction
            $transaction = new Transaction();
            $transaction->member_id = $member->member_id;
            $transaction->amount = $request->amount;
            $transaction->type = 'deposit';
            $transaction->description = $request->description ?? 'Manual deposit';
            $transaction->save();

            // Update member savings
            $member->savings += $request->amount;
            $member->save();

            return response()->json([
                'success' => true,
                'message' => 'Deposit successful',
                'transaction' => $transaction,
                'new_balance' => $member->savings
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
            $memberId = $request->input('member_id', 'MEM240001');
            $months = $request->input('months', 6);

            $history = [];
            $currentDate = Carbon::now();

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = $currentDate->copy()->subMonths($i);
                $monthStart = $month->startOfMonth();
                $monthEnd = $month->endOfMonth();

                $deposits = Transaction::where('member_id', $memberId)
                    ->where('type', 'deposit')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');

                $withdrawals = Transaction::where('member_id', $memberId)
                    ->where('type', 'withdrawal')
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
            $memberId = $request->input('member_id', 'MEM240001');

            $deposits = Transaction::where('member_id', $memberId)
                ->where('type', 'deposit')
                ->sum('amount');

            $withdrawals = Transaction::where('member_id', $memberId)
                ->where('type', 'withdrawal')
                ->sum('amount');

            $transfers = Transaction::where('member_id', $memberId)
                ->where('type', 'transfer')
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
            $memberId = $request->input('member_id', 'MEM240001');

            // Mock spending categories based on transaction patterns
            $totalDeposits = Transaction::where('member_id', $memberId)
                ->where('type', 'deposit')
                ->sum('amount');

            $totalWithdrawals = Transaction::where('member_id', $memberId)
                ->where('type', 'withdrawal')
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
