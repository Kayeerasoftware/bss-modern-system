<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\Financial\TransactionPostingService;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function getDashboardData()
    {
        try {
            $cacheKey = 'dashboard_data_' . now()->format('Y-m-d-H');

            $data = Cache::remember($cacheKey, 3600, function() {
                $viewStats = app(DashboardStatsService::class)->get();
                return [
                    'totalMembers' => (int) ($viewStats['total_members'] ?? Member::count()),
                    'totalSavings' => (float) ($viewStats['total_system_balance'] ?? table_sum_or_zero('savings_accounts', 'current_balance')),
                    'activeLoans' => (int) ($viewStats['active_loans_count'] ?? Loan::query()
                        ->when(true, function ($q) {
                            $approvedStatusId = \App\Models\LoanStatus::query()->where('name', 'approved')->value('id');
                            $disbursedStatusId = \App\Models\LoanStatus::query()->where('name', 'disbursed')->value('id');
                            $statusIds = array_values(array_filter([$approvedStatusId, $disbursedStatusId]));
                            return $statusIds ? $q->whereIn('status_id', $statusIds) : $q;
                        })
                        ->count()),
                    'totalProjects' => Project::count(),
                    'totalBalance' => (float) ($viewStats['total_system_balance'] ?? (table_sum_or_zero('savings_accounts', 'current_balance') - (float) Loan::query()->sum('balance_due'))),
                    'pendingLoans' => (int) ($viewStats['pending_loans_count'] ?? Loan::status('pending')->count()),
                    'completedProjects' => Project::where('status', 'completed')->count(),
                    'monthlyDeposits' => Transaction::query()
                        ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                        ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                        ->where('ts.name', 'completed')
                        ->whereIn('tc.name', ['savings_deposit', 'transfer_in', 'loan_disbursement'])
                        ->whereMonth('transactions.created_at', now()->month)
                        ->sum(DB::raw('COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)')),
                    'monthlyWithdrawals' => Transaction::query()
                        ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                        ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                        ->where('ts.name', 'completed')
                        ->whereIn('tc.name', ['savings_withdrawal', 'transfer_out', 'fundraising_transfer'])
                        ->whereMonth('transactions.created_at', now()->month)
                        ->sum(DB::raw('COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)')),
                    'condolenceFund' => Cache::get('condolence_fund', 2000000)
                ];
            });

            // Fresh data
            $data['recentTransactions'] = Transaction::with(['member:id,member_account_number,member_number,full_name'])
                ->select('id', 'member_id', 'amount', 'transaction_type_id', 'category_id', 'created_at')
                ->latest()
                ->limit(10)
                ->get();

            $data['recentLoans'] = Loan::with(['member:id,member_account_number,member_number,full_name'])
                ->select('id', 'member_id', 'principal_amount', 'status_id', 'created_at')
                ->latest()
                ->limit(5)
                ->get();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('Dashboard data fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load data'], 500);
        }
    }

    public function getMembers(Request $request)
    {
        try {
            $query = Member::query();

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                      ->orWhere('member_account_number', 'LIKE', "%{$search}%")
                      ->orWhere('member_number', 'LIKE', "%{$search}%");
                });
            }

            $members = $query->select('id', 'member_account_number', 'member_number', 'full_name', 'email', 'created_at')
                ->latest()
                ->paginate(15);
            $members->setCollection(
                $members->getCollection()->each->append(['member_id', 'savings', 'loan', 'balance', 'savings_balance'])
            );

            return response()->json(['success' => true, 'data' => $members]);

        } catch (\Exception $e) {
            Log::error('Members fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load members'], 500);
        }
    }

    public function createMember(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'member_id' => 'required|string|unique:members,member_number',
                'phone' => 'nullable|string|max:20',
                'email' => 'required|email|unique:members,email|unique:users,email|max:255',
                'address' => 'nullable|string|max:500',
                'role' => 'nullable|in:admin,client,cashier,td,ceo,shareholder',
                'password' => 'nullable|string|min:6',
            ]);

            DB::beginTransaction();

            $user = User::withoutEvents(function () use ($validated) {
                return User::create([
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role' => $validated['role'] ?? 'client',
                    'status' => 'active',
                    'is_active' => true,
                    'phone' => $validated['phone'] ?? null,
                    'location' => $validated['address'] ?? null,
                ]);
            });

            $memberNumber = $validated['member_id'];
            $member = Member::create([
                'member_number' => $memberNumber,
                'member_account_number' => $memberNumber,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'contact' => $validated['phone'] ?? null,
                'location' => $validated['address'] ?? null,
                'role' => $validated['role'] ?? 'client',
                'status' => 'active',
                'password' => $user->password,
                'user_id' => $user->id,
            ]);

            DB::commit();
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Member created successfully',
                'data' => $member
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Member creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create member'], 500);
        }
    }

    public function deleteMember($id)
    {
        try {
            $member = Member::findOrFail($id);

            if ($member->loan > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete member with active loan'
                ], 400);
            }

            DB::beginTransaction();
            $member->delete();
            DB::commit();

            Cache::flush();

            return response()->json(['success' => true, 'message' => 'Member deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Member deletion failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete member'], 500);
        }
    }

    public function getLoans()
    {
        try {
            $loans = Loan::select('id', 'member_id', 'principal_amount', 'status_id', 'purpose', 'repayment_months', 'created_at')
                ->latest()
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $loans]);

        } catch (\Exception $e) {
            Log::error('Loans fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load loans'], 500);
        }
    }

    public function createLoan(Request $request)
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|string',
                'amount' => 'required|numeric|min:1000|max:500000',
                'purpose' => 'required|string|max:255',
                'repayment_months' => 'required|integer|in:6,12,24'
            ]);

            DB::beginTransaction();

        $resolvedMemberId = resolve_member_id($validated['member_id']);
        $member = $resolvedMemberId ? Member::find($resolvedMemberId) : null;
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid member'
            ], 400);
        }

            if ($member->loan > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member already has an active loan'
                ], 400);
            }

            $maxLoan = min($member->savings * 3, 500000);
            if ($validated['amount'] > $maxLoan) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum loan amount is UGX " . number_format($maxLoan)
                ], 400);
            }

            $interestRate = 2.0;
            $totalInterest = round(($validated['amount'] * $interestRate) / 100, 2);
            $loanTypeId = \App\Models\LoanType::query()->where('is_active', 1)->value('id')
                ?? \App\Models\LoanType::query()->value('id');
            $pendingStatusId = \App\Models\LoanStatus::query()->where('name', 'pending')->value('id');

            $application = \App\Models\LoanApplication::create([
                'member_id' => $member->id,
                'loan_type_id' => $loanTypeId,
                'requested_amount' => $validated['amount'],
                'requested_tenure_months' => $validated['repayment_months'],
                'purpose' => $validated['purpose'],
                'status_id' => $pendingStatusId,
                'submission_date' => now(),
            ]);

            $loan = Loan::create([
                'application_id' => $application->id,
                'member_id' => $member->id,
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

            DB::commit();
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Loan application created successfully',
                'data' => $loan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Loan creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create loan'], 500);
        }
    }

    public function approveLoan($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan is not in pending status'
                ], 400);
            }

            DB::beginTransaction();

            $approvedStatusId = \App\Models\LoanStatus::query()->where('name', 'approved')->value('id');
            $loan->update([
                'status_id' => $approvedStatusId ?? $loan->status_id,
                'approval_date' => now()->toDateString(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approved_ip' => request()->ip(),
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

            DB::commit();
            Cache::flush();

            return response()->json(['success' => true, 'message' => 'Loan approved successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Loan approval failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to approve loan'], 500);
        }
    }

    public function rejectLoan($id)
    {
        try {
            $loan = Loan::findOrFail($id);

            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan is not in pending status'
                ], 400);
            }

            $rejectedStatusId = \App\Models\LoanStatus::query()->where('name', 'rejected')->value('id');
            $loan->update(['status_id' => $rejectedStatusId ?? $loan->status_id]);

            if ($loan->application_id) {
                \App\Models\LoanApplication::query()
                    ->whereKey($loan->application_id)
                    ->update([
                        'status_id' => $rejectedStatusId ?? $loan->status_id,
                        'decision_by' => auth()->id(),
                        'decision_date' => now(),
                    ]);
            }
            Cache::flush();

            return response()->json(['success' => true, 'message' => 'Loan rejected successfully']);

        } catch (\Exception $e) {
            Log::error('Loan rejection failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to reject loan'], 500);
        }
    }

    public function getTransactions()
    {
        try {
            $transactions = Transaction::with('transactionType:id,name')
                ->select('id', 'member_id', 'amount', 'transaction_type_id', 'category_id', 'description', 'created_at')
                ->latest()
                ->paginate(25);
            $transactions->setCollection(
                $transactions->getCollection()->each->append('type')
            );

            return response()->json(['success' => true, 'data' => $transactions]);

        } catch (\Exception $e) {
            Log::error('Transactions fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load transactions'], 500);
        }
    }

    public function createTransaction(Request $request, TransactionPostingService $postingService)
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|string',
                'amount' => 'required|numeric|min:1',
                'type' => 'required|in:deposit,withdrawal,loan_payment,loanPayment',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validated['type'] === 'loanPayment') {
                $validated['type'] = 'loan_payment';
            }

            DB::beginTransaction();

            $resolvedMemberId = resolve_member_id($validated['member_id']);
            $member = $resolvedMemberId ? Member::find($resolvedMemberId) : null;
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid member',
                ], 400);
            }

            if ($validated['type'] === 'withdrawal' && $member->savings < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient savings balance'
                ], 400);
            }

            $transactionTypeId = TransactionType::query()->where('name', $validated['type'])->value('id');
            $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
            $categoryName = match ($validated['type']) {
                'deposit' => 'savings_deposit',
                'withdrawal' => 'savings_withdrawal',
                'loan_payment' => 'loan_payment',
                default => 'savings_deposit',
            };
            $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id');
            $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
            $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

            $balanceBefore = (float) ($member->balance ?? 0);
            $withdrawalFee = $validated['type'] === 'withdrawal'
                ? ($validated['amount'] * setting('withdrawal_fee', 0)) / 100
                : 0;
            $netAmount = max((float) ($validated['amount'] - $withdrawalFee), 0);
            $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
            $balanceAfter = $impact === 'credit'
                ? $balanceBefore + $netAmount
                : $balanceBefore - $netAmount;

            $transaction = Transaction::create([
                'member_id' => $member->id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'status_id' => $statusId,
                'amount' => $validated['amount'],
                'net_amount' => $netAmount,
                'fee' => $withdrawalFee,
                'currency_id' => $currencyId,
                'payment_method_id' => $paymentMethodId,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $validated['description'] ?? null,
                'transaction_date' => now(),
                'value_date' => now(),
            ]);
            $postingService->applyCategoryUpdates($transaction, $validated);

            DB::commit();
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to record transaction'], 500);
        }
    }

    public function getProjects()
    {
        try {
            $projects = Project::select('id', 'name', 'budget', 'status', 'progress', 'timeline', 'created_at')
                ->latest()
                ->get();

            return response()->json(['success' => true, 'data' => $projects]);

        } catch (\Exception $e) {
            Log::error('Projects fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load projects'], 500);
        }
    }

    public function createProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'budget' => 'required|numeric|min:1',
                'timeline' => 'required|date|after:today',
                'description' => 'nullable|string|max:1000'
            ]);

            $validated['status'] = 'planning';
            $validated['progress'] = 0;

            $project = Project::create($validated);
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project
            ], 201);

        } catch (\Exception $e) {
            Log::error('Project creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create project'], 500);
        }
    }

    public function deleteProject($id)
    {
        try {
            $project = Project::findOrFail($id);

            if ($project->status === 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete project in progress'
                ], 400);
            }

            $project->delete();
            Cache::flush();

            return response()->json(['success' => true, 'message' => 'Project deleted successfully']);

        } catch (\Exception $e) {
            Log::error('Project deletion failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete project'], 500);
        }
    }
}
