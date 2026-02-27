<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.financial.transactions', compact('transactions'));
    }

    public function create()
    {
        $members = \App\Models\Member::orderBy('full_name')->get();
        return view('admin.financial.transactions-create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string',
            'channel' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'reference' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'batch_id' => 'nullable|string',
            'location' => 'nullable|string',
            'transaction_date' => 'required|date',
            'scheduled_at' => 'nullable|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'notification_sent' => 'nullable|boolean',
        ]);

        $member = \App\Models\Member::where('member_id', $validated['member_id'])->first();
        
        // Apply system settings
        if (!isset($validated['fee']) || $validated['fee'] == 0) {
            $validated['fee'] = ($validated['amount'] * setting('transaction_fee', 1)) / 100;
        }
        
        $validated['processed_by'] = auth()->id();
        $validated['status'] = 'completed';
        $validated['completed_at'] = now();
        $validated['balance_before'] = $member->balance ?? 0;
        
        $totalCharges = ($validated['fee'] ?? 0) + ($validated['tax_amount'] ?? 0) + ($validated['commission'] ?? 0);
        
        if ($validated['type'] === 'withdrawal') {
            $withdrawalFee = ($validated['amount'] * setting('withdrawal_fee', 2)) / 100;
            $totalCharges += $withdrawalFee;
            $validated['net_amount'] = $validated['amount'] - $totalCharges;
            $validated['balance_after'] = $validated['balance_before'] - $validated['net_amount'];
            $member->balance = $validated['balance_after'];
        } elseif ($validated['type'] === 'deposit') {
            $validated['net_amount'] = $validated['amount'];
            $validated['balance_after'] = $validated['balance_before'] + $validated['amount'];
            $member->balance = $validated['balance_after'];
        } else {
            $validated['net_amount'] = $validated['amount'] - $totalCharges;
            $validated['balance_after'] = $validated['balance_before'] - $validated['net_amount'];
            $member->balance = $validated['balance_after'];
        }
        
        $member->save();
        
        if ($request->notification_sent && setting('sms_notifications', 1)) {
            $validated['notification_sent_at'] = now();
        }
        
        $validated['ip_address'] = $request->ip();
        $validated['device_info'] = $request->userAgent();
        
        Transaction::create($validated);
        
        return redirect()->route('admin.financial.transactions')->with('success', 'Transaction created successfully');
    }

    public function show($id)
    {
        $transaction = Transaction::with('member')->findOrFail($id);
        return view('admin.financial.transactions-show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('admin.financial.transactions-edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $validated = $request->validate([
            'type' => 'required|in:deposit,withdrawal,transfer',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'channel' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'reference' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,reversed',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $totalCharges = ($validated['fee'] ?? 0) + ($validated['tax_amount'] ?? 0) + ($validated['commission'] ?? 0);
        
        if ($validated['type'] === 'withdrawal') {
            $validated['net_amount'] = $validated['amount'] - $totalCharges;
        } else {
            $validated['net_amount'] = $validated['amount'];
        }
        
        $transaction->update($validated);
        
        return redirect()->route('admin.financial.transactions')->with('success', 'Transaction updated successfully');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Reverse balance changes if transaction was completed
        if ($transaction->status === 'completed' && $transaction->member) {
            $member = $transaction->member;
            
            if ($transaction->type === 'deposit') {
                $member->balance -= $transaction->amount;
            } elseif ($transaction->type === 'withdrawal') {
                $member->balance += ($transaction->net_amount ?? $transaction->amount);
            } elseif ($transaction->type === 'transfer') {
                $member->balance += ($transaction->net_amount ?? $transaction->amount);
            }
            
            $member->save();
        }
        
        $transaction->delete();
        
        return redirect()->route('admin.financial.transactions')->with('success', 'Transaction deleted and balance restored successfully');
    }

    public function deposits(Request $request)
    {
        $query = Transaction::where('type', 'deposit')->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deposits = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.financial.deposits', compact('deposits'));
    }

    public function withdrawals(Request $request)
    {
        $query = Transaction::where('type', 'withdrawal')->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.financial.withdrawals', compact('withdrawals'));
    }

    public function transfers(Request $request)
    {
        $query = Transaction::where('type', 'transfer')->with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.financial.transfers', compact('transactions'));
    }

    public function reports()
    {
        $summary = [
            'total_income' => \App\Models\Transaction::where('type', 'deposit')->sum('amount'),
            'total_expenses' => \App\Models\Transaction::where('type', 'withdrawal')->sum('amount'),
            'net_balance' => \App\Models\Member::sum('balance'),
            'total_transactions' => \App\Models\Transaction::count(),
        ];
        
        return view('admin.financial.reports', compact('summary'));
    }

    public function generateReport(Request $request)
    {
        // Implementation
    }
}
