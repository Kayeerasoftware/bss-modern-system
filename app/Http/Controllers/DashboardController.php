<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function getData(Request $request)
    {
        $role = $request->get('role', 'client');

        $cacheKey = 'dashboard:get_data:v1:'.$role;

        $data = Cache::remember($cacheKey, now()->addSeconds(30), static function () {
            $members = Member::query()
                ->select('id', 'member_id', 'full_name', 'email', 'role', 'status', 'savings', 'balance', 'savings_balance', 'created_at')
                ->get();

            $memberSummary = Member::query()
                ->selectRaw('COALESCE(SUM(savings), 0) as total_savings')
                ->first();

            $loanSummary = Loan::query()
                ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as active_loans, COALESCE(SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END), 0) as total_loan_amount')
                ->first();

            $recentTransactions = Transaction::query()
                ->with('member')
                ->latest()
                ->take(5)
                ->get();

            $projects = Project::query()
                ->select('id', 'project_id', 'name', 'budget', 'timeline', 'status', 'progress', 'roi', 'created_at')
                ->get();

            $pendingLoans = Loan::query()
                ->where('status', 'pending')
                ->with('member')
                ->get();

            $loans = Loan::query()
                ->with('member')
                ->get();

            $totalSavings = (float) ($memberSummary->total_savings ?? 0);
            $totalLoanAmount = (float) ($loanSummary->total_loan_amount ?? 0);

            return [
                'members' => $members,
                'total_savings' => $totalSavings,
                'totalSavings' => $totalSavings,
                'active_loans' => (int) ($loanSummary->active_loans ?? 0),
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
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'purpose' => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1|max:60'
        ]);

        $user = auth()->user();
        $member = Member::where('email', $user->email)->first();
        
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $loan = Loan::create([
            'member_id' => $member->member_id,
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'duration_months' => $validated['duration_months'],
            'interest_rate' => 5,
            'status' => 'pending',
            'application_date' => now()
        ]);

        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function approveLoan($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $loan->update([
            'status' => 'active',
            'approved_date' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function recordTransaction(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|in:deposit,withdrawal',
            'description' => 'required|string'
        ]);

        $transaction = Transaction::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'reference' => 'TXN' . time(),
            'status' => 'completed'
        ]);

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

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'planning',
            'progress' => 0
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }
}
