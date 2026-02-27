<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('type', 'withdrawal')->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->latest()->paginate(20)->appends($request->query());
        return view('cashier.withdrawals.index', compact('withdrawals'));
    }

    public function create()
    {
        $members = Member::all();
        return view('cashier.withdrawals.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $member = Member::where('member_id', $validated['member_id'])->firstOrFail();
        
        if ($member->savings < $validated['amount']) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient balance'])->withInput();
        }
        
        $balanceBefore = $member->balance;
        $balanceAfter = $balanceBefore - $validated['amount'];

        Transaction::create([
            'member_id' => $validated['member_id'],
            'type' => 'withdrawal',
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
            'category' => $validated['category'] ?? 'savings',
            'description' => $validated['description'] ?? 'Withdrawal transaction',
            'notes' => $validated['notes'] ?? null,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => 'completed',
            'processed_by' => auth()->id()
        ]);
        
        $member->decrement('savings', $validated['amount']);
        $member->decrement('balance', $validated['amount']);

        return redirect()->route('cashier.withdrawals.index')->with('success', 'Withdrawal recorded successfully');
    }

    public function show($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')->with('member')->findOrFail($id);
        return view('cashier.withdrawals.show', compact('withdrawal'));
    }
}
