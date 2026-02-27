<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('type', 'deposit')->with('member');

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

        $deposits = $query->latest()->paginate(20)->appends($request->query());
        return view('cashier.deposits.index', compact('deposits'));
    }

    public function create()
    {
        $members = Member::all();
        return view('cashier.deposits.create', compact('members'));
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
        
        $balanceBefore = $member->balance;
        $balanceAfter = $balanceBefore + $validated['amount'];

        Transaction::create([
            'member_id' => $validated['member_id'],
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
            'category' => $validated['category'] ?? 'savings',
            'description' => $validated['description'] ?? 'Deposit transaction',
            'notes' => $validated['notes'] ?? null,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => 'completed',
            'processed_by' => auth()->id()
        ]);
        
        $member->increment('savings', $validated['amount']);
        $member->increment('balance', $validated['amount']);

        return redirect()->route('cashier.deposits.index')->with('success', 'Deposit recorded successfully');
    }

    public function show($id)
    {
        $deposit = Transaction::where('type', 'deposit')->with('member')->findOrFail($id);
        return view('cashier.deposits.show', compact('deposit'));
    }
}
