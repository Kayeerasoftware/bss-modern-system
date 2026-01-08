<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('member')->orderBy('created_at', 'desc')->get();
        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $loan = Loan::create([
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate ?? 5,
            'duration_months' => $request->duration_months,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'application_date' => now()
        ]);

        return response()->json($loan->load('member'));
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'approved',
            'approved_date' => now()
        ]);

        return response()->json($loan);
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'rejected']);
        return response()->json($loan);
    }

    public function repayment(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $repaymentAmount = $request->amount;
        
        $loan->update([
            'amount_paid' => $loan->amount_paid + $repaymentAmount,
            'status' => ($loan->amount_paid + $repaymentAmount >= $loan->amount) ? 'completed' : 'active'
        ]);

        return response()->json($loan);
    }
}