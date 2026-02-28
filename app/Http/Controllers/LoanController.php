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
        $repaymentMonths = (int) ($request->repayment_months ?? $request->duration_months ?? 12);
        $interestRate = (float) ($request->interest_rate ?? 10);
        $interest = (float) $request->amount * ($interestRate / 100) * ($repaymentMonths / 12);
        $monthlyPayment = ((float) $request->amount + $interest) / max($repaymentMonths, 1);

        $loan = Loan::create([
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'interest_rate' => $interestRate,
            'repayment_months' => $repaymentMonths,
            'interest' => $interest,
            'monthly_payment' => $monthlyPayment,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'application_date' => now(),
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
        $repaymentAmount = (float) $request->amount;
        $paidAmount = (float) $loan->paid_amount + $repaymentAmount;
        
        $loan->update([
            'amount_paid' => $paidAmount,
            'status' => 'approved',
        ]);

        return response()->json($loan);
    }
}
