<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();

        $query = Transaction::where('member_id', $member->id)
            ->whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
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

        $completedQuery = (clone $query)->whereHas('statusRelation', fn ($q) => $q->where('name', 'completed'));

        $summary = [
            'total_deposits' => (float) (clone $completedQuery)->sum('amount'),
            'this_month' => (float) (clone $completedQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            'average_deposit' => (float) ((clone $completedQuery)->avg('amount') ?? 0),
            'total_count' => (int) (clone $query)->count(),
            'completed_count' => (int) (clone $query)
                ->whereHas('statusRelation', fn ($q) => $q->where('name', 'completed'))
                ->count(),
            'pending_count' => (int) (clone $query)->whereHas('statusRelation', fn ($q) => $q->where('name', 'pending'))->count(),
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

        $transactionTypeId = TransactionType::query()->where('name', 'deposit')->value('id');
        $pendingStatusId = TransactionStatus::query()->where('name', 'pending')->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', $request->payment_method)->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'savings_deposit')->value('id');

        $balanceBefore = (float) ($member->balance ?? 0);
        $balanceAfter = $balanceBefore;

        DB::beginTransaction();
        try {
            Transaction::create([
                'member_id' => $member->id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'amount' => $request->amount,
                'net_amount' => $request->amount,
                'description' => $request->description ?? 'Deposit via ' . $request->payment_method,
                'status_id' => $pendingStatusId,
                'payment_method_id' => $paymentMethodId,
                'currency_id' => $currencyId,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'processed_by' => Auth::id() ?? \App\Models\User::query()->value('id'),
                'processed_at' => now(),
                'transaction_date' => now(),
                'value_date' => now(),
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
