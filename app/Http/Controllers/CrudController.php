<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Share;
use App\Models\SavingsHistory;
use Illuminate\Support\Facades\Hash;

class CrudController extends Controller
{
    // Member CRUD Operations
    public function getNextMemberId()
    {
        $allMembers = Member::all();
        if ($allMembers->isEmpty()) {
            return response()->json(['next_id' => 'BSS001']);
        }
        
        $maxNum = $allMembers->map(function($m) {
            return intval(substr($m->member_id, 3));
        })->max();
        
        $nextId = 'BSS' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
        return response()->json(['next_id' => $nextId]);
    }

    public function createMember(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:members',
                'location' => 'required|string|max:255',
                'occupation' => 'required|string|max:255',
                'contact' => 'required|string|max:20',
                'role' => 'required|in:client,shareholder,cashier,td,ceo'
            ]);

            $allMembers = Member::all();
            if ($allMembers->isEmpty()) {
                $memberId = 'BSS001';
            } else {
                $maxNum = $allMembers->map(function($m) {
                    return intval(substr($m->member_id, 3));
                })->max();
                $memberId = 'BSS' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            }

            $member = Member::create([
                'member_id' => $memberId,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'location' => $validated['location'],
                'occupation' => $validated['occupation'],
                'contact' => $validated['contact'],
                'role' => $validated['role'],
                'savings' => 0,
                'loan' => 0,
                'balance' => 0,
                'savings_balance' => 0,
                'password' => Hash::make('password123')
            ]);

            return response()->json(['success' => true, 'member' => $member]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateMember(Request $request, $id)
    {
        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 404);
            }

            $rules = [
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:members,email,' . $member->id,
                'location' => 'nullable|string|max:255',
                'occupation' => 'nullable|string|max:255',
                'contact' => 'nullable|string|max:20',
                'role' => 'nullable|in:client,shareholder,cashier,td,ceo',
                'savings' => 'nullable|numeric|min:0',
                'loan' => 'nullable|numeric|min:0'
            ];

            $validated = $request->validate($rules);
            $member->update(array_filter($validated));

            return response()->json(['success' => true, 'member' => $member->fresh()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteMember($id)
    {
        try {
            $member = Member::find($id);
            
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 404);
            }
            
            $memberName = $member->full_name;
            $memberId = $member->member_id;
            
            \DB::beginTransaction();
            
            // Delete all related records
            \DB::table('loans')->where('member_id', $memberId)->delete();
            \DB::table('transactions')->where('member_id', $memberId)->delete();
            \DB::table('shares')->where('member_id', $memberId)->delete();
            \DB::table('savings_history')->where('member_id', $memberId)->delete();
            \DB::table('dividends')->where('member_id', $memberId)->delete();
            \DB::table('portfolio_performances')->where('member_id', $memberId)->delete();
            \DB::table('chat_messages')->where('sender_id', $memberId)->orWhere('receiver_id', $memberId)->delete();
            
            $member->delete();
            
            \DB::commit();
            
            $user = 'Admin';
            if (auth()->check() && auth()->user()) {
                $user = auth()->user()->name;
            }
            
            \DB::table('audit_logs')->insert([
                'user' => $user,
                'action' => 'Member Deleted',
                'details' => "Deleted member: {$memberName} ({$memberId})",
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Member deleted successfully']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Loan CRUD Operations
    public function createLoan(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:1000',
            'purpose' => 'required|string|max:255',
            'repayment_months' => 'required|integer|min:1|max:60'
        ]);

        $loan = Loan::create([
            'loan_id' => 'LOAN' . str_pad(Loan::count() + 1, 3, '0', STR_PAD_LEFT),
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'repayment_months' => $validated['repayment_months'],
            'interest' => $validated['amount'] * 0.05, // 5% interest
            'monthly_payment' => ($validated['amount'] * 1.05) / $validated['repayment_months'],
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function approveLoan($id)
    {
        $loan = Loan::findOrFail($id);
        
        $updater = 'Admin';
        if (auth()->check()) {
            $updater = ucfirst(auth()->user()->role);
        } elseif (session('member_id')) {
            $member = Member::where('member_id', session('member_id'))->first();
            if ($member) {
                $updater = ucfirst($member->role);
            }
        }
        
        $loan->update([
            'status' => 'approved',
            'updated_by' => $updater
        ]);

        // Update member's loan balance
        $member = Member::where('member_id', $loan->member_id)->first();
        if ($member) {
            $member->increment('loan', $loan->amount);
        }

        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function rejectLoan($id)
    {
        $loan = Loan::findOrFail($id);
        
        $updater = 'Admin';
        if (auth()->check()) {
            $updater = ucfirst(auth()->user()->role);
        } elseif (session('member_id')) {
            $member = Member::where('member_id', session('member_id'))->first();
            if ($member) {
                $updater = ucfirst($member->role);
            }
        }
        
        $loan->update([
            'status' => 'rejected',
            'updated_by' => $updater
        ]);
        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function pendingLoan($id)
    {
        $loan = Loan::findOrFail($id);
        
        $updater = 'Admin';
        if (auth()->check()) {
            $updater = ucfirst(auth()->user()->role);
        } elseif (session('member_id')) {
            $member = Member::where('member_id', session('member_id'))->first();
            if ($member) {
                $updater = ucfirst($member->role);
            }
        }
        
        $loan->update([
            'status' => 'pending',
            'updated_by' => $updater
        ]);
        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function updateLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'exists:members,member_id',
            'amount' => 'numeric|min:1000',
            'purpose' => 'string|max:255',
            'repayment_months' => 'integer|min:1|max:60',
            'status' => 'in:pending,approved,rejected'
        ]);

        $loan->update($validated);
        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function deleteLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loanId = $loan->loan_id;
        $amount = $loan->amount;
        $loan->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Loan Deleted',
            'details' => "Deleted loan: {$loanId} (UGX " . number_format($amount) . ")",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    public function updateTransaction(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'exists:members,member_id',
            'amount' => 'numeric|min:1',
            'type' => 'in:deposit,withdrawal,transfer,loan_payment',
            'description' => 'nullable|string'
        ]);

        $transaction->update($validated);
        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    public function deleteTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $txnId = $transaction->transaction_id;
        $amount = $transaction->amount;
        $type = $transaction->type;
        $transaction->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Transaction Deleted',
            'details' => "Deleted {$type} transaction: {$txnId} (UGX " . number_format($amount) . ")",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    // Transaction CRUD Operations
    public function createTransaction(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:deposit,withdrawal,transfer,loan_payment',
            'description' => 'nullable|string'
        ]);

        $member = Member::where('member_id', $validated['member_id'])->first();

        // Check sufficient balance for withdrawal/transfer
        if (in_array($validated['type'], ['withdrawal', 'transfer']) && $member->savings < $validated['amount']) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 400);
        }

        $transaction = Transaction::create([
            'transaction_id' => 'TXN' . \Illuminate\Support\Str::random(6),
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? $validated['type']
        ]);

        // Update member balance
        if ($validated['type'] === 'deposit') {
            $member->increment('savings', $validated['amount']);
            $member->increment('balance', $validated['amount']);
        } elseif (in_array($validated['type'], ['withdrawal', 'transfer'])) {
            $member->decrement('savings', $validated['amount']);
            $member->decrement('balance', $validated['amount']);
        } elseif ($validated['type'] === 'loan_payment') {
            $member->decrement('loan', $validated['amount']);
        }

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    // Project CRUD Operations
    public function createProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:1000',
            'timeline' => 'required|date|after:today',
            'roi' => 'nullable|numeric',
            'risk_score' => 'nullable|integer|min:0|max:100'
        ]);

        $project = Project::create([
            'project_id' => 'PRJ' . str_pad(Project::count() + 1, 3, '0', STR_PAD_LEFT),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'timeline' => $validated['timeline'],
            'progress' => 0,
            'roi' => $validated['roi'] ?? 0,
            'risk_score' => $validated['risk_score'] ?? 20
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'budget' => 'numeric|min:1000',
            'timeline' => 'date',
            'progress' => 'integer|min:0|max:100',
            'roi' => 'numeric',
            'risk_score' => 'integer|min:0|max:100'
        ]);

        $project->update($validated);
        return response()->json(['success' => true, 'project' => $project]);
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $projectName = $project->name;
        $project->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Project Deleted',
            'details' => "Deleted project: {$projectName}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    // Share CRUD Operations
    public function createShare(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'shares_owned' => 'required|integer|min:1',
            'share_value' => 'required|numeric|min:100'
        ]);

        $share = Share::create([
            'member_id' => $validated['member_id'],
            'shares_owned' => $validated['shares_owned'],
            'share_value' => $validated['share_value'],
            'total_value' => $validated['shares_owned'] * $validated['share_value'],
            'purchase_date' => now()->toDateString(),
            'status' => 'active'
        ]);

        return response()->json(['success' => true, 'share' => $share]);
    }

    public function updateShare(Request $request, $id)
    {
        $share = Share::findOrFail($id);

        $validated = $request->validate([
            'shares_owned' => 'integer|min:1',
            'share_value' => 'numeric|min:100',
            'status' => 'in:active,inactive'
        ]);

        if (isset($validated['shares_owned']) || isset($validated['share_value'])) {
            $sharesOwned = $validated['shares_owned'] ?? $share->shares_owned;
            $shareValue = $validated['share_value'] ?? $share->share_value;
            $validated['total_value'] = $sharesOwned * $shareValue;
        }

        $share->update($validated);
        return response()->json(['success' => true, 'share' => $share]);
    }

    // Get all data for specific member
    public function getMemberData($memberId)
    {
        $member = Member::where('member_id', $memberId)->first();

        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $data = [
            'member' => $member,
            'loans' => Loan::where('member_id', $memberId)->get(),
            'transactions' => Transaction::where('member_id', $memberId)->latest()->get(),
            'shares' => Share::where('member_id', $memberId)->first(),
            'savings_history' => SavingsHistory::where('member_id', $memberId)->orderBy('transaction_date')->get()
        ];

        return response()->json($data);
    }
}
