<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Share;
use App\Models\SavingsHistory;
use App\Models\User;
use App\Services\Financial\TransactionPostingService;
use Illuminate\Support\Facades\DB;
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
                'email' => 'required|email|unique:members|unique:users,email',
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

            $user = User::withoutEvents(function () use ($validated) {
                return User::create([
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('password123'),
                    'role' => $validated['role'],
                    'status' => 'active',
                    'is_active' => true,
                    'phone' => $validated['contact'],
                    'location' => $validated['location'],
                ]);
            });

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
                'password' => $user->password,
                'user_id' => $user->id,
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
                'loan' => 'nullable|numeric|min:0'
            ];

            $validated = $request->validate($rules);
            unset($validated['savings']);
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
            $resolvedMemberId = resolve_member_id($memberId);
            if ($resolvedMemberId) {
                \DB::table('savings_transactions')
                    ->whereIn('savings_account_id', function ($query) use ($resolvedMemberId) {
                        $query->select('id')
                            ->from('savings_accounts')
                            ->where('member_id', $resolvedMemberId);
                    })
                    ->delete();
            }
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
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'purpose' => 'required|string|max:255',
            'repayment_months' => 'required|integer|min:1|max:60'
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Invalid member'], 422);
        }

        $loanTypeId = \App\Models\LoanType::query()->where('is_active', 1)->value('id')
            ?? \App\Models\LoanType::query()->value('id');
        $pendingStatusId = \App\Models\LoanStatus::query()->where('name', 'pending')->value('id');
        $interestRate = 5.0;
        $totalInterest = round($validated['amount'] * ($interestRate / 100), 2);

        $loan = null;
        DB::transaction(function () use ($memberId, $loanTypeId, $pendingStatusId, $validated, $interestRate, $totalInterest, &$loan): void {
            $application = \App\Models\LoanApplication::create([
                'member_id' => $memberId,
                'loan_type_id' => $loanTypeId,
                'requested_amount' => $validated['amount'],
                'requested_tenure_months' => $validated['repayment_months'],
                'purpose' => $validated['purpose'],
                'status_id' => $pendingStatusId,
                'submission_date' => now(),
            ]);

            $loan = Loan::create([
                'application_id' => $application->id,
                'member_id' => $memberId,
                'loan_type_id' => $loanTypeId,
                'principal_amount' => $validated['amount'],
                'interest_rate' => $interestRate,
                'total_interest' => $totalInterest,
                'repayment_months' => $validated['repayment_months'],
                'application_date' => now()->toDateString(),
                'status_id' => $pendingStatusId,
                'notes' => $validated['purpose'],
            ]);
            $application->update(['converted_to_loan_id' => $loan->id]);
        });

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
        
        $approvedStatusId = \App\Models\LoanStatus::query()->where('name', 'approved')->value('id');
        $loan->update([
            'status_id' => $approvedStatusId ?? $loan->status_id,
        ]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $approvedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
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
        
        $rejectedStatusId = \App\Models\LoanStatus::query()->where('name', 'rejected')->value('id');
        $loan->update([
            'status_id' => $rejectedStatusId ?? $loan->status_id,
        ]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $rejectedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }
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
        
        $pendingStatusId = \App\Models\LoanStatus::query()->where('name', 'pending')->value('id');
        $loan->update([
            'status_id' => $pendingStatusId ?? $loan->status_id,
        ]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $pendingStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }
        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function updateLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'nullable|string',
            'amount' => 'numeric|min:1000',
            'purpose' => 'string|max:255',
            'repayment_months' => 'integer|min:1|max:60',
            'status' => 'in:pending,approved,rejected'
        ]);

        $payload = [];
        if (isset($validated['member_id'])) {
            $resolvedMemberId = resolve_member_id($validated['member_id']);
            if (!$resolvedMemberId) {
                return response()->json(['success' => false, 'message' => 'Invalid member'], 422);
            }
            $payload['member_id'] = $resolvedMemberId;
        }
        if (isset($validated['amount'])) {
            $payload['principal_amount'] = $validated['amount'];
        }
        if (isset($validated['repayment_months'])) {
            $payload['repayment_months'] = $validated['repayment_months'];
        }
        if (isset($validated['purpose'])) {
            $payload['notes'] = $validated['purpose'];
        }
        if (isset($validated['status'])) {
            $statusId = \App\Models\LoanStatus::query()->where('name', $validated['status'])->value('id');
            $payload['status_id'] = $statusId ?? $loan->status_id;
        }

        if (!empty($payload)) {
            $loan->update($payload);
        }
        return response()->json(['success' => true, 'loan' => $loan]);
    }

    public function deleteLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loanId = $loan->loan_number ?? $loan->id;
        $amount = $loan->principal_amount ?? 0;
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
    public function createTransaction(Request $request, TransactionPostingService $postingService)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:deposit,withdrawal,transfer,loan_payment',
            'description' => 'nullable|string'
        ]);

        $resolvedMemberId = resolve_member_id($validated['member_id']);
        $member = $resolvedMemberId ? Member::find($resolvedMemberId) : null;
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Invalid member'], 400);
        }

        // Check sufficient balance for withdrawal/transfer
        if (in_array($validated['type'], ['withdrawal', 'transfer'], true) && $member->savings_balance < $validated['amount']) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 400);
        }

        $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id')
            ?? TransactionType::query()->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');
        $categoryName = match ($validated['type']) {
            'deposit' => 'savings_deposit',
            'withdrawal' => 'savings_withdrawal',
            'transfer' => 'transfer_out',
            'loan_payment' => 'loan_payment',
            default => 'savings_deposit',
        };
        $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id')
            ?? TransactionCategory::query()->where('transaction_type_id', $transactionTypeId)->value('id')
            ?? TransactionCategory::query()->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $balanceBefore = (float) ($member->balance ?? 0);
        $netAmount = (float) $validated['amount'];
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'credit'
            ? $balanceBefore + $netAmount
            : $balanceBefore - $netAmount;

        $transaction = null;
        try {
            DB::transaction(function () use (
                $resolvedMemberId,
                $transactionTypeId,
                $categoryId,
                $statusId,
                $validated,
                $currencyId,
                $paymentMethodId,
                $balanceBefore,
                $balanceAfter,
                $netAmount,
                $postingService,
                &$transaction
            ): void {
                $transaction = Transaction::create([
                    'member_id' => $resolvedMemberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $validated['amount'],
                    'net_amount' => $netAmount,
                    'currency_id' => $currencyId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'payment_method_id' => $paymentMethodId,
                    'description' => $validated['description'] ?? $validated['type'],
                    'transaction_date' => now(),
                    'value_date' => now(),
                    'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
                    'processed_at' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $validated);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
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
        $resolvedMemberId = resolve_member_id($memberId);
        $member = $resolvedMemberId ? Member::find($resolvedMemberId) : null;

        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $data = [
            'member' => $member,
            'loans' => Loan::where('member_id', $member->id)->get(),
            'transactions' => Transaction::where('member_id', $member->id)->latest()->get(),
            'shares' => Share::where('member_id', $member->id)->first(),
            'savings_history' => (resolve_member_id($memberId)
                ? SavingsHistory::forMember((int) resolve_member_id($memberId))->orderBy('created_at')->get()
                : collect())
        ];

        return response()->json($data);
    }
}
