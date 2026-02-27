<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $query = Transaction::where('member_id', $member->member_id)
            ->where('type', 'withdrawal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
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

        $withdrawals = $query->latest()->paginate(15)->appends($request->query());
        
        return view('member.withdrawals.index', compact('withdrawals', 'member'));
    }

    public function create()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        return view('member.withdrawals.create', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'withdrawal_method' => 'required|string',
            'reason' => 'required|string'
        ]);

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();

        if ($request->amount > $member->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance'])->withInput();
        }

        DB::beginTransaction();
        try {
            Transaction::create([
                'member_id' => $member->member_id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'description' => $request->reason,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('member.withdrawals.index')->with('success', 'Withdrawal request submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit withdrawal request'])->withInput();
        }
    }
}
