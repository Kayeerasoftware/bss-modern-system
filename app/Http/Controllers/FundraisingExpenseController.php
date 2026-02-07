<?php

namespace App\Http\Controllers;

use App\Models\FundraisingExpense;
use Illuminate\Http\Request;

class FundraisingExpenseController extends Controller
{
    public function index($fundraisingId = null)
    {
        $query = FundraisingExpense::with('fundraising');
        
        if ($fundraisingId) {
            $query->where('fundraising_id', $fundraisingId);
        }
        
        return response()->json($query->orderBy('expense_date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fundraising_id' => 'required|exists:fundraisings,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string'
        ]);

        $lastExpense = FundraisingExpense::latest('id')->first();
        $nextNumber = $lastExpense ? intval(substr($lastExpense->expense_id, 3)) + 1 : 1;
        $validated['expense_id'] = 'EXP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $expense = FundraisingExpense::create($validated);
        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function update(Request $request, $id)
    {
        $expense = FundraisingExpense::findOrFail($id);
        $expense->update($request->all());
        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function destroy($id)
    {
        FundraisingExpense::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
