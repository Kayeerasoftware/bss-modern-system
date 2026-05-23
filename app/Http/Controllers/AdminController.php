<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Notification;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\Financial\TransactionPostingService;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    private function clearAdminApiCaches(): void
    {
        Cache::forget('api_admin:dashboard:v1');
        Cache::forget('api_admin:members:v1');
        Cache::forget('api_admin:loans:v1');
        Cache::forget('api_admin:transactions:v1');
        Cache::forget('api_admin:projects:v1');
    }

    public function dashboard()
    {
        $stats = Cache::remember('api_admin:dashboard:v1', now()->addSeconds(45), static function () {
            $viewStats = app(DashboardStatsService::class)->get();
            $memberSummary = (object) [
                'total_members' => (int) Member::query()->count(),
                'total_savings' => (float) DB::table('savings_accounts')->sum('current_balance'),
                'client_count' => (int) DB::table('member_roles')->join('roles', 'roles.id', '=', 'member_roles.role_id')->where('roles.name', 'client')->count(),
                'shareholder_count' => (int) DB::table('member_roles')->join('roles', 'roles.id', '=', 'member_roles.role_id')->where('roles.name', 'shareholder')->count(),
                'cashier_count' => (int) DB::table('member_roles')->join('roles', 'roles.id', '=', 'member_roles.role_id')->where('roles.name', 'cashier')->count(),
                'td_count' => (int) DB::table('member_roles')->join('roles', 'roles.id', '=', 'member_roles.role_id')->where('roles.name', 'td')->count(),
                'ceo_count' => (int) DB::table('member_roles')->join('roles', 'roles.id', '=', 'member_roles.role_id')->where('roles.name', 'ceo')->count(),
            ];

            $transactionSummary = Transaction::query()
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->selectRaw('COUNT(*) as total_transactions')
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ('savings_deposit','transfer_in','loan_disbursement') AND transactions.created_at >= ? THEN COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0) ELSE 0 END),0) as monthly_deposits", [now()->startOfMonth()])
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ('savings_withdrawal','transfer_out','fundraising_transfer') AND transactions.created_at >= ? THEN COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0) ELSE 0 END),0) as monthly_withdrawals", [now()->startOfMonth()])
                ->first();

            return [
                'totalMembers' => (int) ($viewStats['total_members'] ?? $memberSummary->total_members ?? 0),
                'totalSavings' => (float) ($viewStats['total_system_balance'] ?? $memberSummary->total_savings ?? 0),
                'activeLoans' => (int) ($viewStats['active_loans_count'] ?? 0),
                'totalProjects' => Project::query()->count(),
                'pendingLoans' => (int) ($viewStats['pending_loans_count'] ?? 0),
                'totalTransactions' => (int) ($transactionSummary->total_transactions ?? 0),
                'monthlyDeposits' => (float) ($transactionSummary->monthly_deposits ?? 0),
                'monthlyWithdrawals' => (float) ($transactionSummary->monthly_withdrawals ?? 0),
                'roleDistribution' => [
                    'client' => (int) ($memberSummary->client_count ?? 0),
                    'shareholder' => (int) ($memberSummary->shareholder_count ?? 0),
                    'cashier' => (int) ($memberSummary->cashier_count ?? 0),
                    'td' => (int) ($memberSummary->td_count ?? 0),
                    'ceo' => (int) ($memberSummary->ceo_count ?? 0),
                ],
            ];
        });

        return response()->json($stats);
    }

    public function getMembers()
    {
        $members = Cache::remember('api_admin:members:v1', now()->addSeconds(30), static function () {
            return Member::query()->orderBy('created_at', 'desc')->get();
        });

        return response()->json($members);
    }

    public function createMember(Request $request)
    {
        $user = User::withoutEvents(function () use ($request) {
            return User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => $request->role ?? 'client',
                'status' => 'active',
                'is_active' => true,
                'phone' => $request->contact,
                'location' => $request->location,
            ]);
        });

        $member = new Member();
        $member->member_id = $request->member_id;
        $member->full_name = $request->full_name;
        $member->email = $request->email;
        $member->place_of_birth = $request->location;
        $member->occupation = $request->occupation;
        $member->primary_phone = $request->contact;
        $member->password = $user->password;
        $member->user_id = $user->id;
        $member->membership_status = 'active';
        $member->join_date = now()->toDateString();
        Member::queueOpeningSavings($member, (float) ($request->savings ?? 0));
        $member->save();
        if (!empty($request->role)) {
            $member->assignRole($request->role);
        }

        $this->clearAdminApiCaches();

        return response()->json($member);
    }

    public function updateMember(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $payload = $request->except(['savings', 'savings_transaction_id']);
        $member->update($payload);
        $this->clearAdminApiCaches();
        return response()->json($member);
    }

    public function deleteMember($id)
    {
        Member::findOrFail($id)->delete();
        $this->clearAdminApiCaches();
        return response()->json(['message' => 'Member deleted successfully']);
    }

    public function getLoans()
    {
        $loans = Cache::remember('api_admin:loans:v1', now()->addSeconds(30), static function () {
            return Loan::query()->with('member')->orderBy('created_at', 'desc')->get();
        });

        return response()->json($loans);
    }

    public function approveLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $approvedStatusId = \App\Models\LoanStatus::query()->where('name', 'approved')->value('id');
        $loan->update(['status_id' => $approvedStatusId ?? $loan->status_id]);

        if ($loan->application_id) {
            \App\Models\LoanApplication::query()
                ->whereKey($loan->application_id)
                ->update([
                    'status_id' => $approvedStatusId ?? $loan->status_id,
                    'decision_by' => auth()->id(),
                    'decision_date' => now(),
                ]);
        }
        $this->clearAdminApiCaches();

        return response()->json($loan);
    }

    public function rejectLoan($id)
    {
        $loan = Loan::findOrFail($id);
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
        $this->clearAdminApiCaches();
        return response()->json($loan);
    }

    public function getTransactions()
    {
        $transactions = Cache::remember('api_admin:transactions:v1', now()->addSeconds(30), static function () {
            return Transaction::query()->with('member')->orderBy('created_at', 'desc')->get();
        });

        return response()->json($transactions);
    }

    public function createTransaction(Request $request, TransactionPostingService $postingService)
    {
        $memberId = resolve_member_id($request->member_id);
        if (!$memberId) {
            return response()->json(['message' => 'Invalid member'], 422);
        }

        $transactionTypeId = TransactionType::query()->where('name', $request->type)->value('id');
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id');
        $categoryName = match ($request->type) {
            'deposit' => 'savings_deposit',
            'withdrawal' => 'savings_withdrawal',
            'transfer' => 'transfer_out',
            default => 'savings_deposit',
        };
        $categoryId = TransactionCategory::query()->where('name', $categoryName)->value('id');
        $paymentMethodId = PaymentMethod::query()->where('name', 'cash')->value('id') ?? PaymentMethod::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $member = Member::find($memberId);
        $balanceBefore = (float) ($member?->balance ?? 0);
        $withdrawalFee = $request->type === 'withdrawal'
            ? ($request->amount * setting('withdrawal_fee', 0)) / 100
            : 0;
        $netAmount = max((float) ($request->amount - $withdrawalFee), 0);
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'credit'
            ? $balanceBefore + $netAmount
            : $balanceBefore - $netAmount;

        $transaction = null;
        try {
            DB::transaction(function () use (
                $memberId,
                $transactionTypeId,
                $categoryId,
                $statusId,
                $paymentMethodId,
                $currencyId,
                $request,
                $balanceBefore,
                $balanceAfter,
                $netAmount,
                $withdrawalFee,
                &$transaction,
                $postingService
            ): void {
                $transaction = Transaction::create([
                    'transaction_id' => 'TXN' . time(),
                    'member_id' => $memberId,
                    'transaction_type_id' => $transactionTypeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $request->amount,
                    'net_amount' => $netAmount,
                    'fee' => $withdrawalFee,
                    'currency_id' => $currencyId,
                    'payment_method_id' => $paymentMethodId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $request->description,
                    'transaction_date' => now(),
                    'value_date' => now(),
                    'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
                    'processed_at' => now(),
                ]);

                $postingService->applyCategoryUpdates($transaction, $request->all());
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $this->clearAdminApiCaches();

        return response()->json($transaction->load('member'));
    }

    public function getProjects()
    {
        $projects = Cache::remember('api_admin:projects:v1', now()->addSeconds(30), static function () {
            return Project::query()->orderBy('created_at', 'desc')->get();
        });

        return response()->json($projects);
    }

    public function createProject(Request $request)
    {
        $project = Project::create([
            'project_id' => 'PRJ' . time(),
            'name' => $request->name,
            'budget' => $request->budget,
            'timeline' => $request->timeline,
            'description' => $request->description,
            'progress' => $request->progress ?? 0,
            'roi' => $request->roi ?? 0,
            'risk_score' => $request->risk_score ?? 0
        ]);

        $this->clearAdminApiCaches();

        return response()->json($project);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        $this->clearAdminApiCaches();
        return response()->json($project);
    }

    public function deleteProject($id)
    {
        Project::findOrFail($id)->delete();
        $this->clearAdminApiCaches();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    public function sendNotification(Request $request)
    {
        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'roles' => $request->roles,
            'created_by' => 'admin'
        ]);

        return response()->json($notification);
    }

    public function getSystemSettings()
    {
        return response()->json([
            'interest_rate' => 10,
            'loan_processing_fee' => 2,
            'minimum_savings' => 100000,
            'maximum_loan' => 10000000,
            'company_name' => 'BSS Investment Group',
            'currency' => 'UGX'
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        // In a real app, store these in a settings table
        return response()->json(['message' => 'Settings updated successfully']);
    }
}
