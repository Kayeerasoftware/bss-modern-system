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
            return view('shareholder.transactions', compact('transactions'));
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

        $transactions = $query->latest('created_at')->paginate(20);

        return view('shareholder.transactions', compact('transactions'));
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
