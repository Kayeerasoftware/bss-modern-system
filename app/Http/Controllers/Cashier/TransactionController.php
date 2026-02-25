<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('member')->latest()->paginate(20);
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
