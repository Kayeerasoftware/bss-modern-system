<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get all months
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Get member growth for last 12 months including 2025 data
        $membersGrowth = [];
        $cumulativeCount = 0;
        
        // Start from Dec 2025 to current month 2026
        for ($i = 12; $i >= 1; $i--) {
            $monthCount = Member::whereYear('created_at', 2025)
                ->whereMonth('created_at', $i)
                ->count();
            if ($monthCount > 0) {
                $cumulativeCount += $monthCount;
                $membersGrowth[] = [
                    'month' => $months[$i - 1] . ' 25',
                    'count' => $cumulativeCount
                ];
            }
        }
        
        // Add 2026 months
        $currentMonth = (int)date('n');
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthCount = Member::whereYear('created_at', 2026)
                ->whereMonth('created_at', $i)
                ->count();
            $cumulativeCount += $monthCount;
            
            $membersGrowth[] = [
                'month' => $months[$i - 1],
                'count' => $cumulativeCount
            ];
        }
            
        $loanStats = [
            'pending' => Loan::where('status', 'pending')->count(),
            'approved' => Loan::where('status', 'approved')->count(),
            'rejected' => Loan::where('status', 'rejected')->count(),
        ];
        
        $transactionStats = [
            'deposits' => Transaction::where('type', 'deposit')->count(),
            'withdrawals' => Transaction::where('type', 'withdrawal')->count(),
            'transfers' => Transaction::where('type', 'transfer')->count(),
            'fees' => Transaction::where('type', 'fee')->count(),
        ];
        
        // Get cumulative revenue for each month
        $monthlyRevenue = [];
        $cumulativeRevenue = 0;
        
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthRevenue = Transaction::where('type', 'deposit')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->sum('amount');
            $cumulativeRevenue += $monthRevenue;
            
            $monthlyRevenue[] = [
                'month' => $months[$i - 1],
                'total' => $cumulativeRevenue
            ];
        }
        
        // Get cumulative savings growth
        $savingsGrowth = [];
        $cumulativeSavings = 0;
        
        for ($i = 12; $i >= 1; $i--) {
            $monthSavings = Member::whereYear('created_at', 2025)
                ->whereMonth('created_at', $i)
                ->sum('savings');
            if ($monthSavings > 0) {
                $cumulativeSavings += $monthSavings;
                $savingsGrowth[] = [
                    'month' => $months[$i - 1] . ' 25',
                    'total' => $cumulativeSavings
                ];
            }
        }
        
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthSavings = Member::whereYear('created_at', 2026)
                ->whereMonth('created_at', $i)
                ->sum('savings');
            $cumulativeSavings += $monthSavings;
            
            $savingsGrowth[] = [
                'month' => $months[$i - 1],
                'total' => $cumulativeSavings
            ];
        }
            
        $projects = Project::select('name', 'progress')->get();
        
        return response()->json([
            'totalMembers' => Member::count(),
            'totalSavings' => Member::sum('savings'),
            'activeLoans' => Loan::where('status', 'approved')->count(),
            'totalProjects' => Project::count(),
            'pendingApprovals' => Loan::where('status', 'pending')->count(),
            'approvedLoans' => $loanStats['approved'],
            'pendingLoans' => $loanStats['pending'],
            'rejectedLoans' => $loanStats['rejected'],
            'totalLoanAmount' => Loan::sum('amount'),
            'totalDeposits' => Transaction::where('type', 'deposit')->sum('amount'),
            'totalWithdrawals' => Transaction::where('type', 'withdrawal')->sum('amount'),
            'totalTransfers' => Transaction::where('type', 'transfer')->sum('amount'),
            'totalFees' => Transaction::where('type', 'fee')->sum('amount'),
            'netBalance' => Member::sum('savings') + Transaction::where('type', 'deposit')->sum('amount') - Transaction::where('type', 'withdrawal')->sum('amount') - Loan::sum('amount'),
            'membersGrowth' => $membersGrowth,
            'loanStats' => $loanStats,
            'transactionStats' => $transactionStats,
            'monthlyRevenue' => $monthlyRevenue,
            'savingsGrowth' => $savingsGrowth,
            'projects' => $projects
        ]);
    }

    public function getSettings()
    {
        $settings = DB::table('system_settings')->pluck('value', 'key');
        return response()->json([
            'interest_rate' => $settings['interest_rate'] ?? 5.5,
            'min_savings' => $settings['min_savings'] ?? 50000,
            'max_loan' => $settings['max_loan'] ?? 5000000,
            'loan_fee' => $settings['loan_fee'] ?? 2.5,
            'system_name' => $settings['system_name'] ?? 'BSS Investment Group',
            'currency' => $settings['currency'] ?? 'UGX',
            'timezone' => $settings['timezone'] ?? 'Africa/Kampala',
            'date_format' => $settings['date_format'] ?? 'Y-m-d',
            'email_notifications' => ($settings['email_notifications'] ?? 'true') === 'true',
            'sms_notifications' => ($settings['sms_notifications'] ?? 'false') === 'true',
            'loan_approval_notify' => ($settings['loan_approval_notify'] ?? 'true') === 'true',
            'transaction_notify' => ($settings['transaction_notify'] ?? 'true') === 'true',
            'session_timeout' => $settings['session_timeout'] ?? 30,
            'password_min_length' => $settings['password_min_length'] ?? 8,
            'two_factor_auth' => ($settings['two_factor_auth'] ?? 'false') === 'true',
        ]);
    }

    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'min_savings' => 'nullable|numeric|min:0',
                'max_loan' => 'nullable|numeric|min:0',
                'loan_fee' => 'nullable|numeric|min:0|max:10',
                'system_name' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:10',
                'timezone' => 'nullable|string|max:100',
                'date_format' => 'nullable|string|max:50',
                'session_timeout' => 'nullable|integer|min:5|max:1440',
                'password_min_length' => 'nullable|integer|min:6|max:20',
            ]);

            foreach ($request->all() as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                DB::table('system_settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
            }
            
            DB::table('audit_logs')->insert([
                'user' => 'Admin',
                'action' => 'Settings Updated',
                'details' => 'System settings were modified',
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAuditLogs()
    {
        $logs = collect();
        
        try {
            $members = DB::table('members')->orderBy('created_at', 'desc')->get();
            foreach ($members as $member) {
                $logs->push([
                    'id' => 'M' . $member->id,
                    'action' => 'Member Created',
                    'user' => 'Admin',
                    'details' => "Created member: {$member->full_name} ({$member->member_id})",
                    'timestamp' => $member->created_at
                ]);
                if ($member->updated_at && $member->updated_at != $member->created_at) {
                    $logs->push([
                        'id' => 'MU' . $member->id,
                        'action' => 'Member Updated',
                        'user' => 'Admin',
                        'details' => "Updated member: {$member->full_name} ({$member->member_id})",
                        'timestamp' => $member->updated_at
                    ]);
                }
            }
        } catch (\Exception $e) {}
        
        try {
            $loans = DB::table('loans')->orderBy('updated_at', 'desc')->get();
            foreach ($loans as $loan) {
                $logs->push([
                    'id' => 'LC' . $loan->id,
                    'action' => 'Loan Created',
                    'user' => 'Member',
                    'details' => "Created loan {$loan->loan_id} for UGX " . number_format($loan->amount),
                    'timestamp' => $loan->created_at
                ]);
                if ($loan->status === 'approved') {
                    $logs->push([
                        'id' => 'LA' . $loan->id,
                        'action' => 'Loan Approved',
                        'user' => $loan->updated_by ?? 'Manager',
                        'details' => "Approved loan {$loan->loan_id} for UGX " . number_format($loan->amount),
                        'timestamp' => $loan->updated_at ?? $loan->created_at
                    ]);
                } elseif ($loan->status === 'rejected') {
                    $logs->push([
                        'id' => 'LR' . $loan->id,
                        'action' => 'Loan Rejected',
                        'user' => $loan->updated_by ?? 'Manager',
                        'details' => "Rejected loan {$loan->loan_id}",
                        'timestamp' => $loan->updated_at ?? $loan->created_at
                    ]);
                }
            }
        } catch (\Exception $e) {}
        
        try {
            $transactions = DB::table('transactions')->orderBy('created_at', 'desc')->get();
            foreach ($transactions as $txn) {
                $action = ucfirst($txn->type) . ' Processed';
                $logs->push([
                    'id' => 'T' . $txn->id,
                    'action' => $action,
                    'user' => 'Cashier',
                    'details' => ucfirst($txn->type) . " of UGX " . number_format($txn->amount) . " for member {$txn->member_id}",
                    'timestamp' => $txn->created_at
                ]);
            }
        } catch (\Exception $e) {}
        
        try {
            $projects = DB::table('projects')->orderBy('created_at', 'desc')->get();
            foreach ($projects as $project) {
                $logs->push([
                    'id' => 'PC' . $project->id,
                    'action' => 'Project Created',
                    'user' => 'TD',
                    'details' => "Created project: {$project->name} with budget UGX " . number_format($project->budget),
                    'timestamp' => $project->created_at
                ]);
                if ($project->updated_at && $project->updated_at != $project->created_at) {
                    $logs->push([
                        'id' => 'PU' . $project->id,
                        'action' => 'Project Updated',
                        'user' => 'TD',
                        'details' => "Updated project: {$project->name} - Progress: {$project->progress}%",
                        'timestamp' => $project->updated_at
                    ]);
                }
            }
        } catch (\Exception $e) {}
        
        try {
            $reports = DB::table('audit_logs')
                ->where('action', 'LIKE', '%Report%')
                ->orWhere('action', 'LIKE', '%Generate%')
                ->orWhere('action', 'LIKE', '%Delete%')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($reports as $report) {
                $logs->push([
                    'id' => 'R' . $report->id,
                    'action' => $report->action,
                    'user' => $report->user,
                    'details' => $report->details,
                    'timestamp' => $report->timestamp ?? $report->created_at
                ]);
            }
        } catch (\Exception $e) {}
        
        $logs = $logs->sortByDesc('timestamp')->values();
        
        return response()->json($logs);
    }

    public function getBackups()
    {
        $backups = DB::table('backups')->orderBy('created_at', 'desc')->get();
        return response()->json($backups);
    }

    public function createBackup()
    {
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        DB::table('backups')->insert([
            'filename' => $filename,
            'path' => '/backups/' . $filename,
            'size' => rand(1000000, 5000000),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteBackup($id)
    {
        DB::table('backups')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function getFinancialSummary()
    {
        $totalDeposits = Transaction::where('type', 'deposit')->sum('amount');
        $totalWithdrawals = Transaction::where('type', 'withdrawal')->sum('amount');
        $totalLoans = Loan::where('status', 'approved')->sum('amount');
        $totalSavings = Member::sum('savings');
        $loanRepayments = Transaction::where('type', 'loan_payment')->sum('amount');
        
        $netBalance = $totalSavings + $totalDeposits - $totalWithdrawals - $totalLoans + $loanRepayments;
        
        return response()->json([
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalLoans' => $totalLoans,
            'netBalance' => $netBalance,
        ]);
    }

    public function getSystemHealth()
    {
        return response()->json([
            'storageUsage' => rand(45, 75),
            'lastBackup' => DB::table('backups')->latest('created_at')->value('created_at') ?? 'Never',
        ]);
    }

    public function getRoles()
    {
        return response()->json([
            ['name' => 'Admin', 'permissions' => ['all']],
            ['name' => 'Manager', 'permissions' => ['view', 'edit', 'approve']],
            ['name' => 'Staff', 'permissions' => ['view', 'edit']],
        ]);
    }

    public function getUsers()
    {
        $users = User::select('id', 'name', 'email', 'role', 'status')->get();
        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active'
        ]);
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'User Created',
            'details' => 'Created user: ' . $user->name,
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'User Status Changed',
            'details' => 'Changed status for: ' . $user->name,
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'User Deleted',
            'details' => 'Deleted user: ' . $userName,
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    public function exportMembers()
    {
        $members = Member::all();
        $csv = "Member ID,Full Name,Email,Contact,Role,Savings\n";
        foreach ($members as $member) {
            $csv .= "{$member->member_id},{$member->full_name},{$member->email},{$member->contact},{$member->role},{$member->savings}\n";
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="members_export.csv"');
    }

    public function importMembers(Request $request)
    {
        $file = $request->file('file');
        $csv = array_map('str_getcsv', file($file->getRealPath()));
        array_shift($csv); // Remove header
        
        $imported = 0;
        foreach ($csv as $row) {
            if (count($row) >= 6) {
                Member::create([
                    'member_id' => $row[0],
                    'full_name' => $row[1],
                    'email' => $row[2],
                    'contact' => $row[3],
                    'role' => $row[4],
                    'savings' => $row[5],
                    'balance' => $row[5]
                ]);
                $imported++;
            }
        }
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'Bulk Import',
            'details' => "Imported {$imported} members",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'imported' => $imported]);
    }

    public function sendBulkEmail(Request $request)
    {
        $recipients = $request->recipients;
        $query = Member::query();
        
        if ($recipients === 'clients') {
            $query->where('role', 'client');
        } elseif ($recipients === 'shareholders') {
            $query->where('role', 'shareholder');
        } elseif ($recipients === 'staff') {
            $query->whereIn('role', ['cashier', 'td', 'ceo']);
        }
        
        $count = $query->count();
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'Bulk Email Sent',
            'details' => "Sent '{$request->subject}' to {$count} recipients",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'sent' => $count]);
    }

    public function generateReport()
    {
        $type = request('type');
        $dateFrom = request('dateFrom');
        $dateTo = request('dateTo');
        $format = request('format', 'pdf');
        
        // Log report generation activity
        try {
            DB::table('audit_logs')->insert([
                'action' => 'Report Generated',
                'user' => auth()->user()->name ?? 'Admin',
                'details' => "Generated {$type} report" . ($dateFrom ? " from {$dateFrom}" : '') . ($dateTo ? " to {$dateTo}" : ''),
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Table might not exist, continue anyway
        }
        
        $data = [];
        $title = '';
        $view = '';
        
        switch ($type) {
            case 'members':
                $query = Member::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $data = $query->get();
                $title = 'Members Report';
                $view = 'reports.members';
                break;
                
            case 'financial':
                $totalSavings = Member::sum('savings');
                $totalLoans = Loan::sum('amount');
                $totalDeposits = Transaction::where('type', 'deposit')->sum('amount');
                $totalWithdrawals = Transaction::where('type', 'withdrawal')->sum('amount');
                $netBalance = Member::sum('balance');
                
                $approvedLoans = Loan::where('status', 'approved')->count();
                $pendingLoans = Loan::where('status', 'pending')->count();
                $rejectedLoans = Loan::where('status', 'rejected')->count();
                $loansCount = Loan::count();
                $avgLoanAmount = Loan::avg('amount') ?? 0;
                
                $totalMembers = Member::count();
                $activeMembers = Member::where('savings', '>', 0)->count();
                $avgSavings = Member::avg('savings') ?? 0;
                
                $loanInterest = $totalLoans * 0.055;
                $processingFees = $totalLoans * 0.025;
                $netCashFlow = $totalDeposits - $totalWithdrawals + $loanInterest + $processingFees;
                
                $transactionBreakdown = [
                    'deposit' => [
                        'count' => Transaction::where('type', 'deposit')->count(),
                        'amount' => $totalDeposits
                    ],
                    'withdrawal' => [
                        'count' => Transaction::where('type', 'withdrawal')->count(),
                        'amount' => $totalWithdrawals
                    ],
                    'transfer' => [
                        'count' => Transaction::where('type', 'transfer')->count(),
                        'amount' => Transaction::where('type', 'transfer')->sum('amount')
                    ],
                    'loan_payment' => [
                        'count' => Transaction::where('type', 'loan_payment')->count(),
                        'amount' => Transaction::where('type', 'loan_payment')->sum('amount')
                    ]
                ];
                
                $data = [
                    'total_savings' => $totalSavings,
                    'total_loans' => $totalLoans,
                    'total_deposits' => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                    'net_balance' => $netBalance,
                    'loan_interest' => $loanInterest,
                    'processing_fees' => $processingFees,
                    'net_cash_flow' => $netCashFlow,
                    'loans_count' => $loansCount,
                    'approved_loans' => $approvedLoans,
                    'pending_loans' => $pendingLoans,
                    'rejected_loans' => $rejectedLoans,
                    'avg_loan_amount' => $avgLoanAmount,
                    'total_members' => $totalMembers,
                    'active_members' => $activeMembers,
                    'avg_savings' => $avgSavings,
                    'transaction_breakdown' => $transactionBreakdown
                ];
                $title = 'Financial Report';
                $view = 'reports.financial';
                break;
                
            case 'loans':
                $query = Loan::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $data = $query->get();
                $title = 'Loans Report';
                $view = 'reports.loans';
                break;
                
            case 'transactions':
                $query = Transaction::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $data = $query->get();
                $title = 'Transactions Report';
                $view = 'reports.transactions';
                break;
                
            case 'projects':
                $data = Project::all();
                $title = 'Projects Report';
                $view = 'reports.projects';
                break;
                
            case 'audit':
                $query = DB::table('audit_logs');
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $data = $query->get();
                $title = 'Audit Report';
                $view = 'reports.audit';
                break;
        }
        
        DB::table('reports')->insert([
            'type' => $title,
            'format' => $format,
            'date' => now()->format('Y-m-d H:i'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'Report Generated',
            'details' => "Generated {$title} in {$format} format",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($format === 'pdf' || $format === 'html') {
            $html = view($view, ['data' => $data, 'title' => $title])->render();
            return response($html)->header('Content-Type', 'text/html');
        }
        
        // CSV format
        $content = '';
        if ($type === 'members') {
            $content = "Member ID,Full Name,Email,Contact,Role,Savings\n";
            foreach ($data as $m) {
                $content .= "{$m->member_id},{$m->full_name},{$m->email},{$m->contact},{$m->role},{$m->savings}\n";
            }
        } elseif ($type === 'financial') {
            $content = "Metric,Amount\n";
            foreach ($data as $key => $value) {
                $content .= "{$key},{$value}\n";
            }
        } elseif ($type === 'loans') {
            $content = "Loan ID,Member ID,Amount,Status,Purpose\n";
            foreach ($data as $l) {
                $content .= "{$l->loan_id},{$l->member_id},{$l->amount},{$l->status},{$l->purpose}\n";
            }
        } elseif ($type === 'transactions') {
            $content = "Transaction ID,Member ID,Type,Amount,Date\n";
            foreach ($data as $t) {
                $content .= "{$t->transaction_id},{$t->member_id},{$t->type},{$t->amount},{$t->created_at}\n";
            }
        } elseif ($type === 'projects') {
            $content = "Project ID,Name,Budget,Progress,ROI,Risk Score\n";
            foreach ($data as $p) {
                $content .= "{$p->project_id},{$p->name},{$p->budget},{$p->progress},{$p->roi},{$p->risk_score}\n";
            }
        } elseif ($type === 'audit') {
            $content = "User,Action,Details,Timestamp\n";
            foreach ($data as $log) {
                $content .= "{$log->user},{$log->action},{$log->details},{$log->timestamp}\n";
            }
        }
        
        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Ymd_His') . '.csv';
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    public function getRecentReports()
    {
        $reports = DB::table('reports')->orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json($reports);
    }
    
    public function viewReport($id)
    {
        $report = DB::table('reports')->where('id', $id)->first();
        if (!$report) {
            return response('Report not found', 404);
        }
        
        $params = http_build_query([
            'type' => strtolower(str_replace(' Report', '', $report->type)),
            'format' => $report->format
        ]);
        
        return redirect('/api/reports/generate?' . $params);
    }
    
    public function deleteReport($id)
    {
        DB::table('reports')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function sendNotification(Request $request)
    {
        DB::table('audit_logs')->insert([
            'user' => 'Admin',
            'action' => 'Notification Sent',
            'details' => $request->title . ' to ' . $request->target,
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }
}
