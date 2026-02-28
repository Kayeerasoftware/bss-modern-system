<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->latest();

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(25),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|exists:members,member_id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => Transaction::findOrFail($id),
        ]);
    }
}
