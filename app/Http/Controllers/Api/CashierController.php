<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'stats' => [
                'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
                'pendingLoans' => Loan::where('status', 'pending')->count(),
                'todayDeposits' => Transaction::whereDate('created_at', today())->where('type', 'deposit')->sum('amount'),
                'todayWithdrawals' => Transaction::whereDate('created_at', today())->where('type', 'withdrawal')->sum('amount'),
            ],
        ]);
    }

    public function getRecentTransactions()
    {
        return response()->json([
            'success' => true,
            'transactions' => Transaction::latest()->take(20)->get(),
        ]);
    }

    public function processDeposit(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|exists:members,member_id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'type' => 'deposit',
            'description' => $validated['description'] ?? 'Deposit',
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deposit processed successfully.',
            'transaction' => $transaction,
        ]);
    }

    public function processWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|exists:members,member_id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'type' => 'withdrawal',
            'description' => $validated['description'] ?? 'Withdrawal',
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal processed successfully.',
            'transaction' => $transaction,
        ]);
    }
}
