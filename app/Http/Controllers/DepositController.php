<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        return response()->json(Deposit::with('member')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_name' => 'required|string',
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'deposit_type' => 'required|string',
            'payment_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $deposit = Deposit::create($validated);
        return response()->json(['success' => true, 'deposit' => $deposit]);
    }

    public function show(Deposit $deposit)
    {
        return response()->json($deposit->load('member'));
    }

    public function update(Request $request, Deposit $deposit)
    {
        $validated = $request->validate([
            'member_name' => 'required|string',
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'deposit_type' => 'required|string',
            'payment_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $deposit->update($validated);
        return response()->json(['success' => true, 'deposit' => $deposit]);
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
        return response()->json(['success' => true]);
    }
}