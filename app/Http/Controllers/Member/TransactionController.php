<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function history(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $query = Transaction::where('member_id', $member->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->type) {
            $query->whereHas('transactionType', fn ($q) => $q->where('name', $request->type));
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $completedQuery = (clone $query)->whereHas('statusRelation', fn ($q) => $q->where('name', 'completed'));

        $summary = [
            'total_transactions' => (int) (clone $query)->count(),
            'completed_deposits' => (float) (clone $completedQuery)->whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->sum('amount'),
            'completed_withdrawals' => (float) (clone $completedQuery)->whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->sum('amount'),
            'completed_transfers' => (float) (clone $completedQuery)->whereHas('transactionType', fn ($q) => $q->where('name', 'transfer'))->sum('amount'),
            'pending_count' => (int) (clone $query)->whereHas('statusRelation', fn ($q) => $q->where('name', 'pending'))->count(),
        ];
        $summary['net_flow'] = $summary['completed_deposits'] - $summary['completed_withdrawals'] - $summary['completed_transfers'];
        
        $transactions = $query->latest()->paginate(20)->appends($request->query());
        
        return view('member.transactions.history', compact('transactions', 'member', 'summary'));
    }

    public function statement()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        return view('member.transactions.statement', compact('member'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $transaction = Transaction::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();
        
        return view('member.transactions.show', compact('transaction', 'member'));
    }

    public function generateStatement(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);
        
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $transactions = Transaction::where('member_id', $member->id)
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->latest()
            ->get();
        
        return view('member.transactions.statement', compact('transactions', 'member', 'request'));
    }
}
