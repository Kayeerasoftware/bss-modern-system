<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20)->appends($request->query());
        return view('cashier.transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('cashier.transactions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        Transaction::create($validated);
        return redirect()->route('cashier.transactions.index')->with('success', 'Transaction created successfully');
    }

    public function show($id)
    {
        $transaction = Transaction::with('member')->findOrFail($id);
        return view('cashier.transactions.show', compact('transaction'));
    }
}
