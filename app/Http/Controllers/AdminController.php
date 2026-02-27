<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Notification;
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
            $memberSummary = Member::query()
                ->selectRaw('COUNT(*) as total_members, COALESCE(SUM(savings),0) as total_savings, SUM(CASE WHEN role = "client" THEN 1 ELSE 0 END) as client_count, SUM(CASE WHEN role = "shareholder" THEN 1 ELSE 0 END) as shareholder_count, SUM(CASE WHEN role = "cashier" THEN 1 ELSE 0 END) as cashier_count, SUM(CASE WHEN role = "td" THEN 1 ELSE 0 END) as td_count, SUM(CASE WHEN role = "ceo" THEN 1 ELSE 0 END) as ceo_count')
                ->first();

            $loanSummary = Loan::query()
                ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as active_loans, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_loans')
                ->first();

            $transactionSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions, COALESCE(SUM(CASE WHEN type = "deposit" AND created_at >= ? THEN amount ELSE 0 END),0) as monthly_deposits, COALESCE(SUM(CASE WHEN type = "withdrawal" AND created_at >= ? THEN amount ELSE 0 END),0) as monthly_withdrawals', [now()->startOfMonth(), now()->startOfMonth()])
                ->first();

            return [
                'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                'totalSavings' => (float) ($memberSummary->total_savings ?? 0),
                'activeLoans' => (int) ($loanSummary->active_loans ?? 0),
                'totalProjects' => Project::query()->count(),
                'pendingLoans' => (int) ($loanSummary->pending_loans ?? 0),
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
        $member = Member::create([
            'member_id' => $request->member_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'location' => $request->location,
            'occupation' => $request->occupation,
            'contact' => $request->contact,
            'role' => $request->role,
            'savings' => $request->savings ?? 0,
            'loan' => 0,
            'password' => Hash::make($request->password ?? 'password123')
        ]);

        $this->clearAdminApiCaches();

        return response()->json($member);
    }

    public function updateMember(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $member->update($request->all());
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
        $loan->update(['status' => 'approved']);
        
        $member = Member::where('member_id', $loan->member_id)->first();
        $member->increment('loan', $loan->amount);
        $this->clearAdminApiCaches();

        return response()->json($loan);
    }

    public function rejectLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'rejected']);
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

    public function createTransaction(Request $request)
    {
        $transaction = Transaction::create([
            'transaction_id' => 'TXN' . time(),
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description
        ]);

        $member = Member::where('member_id', $request->member_id)->first();
        if ($request->type === 'deposit') {
            $member->increment('savings', $request->amount);
        } elseif ($request->type === 'withdrawal') {
            $member->decrement('savings', $request->amount);
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
