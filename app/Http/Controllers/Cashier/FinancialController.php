<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Financial\Transaction;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('member');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($q2) use ($request) {
                      $q2->where('full_name', 'like', '%' . $request->search . '%')
                         ->orWhere('member_id', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
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

        $transactions = $query->latest()->paginate(15)->appends($request->all());
        return view('cashier.financial.index', compact('transactions'));
    }

    public function create()
    {
        $members = \App\Models\Member::orderBy('full_name')->get();
        return view('cashier.financial.create', compact('members'));
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
        
        $validated['transaction_id'] = 'TXN-' . strtoupper(uniqid());
        $validated['processed_by'] = auth()->id();
        $validated['status'] = 'completed';
        $validated['completed_at'] = now();
        $validated['balance_before'] = $member->balance ?? 0;
        
        $totalCharges = ($validated['fee'] ?? 0) + ($validated['tax_amount'] ?? 0) + ($validated['commission'] ?? 0);
        
        if ($validated['type'] === 'withdrawal') {
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
        
        return redirect()->route('cashier.financial.index')->with('success', 'Transaction created successfully');
    }

    public function deposits(Request $request)
    {
        $query = Transaction::where('type', 'deposit')->with('member');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($q2) use ($request) {
                      $q2->where('full_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deposits = $query->latest()->paginate(15)->appends($request->all());
        return view('cashier.financial.deposits', compact('deposits'));
    }

    public function withdrawals(Request $request)
    {
        $query = Transaction::where('type', 'withdrawal')->with('member');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($q2) use ($request) {
                      $q2->where('full_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->latest()->paginate(15)->appends($request->all());
        return view('cashier.financial.withdrawals', compact('withdrawals'));
    }

    public function transfers(Request $request)
    {
        $query = Transaction::where('type', 'transfer')->with('member');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($q2) use ($request) {
                      $q2->where('full_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->appends($request->all());
        return view('cashier.financial.transfers', compact('transactions'));
    }
}
