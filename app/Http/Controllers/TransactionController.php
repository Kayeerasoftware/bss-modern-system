<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('member')->orderBy('created_at', 'desc')->get();
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $transaction = Transaction::create([
            'member_id' => $request->member_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'reference' => 'TXN' . time(),
            'status' => 'completed'
        ]);

        // Update member savings
        $member = Member::find($request->member_id);
        if ($request->type === 'deposit') {
            $member->increment('savings', $request->amount);
        } else {
            $member->decrement('savings', $request->amount);
        }

        return response()->json($transaction->load('member'));
    }

    public function getByMember($memberId)
    {
        $transactions = Transaction::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($transactions);
    }

    public function summary()
    {
        $summary = [
            'total_deposits' => Transaction::where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => Transaction::where('type', 'withdrawal')->sum('amount'),
            'total_transactions' => Transaction::count(),
            'recent_transactions' => Transaction::with('member')->latest()->take(10)->get()
        ];

        return response()->json($summary);
    }
}
