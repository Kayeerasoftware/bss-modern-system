<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalMembers' => Member::count(),
            'totalSavings' => Member::sum('savings'),
            'activeLoans' => Loan::where('status', 'approved')->count(),
            'totalProjects' => Project::count(),
            'pendingLoans' => Loan::where('status', 'pending')->count(),
            'totalTransactions' => Transaction::count(),
            'monthlyDeposits' => Transaction::where('type', 'deposit')->whereMonth('created_at', now()->month)->sum('amount'),
            'monthlyWithdrawals' => Transaction::where('type', 'withdrawal')->whereMonth('created_at', now()->month)->sum('amount'),
            'roleDistribution' => [
                'client' => Member::where('role', 'client')->count(),
                'shareholder' => Member::where('role', 'shareholder')->count(),
                'cashier' => Member::where('role', 'cashier')->count(),
                'td' => Member::where('role', 'td')->count(),
                'ceo' => Member::where('role', 'ceo')->count()
            ]
        ];

        return response()->json($stats);
    }

    public function getMembers()
    {
        return response()->json(Member::orderBy('created_at', 'desc')->get());
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

        return response()->json($member);
    }

    public function updateMember(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $member->update($request->all());
        return response()->json($member);
    }

    public function deleteMember($id)
    {
        Member::findOrFail($id)->delete();
        return response()->json(['message' => 'Member deleted successfully']);
    }

    public function getLoans()
    {
        return response()->json(Loan::with('member')->orderBy('created_at', 'desc')->get());
    }

    public function approveLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'approved']);
        
        $member = Member::where('member_id', $loan->member_id)->first();
        $member->increment('loan', $loan->amount);

        return response()->json($loan);
    }

    public function rejectLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'rejected']);
        return response()->json($loan);
    }

    public function getTransactions()
    {
        return response()->json(Transaction::with('member')->orderBy('created_at', 'desc')->get());
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

        return response()->json($transaction->load('member'));
    }

    public function getProjects()
    {
        return response()->json(Project::orderBy('created_at', 'desc')->get());
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

        return response()->json($project);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        return response()->json($project);
    }

    public function deleteProject($id)
    {
        Project::findOrFail($id)->delete();
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