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

        if ($request->filled('period')) {
            $this->applyPeriodFilter($query, (string) $request->period);
        }

        $deposits = $query->latest()->paginate(15)->appends($request->query());

        $completedQuery = (clone $query)->where(function ($q): void {
            $q->where('status', 'completed')
                ->orWhereNull('status');
        });

        $summary = [
            'total_deposits' => (float) (clone $completedQuery)->sum('amount'),
            'this_month' => (float) (clone $completedQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            'average_deposit' => (float) ((clone $completedQuery)->avg('amount') ?? 0),
            'total_count' => (int) (clone $query)->count(),
            'completed_count' => (int) (clone $query)
                ->where(function ($q): void {
                    $q->where('status', 'completed')
                        ->orWhereNull('status');
                })
                ->count(),
            'pending_count' => (int) (clone $query)->where('status', 'pending')->count(),
        ];
        
        return view('member.deposits.index', compact('deposits', 'member', 'summary'));
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

    private function applyPeriodFilter($query, string $period): void
    {
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            default:
                break;
        }
    }
}
