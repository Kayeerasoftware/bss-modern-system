<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $query = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit');

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

        $deposits = $query->latest()->paginate(15)->appends($request->query());
        
        return view('member.deposits.index', compact('deposits', 'member'));
    }

    public function create()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        return view('member.deposits.create', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();

        DB::beginTransaction();
        try {
            Transaction::create([
                'member_id' => $member->member_id,
                'type' => 'deposit',
                'amount' => $request->amount,
                'description' => $request->description ?? 'Deposit via ' . $request->payment_method,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('member.deposits.index')->with('success', 'Deposit request submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit deposit request'])->withInput();
        }
    }
}
