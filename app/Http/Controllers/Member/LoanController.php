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

    public function myLoans(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
        $query = Loan::where('member_id', $member->member_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_id', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $query->filterStatus($request->status);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->latest()->paginate(10)->appends($request->query());
        
        return view('member.loans.my-loans', compact('loans', 'member'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
        $loan = Loan::where('id', $id)
            ->where('member_id', $member->member_id)
            ->firstOrFail();
        
        return view('member.loans.show', compact('loan', 'member'));
    }

    public function store(Request $request)
    {
        $repaymentMonths = (int) ($request->repayment_months ?? $request->duration ?? 0);

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'purpose' => 'required|string',
            'repayment_months' => 'nullable|integer|min:1|max:60',
            'duration' => 'nullable|integer|min:1|max:60',
        ]);
        if ($repaymentMonths < 1) {
            return back()->withErrors(['repayment_months' => 'Repayment months is required.'])->withInput();
        }

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        if (!$member) {
            return back()->withErrors(['error' => 'Member profile not found'])->withInput();
        }

        DB::beginTransaction();
        try {
            $interestRate = 10;
            $interest = $member->calculateInterest($request->amount, $repaymentMonths);
            $monthlyPayment = $member->calculateMonthlyPayment($request->amount, $interest, $repaymentMonths);

            Loan::create([
                'member_id' => $member->member_id,
                'amount' => $request->amount,
                'interest_rate' => $interestRate,
                'interest' => $interest,
                'repayment_months' => $repaymentMonths,
                'monthly_payment' => $monthlyPayment,
                'purpose' => $request->purpose,
                'status' => 'pending',
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
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        
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
            $loan->paid_amount = $loan->paid_amount + (float) $request->amount;
            $loan->status = 'approved';
            $loan->save();

            DB::commit();
            return redirect()->route('member.loans.show', $id)->with('success', 'Repayment recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record repayment']);
        }
    }
}
