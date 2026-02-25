<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;
        
        $transactions = Transaction::where('member_id', $member->member_id ?? null)->get();
        
        $summary = [
            'total_income' => $transactions->where('type', 'deposit')->sum('amount'),
            'total_expenses' => $transactions->where('type', 'withdrawal')->sum('amount'),
            'net_balance' => $member->balance ?? 0,
            'total_transactions' => $transactions->count()
        ];
        
        $reports = [];
        
        return view('member.reports.index', compact('summary', 'reports'));
    }

    public function statement()
    {
        return view('member.transactions.statement');
    }

    public function loans()
    {
        return view('member.reports.loans');
    }

    public function tax()
    {
        return view('member.reports.tax');
    }
}
