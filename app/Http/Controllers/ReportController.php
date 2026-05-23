<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financialSummary(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $data = [
            'total_members' => Member::count(),
            'total_savings' => Member::transactionSavingsTotal(),
            'total_loans' => Member::sum('loan'),
            'pending_loans' => Loan::status('pending')->count(),
            'approved_loans' => Loan::status('approved')->count(),
            'monthly_transactions' => Transaction::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_projects' => Project::count(),
            'savings_growth' => $this->getSavingsGrowth(),
            'loan_distribution' => $this->getLoanDistribution(),
            'member_activity' => $this->getMemberActivity()
        ];

        return response()->json($data);
    }

    public function memberReport()
    {
        $members = Member::with(['loans', 'transactions'])
            ->withTransactionSavings()
            ->addSelect(DB::raw('(COALESCE(member_transaction_savings.transaction_savings, 0) - loan) as net_worth'))
            ->orderByRaw('COALESCE(member_transaction_savings.transaction_savings, 0) desc')
            ->get();

        return response()->json($members);
    }

    public function loanReport()
    {
        $loans = Loan::with('member')
            ->selectRaw('*, (amount + interest) as total_repayment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($loans);
    }

    private function getSavingsGrowth()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'amount' => Member::transactionSavingsTotal()
            ];
        }
        return $months;
    }

    private function getLoanDistribution()
    {
        return [
            'pending' => Loan::status('pending')->count(),
            'approved' => Loan::status('approved')->count(),
            'rejected' => Loan::status('rejected')->count()
        ];
    }

    private function getMemberActivity()
    {
        return [
            'active_this_month' => Transaction::whereMonth('created_at', Carbon::now()->month)->distinct('member_id')->count(),
            'new_members_this_month' => Member::whereMonth('created_at', Carbon::now()->month)->count(),
            'total_transactions_this_month' => Transaction::whereMonth('created_at', Carbon::now()->month)->count()
        ];
    }
}
