<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            $transactions = collect();
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $summary = [
                'total_transactions' => 0,
                'completed_deposits' => 0,
                'completed_withdrawals' => 0,
                'completed_transfers' => 0,
                'pending_count' => 0,
                'net_flow' => 0,
            ];
            return view('shareholder.transactions', compact('transactions', 'summary'));
        }
        
        $query = Transaction::with('member')->where('member_id', $member->member_id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $completedQuery = (clone $query)->where(function ($q): void {
            $q->where('status', 'completed')
                ->orWhereNull('status');
        });

        $summary = [
            'total_transactions' => (int) (clone $query)->count(),
            'completed_deposits' => (float) (clone $completedQuery)->where('type', 'deposit')->sum('amount'),
            'completed_withdrawals' => (float) (clone $completedQuery)->where('type', 'withdrawal')->sum('amount'),
            'completed_transfers' => (float) (clone $completedQuery)->where('type', 'transfer')->sum('amount'),
            'pending_count' => (int) (clone $query)->where('status', 'pending')->count(),
        ];
        $summary['net_flow'] = $summary['completed_deposits'] - $summary['completed_withdrawals'] - $summary['completed_transfers'];

        $transactions = $query->latest('created_at')->paginate(20);

        return view('shareholder.transactions', compact('transactions', 'summary'));
    }

    public function create()
    {
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('shareholder.transactions')->with('error', 'Member profile not found');
        }
        
        $members = Member::where('member_id', $member->member_id)->get();
        return view('shareholder.transactions-create', compact('members', 'member'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'category' => 'required',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'completed';
        $validated['processed_by'] = auth()->id();
        $validated['completed_at'] = now();

        Transaction::create($validated);

        return redirect()->route('shareholder.transactions')->with('success', 'Transaction created successfully');
    }

    public function show($id)
    {
        $user = auth()->user();
        $member = \App\Models\Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) {
            abort(404);
        }
        
        $transaction = Transaction::with('member')->where('member_id', $member->member_id)->findOrFail($id);
        return view('shareholder.transactions-show', compact('transaction'));
    }
}
