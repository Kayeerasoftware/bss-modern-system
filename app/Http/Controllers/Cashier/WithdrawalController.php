<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')->with('member')->latest()->paginate(20);
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
