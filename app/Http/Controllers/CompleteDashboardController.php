<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Http\Request;

class CompleteDashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        try {
            $role = $request->query('role', 'client');
            $year = $request->query('year', date('Y'));
            $allYears = $request->query('allYears', false);

            // Load data with error handling
            $members = Member::with('user')->get()->map(function($member) {
                return [
                    'id' => $member->id,
                    'member_id' => $member->member_id,
                    'full_name' => $member->full_name,
                    'email' => $member->email,
                    'contact' => $member->contact,
                    'location' => $member->location,
                    'occupation' => $member->occupation,
                    'role' => $member->role,
                    'savings' => $member->savings,
                    'loan' => $member->loan,
                    'savings_balance' => $member->savings_balance,
                    'profile_picture' => $member->profile_picture,
                    'created_at' => $member->created_at,
                    'user_id' => $member->user_id,
                    'user_role' => $member->user ? $member->user->role : null,
                    'user_status' => $member->user ? ($member->user->is_active ? 'active' : 'inactive') : null
                ];
            })->values();
            $loans = Loan::with('member')->get();
            $transactions = Transaction::with('member')->latest()->get();
            $projects = Project::all();

            // Calculate totals
            $totalSavings = $members->sum('savings');
            $totalLoans = $loans->where('status', 'approved')->sum('amount');
            $pendingLoans = $loans->where('status', 'pending')->values();

            // Get member growth data
            if ($allYears) {
                $membersGrowth = $this->getAllYearsGrowth();
                $savingsGrowth = $this->getAllYearsSavings();
            } else {
                $membersGrowth = $this->getMemberGrowthByYear($year);
                $savingsGrowth = $this->getSavingsGrowthByYear($year);
            }

            return response()->json([
                'success' => true,
                'totalSavings' => $totalSavings,
                'totalLoans' => $totalLoans,
                'members' => $members,
                'projects' => $projects,
                'pending_loans' => $pendingLoans->take(5),
                'recent_transactions' => $transactions,
                'membersGrowth' => $allYears ? [] : $membersGrowth,
                'savingsGrowth' => $allYears ? [] : $savingsGrowth,
                'allYearsGrowth' => $allYears ? $membersGrowth : [],
                'selectedYear' => $year,
                'stats' => [
                    'total_members' => $members->count(),
                    'active_projects' => $projects->where('progress', '<', 100)->count(),
                    'completed_projects' => $projects->where('progress', 100)->count(),
                    'total_transactions' => Transaction::count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error loading dashboard data'
            ], 500);
        }
    }

    private function getMemberGrowthByYear($year)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $growthData = [];
        
        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            $count = Member::whereYear('created_at', $year)
                          ->whereMonth('created_at', $monthNumber)
                          ->count();
            
            $growthData[] = [
                'month' => $month,
                'count' => $count
            ];
        }
        
        return $growthData;
    }

    private function getAllYearsGrowth()
    {
        $startYear = 2023;
        $endYear = 2033;
        $growthData = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $total = Member::whereYear('created_at', $year)->count();
            $totalSavings = Member::whereYear('created_at', '<=', $year)->sum('savings');
            $growthData[] = [
                'year' => (string)$year,
                'total' => $total,
                'total_savings' => $totalSavings
            ];
        }
        
        return $growthData;
    }

    private function getSavingsGrowthByYear($year)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $growthData = [];
        
        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            $total = Member::whereYear('created_at', '<=', $year)
                          ->where(function($query) use ($year, $monthNumber) {
                              $query->whereYear('created_at', '<', $year)
                                    ->orWhere(function($q) use ($year, $monthNumber) {
                                        $q->whereYear('created_at', $year)
                                          ->whereMonth('created_at', '<=', $monthNumber);
                                    });
                          })
                          ->sum('savings');
            
            $growthData[] = [
                'month' => $month,
                'total' => $total
            ];
        }
        
        return $growthData;
    }

    private function getAllYearsSavings()
    {
        $startYear = 2023;
        $endYear = 2033;
        $growthData = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $total = Member::whereYear('created_at', '<=', $year)->sum('savings');
            $growthData[] = [
                'year' => (string)$year,
                'total_savings' => $total
            ];
        }
        
        return $growthData;
    }
}
