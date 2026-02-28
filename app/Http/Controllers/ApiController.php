<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\User;
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
                return [
                    'totalMembers' => Member::count(),
                    'totalSavings' => Member::sum('savings') + Member::sum('savings_balance'),
                    'activeLoans' => Loan::where('status', 'approved')->count(),
                    'totalProjects' => Project::count(),
                    'totalBalance' => Member::selectRaw('SUM(savings - loan) as total_balance')->first()->total_balance ?? 0,
                    'pendingLoans' => Loan::where('status', 'pending')->count(),
                    'completedProjects' => Project::where('status', 'completed')->count(),
                    'monthlyDeposits' => Transaction::where('type', 'deposit')
                        ->whereMonth('created_at', now()->month)
                        ->sum('amount'),
                    'monthlyWithdrawals' => Transaction::where('type', 'withdrawal')
                        ->whereMonth('created_at', now()->month)
                        ->sum('amount'),
                    'condolenceFund' => Cache::get('condolence_fund', 2000000)
                ];
            });

            // Fresh data
            $data['recentTransactions'] = Transaction::with(['member:id,member_id,full_name'])
                ->select('id', 'member_id', 'amount', 'type', 'created_at')
                ->latest()
                ->limit(10)
                ->get();

            $data['recentLoans'] = Loan::with(['member:id,member_id,full_name'])
                ->select('id', 'member_id', 'amount', 'status', 'created_at')
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
                      ->orWhere('member_id', 'LIKE', "%{$search}%");
                });
            }

            $members = $query->select('id', 'member_id', 'full_name', 'savings', 'loan', 'balance', 'created_at')
                ->latest()
                ->paginate(15);

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
                'member_id' => 'required|string|unique:members,member_id',
                'phone' => 'nullable|string|max:20',
                'email' => 'required|email|unique:members,email|unique:users,email|max:255',
                'address' => 'nullable|string|max:500',
                'role' => 'nullable|in:admin,client,cashier,td,ceo,shareholder',
                'password' => 'nullable|string|min:6',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password123'),
                'role' => $validated['role'] ?? 'client',
                'status' => 'active',
                'is_active' => true,
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['address'] ?? null,
            ]);

            $member = Member::create([
                'member_id' => $validated['member_id'],
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
            $loans = Loan::select('id', 'member_id', 'amount', 'status', 'purpose', 'repayment_months', 'created_at')
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
                'member_id' => 'required|exists:members,member_id',
                'amount' => 'required|numeric|min:1000|max:500000',
                'purpose' => 'required|string|max:255',
                'repayment_months' => 'required|integer|in:6,12,24'
            ]);

            DB::beginTransaction();

            $member = Member::where('member_id', $validated['member_id'])->first();

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

            $interest = $validated['amount'] * 0.02 * $validated['repayment_months'];
            $monthlyPayment = ($validated['amount'] + $interest) / $validated['repayment_months'];

            $loan = Loan::create([
                'member_id' => $validated['member_id'],
                'amount' => $validated['amount'],
                'purpose' => $validated['purpose'],
                'repayment_months' => $validated['repayment_months'],
                'interest' => $interest,
                'monthly_payment' => $monthlyPayment,
                'status' => 'pending'
            ]);

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

            $loan->update(['status' => 'approved']);

            $member = $loan->member;
            $member->increment('loan', $loan->amount);

            Transaction::create([
                'member_id' => $loan->member_id,
                'amount' => $loan->amount,
                'type' => 'loan_request',
                'description' => "Loan approved and disbursed - ID: {$loan->id}",
                'status' => 'completed',
            ]);

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

            $loan->update(['status' => 'rejected']);
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
            $transactions = Transaction::select('id', 'member_id', 'amount', 'type', 'description', 'created_at')
                ->latest()
                ->paginate(25);

            return response()->json(['success' => true, 'data' => $transactions]);

        } catch (\Exception $e) {
            Log::error('Transactions fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load transactions'], 500);
        }
    }

    public function createTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,member_id',
                'amount' => 'required|numeric|min:1',
                'type' => 'required|in:deposit,withdrawal,loan_payment,loanPayment',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validated['type'] === 'loanPayment') {
                $validated['type'] = 'loan_payment';
            }

            DB::beginTransaction();

            $member = Member::where('member_id', $validated['member_id'])->first();

            if ($validated['type'] === 'withdrawal' && $member->savings < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient savings balance'
                ], 400);
            }

            if ($validated['type'] === 'deposit') {
                $member->increment('savings', $validated['amount']);
            } elseif ($validated['type'] === 'withdrawal') {
                $member->decrement('savings', $validated['amount']);
            } elseif ($validated['type'] === 'loan_payment') {
                $member->decrement('loan', $validated['amount']);
            }

            $transaction = Transaction::create($validated);

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
