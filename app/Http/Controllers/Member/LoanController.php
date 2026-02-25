<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function apply()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        return view('member.loans.apply', compact('member'));
    }

    public function myLoans()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $loans = Loan::where('member_id', $member->member_id)
            ->latest()
            ->paginate(10);
        
        return view('member.loans.my-loans', compact('loans', 'member'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->member_id)
            ->firstOrFail();
        
        return view('member.loans.show', compact('loan', 'member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'purpose' => 'required|string',
            'duration' => 'required|integer|min:1|max:60'
        ]);

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();

        DB::beginTransaction();
        try {
            $interest = $member->calculateInterest($request->amount, $request->duration);
            $monthlyPayment = $member->calculateMonthlyPayment($request->amount, $interest, $request->duration);

            Loan::create([
                'member_id' => $member->member_id,
                'amount' => $request->amount,
                'interest' => $interest,
                'duration' => $request->duration,
                'monthly_payment' => $monthlyPayment,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'balance' => $request->amount + $interest,
            ]);

            DB::commit();
            return redirect()->route('member.loans.my-loans')->with('success', 'Loan application submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit loan application'])->withInput();
        }
    }

    public function repay($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->member_id)
            ->firstOrFail();
        
        return view('member.loans.repay', compact('loan', 'member'));
    }

    public function storeRepayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->member_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $loan->balance -= $request->amount;
            if ($loan->balance <= 0) {
                $loan->status = 'paid';
                $loan->balance = 0;
            }
            $loan->save();

            DB::commit();
            return redirect()->route('member.loans.show', $id)->with('success', 'Repayment recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record repayment']);
        }
    }
}
