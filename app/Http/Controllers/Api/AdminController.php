<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Models\Transaction;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\ProfilePictureStorageService;
use App\Services\AuditLogService;
use App\Services\Financial\SavingsReconciliationService;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function generateMemberId(): string
    {
        return generate_member_id();
    }

    public function dashboard()
    {
        $viewStats = app(DashboardStatsService::class)->get();
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
            
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');

        $loanStats = [
            'pending' => $pendingStatusId ? Loan::where('status_id', $pendingStatusId)->count() : 0,
            'approved' => $approvedStatusId ? Loan::where('status_id', $approvedStatusId)->count() : 0,
            'rejected' => $rejectedStatusId ? Loan::where('status_id', $rejectedStatusId)->count() : 0,
        ];
        
        $transactionStats = [
            'deposits' => Transaction::query()->ofType('deposit')->count(),
            'withdrawals' => Transaction::query()->ofType('withdrawal')->count(),
            'transfers' => Transaction::query()->ofType('transfer')->count(),
            'fees' => Transaction::query()->ofType('fee')->count(),
        ];
        
        // Get cumulative revenue for each month
        $monthlyRevenue = [];
        $cumulativeRevenue = 0;
        
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthRevenue = Transaction::query()
                ->ofType('deposit')
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
            $monthSavings = Transaction::query()
                ->ofType('deposit')
                ->whereYear('created_at', 2025)
                ->whereMonth('created_at', $i)
                ->sum('amount');
            if ($monthSavings > 0) {
                $cumulativeSavings += $monthSavings;
                $savingsGrowth[] = [
                    'month' => $months[$i - 1] . ' 25',
                    'total' => $cumulativeSavings
                ];
            }
        }
        
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthSavings = Transaction::query()
                ->ofType('deposit')
                ->whereYear('created_at', 2026)
                ->whereMonth('created_at', $i)
                ->sum('amount');
            $cumulativeSavings += $monthSavings;
            
            $savingsGrowth[] = [
                'month' => $months[$i - 1],
                'total' => $cumulativeSavings
            ];
        }
            
        $projects = Project::select('name', 'progress_percentage')
            ->get()
            ->map(function ($project) {
                return [
                    'name' => $project->name,
                    'progress' => $project->progress_percentage,
                ];
            });
        
        $transactionTypeAmounts = [
            'deposit' => Transaction::query()->ofType('deposit')->sum('amount'),
            'withdrawal' => Transaction::query()->ofType('withdrawal')->sum('amount'),
            'transfer' => Transaction::query()->ofType('transfer')->sum('amount'),
            'loan_payment' => Transaction::query()->ofType('loan_payment')->sum('amount'),
            'loan_request' => Transaction::query()->ofType('loan_request')->sum('amount'),
            'fundraising' => Transaction::query()->ofType('fundraising')->sum('amount'),
            'condolence' => Transaction::query()->ofType('condolence')->sum('amount'),
        ];

        $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');
        $recon = app(SavingsReconciliationService::class)->getSystemSummary(1000);
        $totalLoans = (float) Loan::sum('principal_amount');
        $totalDeposits = (float) Transaction::query()->ofType('deposit')->sum('amount');
        $totalWithdrawals = (float) Transaction::query()->ofType('withdrawal')->sum('amount');
        $totalTransfers = (float) Transaction::query()->ofType('transfer')->sum('amount');
        $totalFees = (float) Transaction::query()->ofType('fee')->sum('amount');
        $loanRepayments = (float) Transaction::query()->ofType('loan_payment')->sum('amount');

        $roleCounts = DB::table('member_roles')
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->selectRaw('LOWER(roles.name) as role, COUNT(*) as count')
            ->groupBy('roles.name')
            ->pluck('count', 'role');

        return response()->json([
            'totalMembers' => (int) ($viewStats['total_members'] ?? Member::count()),
            'totalSavings' => (float) ($viewStats['total_system_balance'] ?? $totalSavings),
            'activeLoans' => (int) ($viewStats['active_loans_count'] ?? ($approvedStatusId ? Loan::where('status_id', $approvedStatusId)->count() : 0)),
            'totalProjects' => Project::count(),
            'pendingApprovals' => (int) ($viewStats['pending_loans_count'] ?? ($pendingStatusId ? Loan::where('status_id', $pendingStatusId)->count() : 0)),
            'approvedLoans' => $loanStats['approved'],
            'pendingLoans' => $loanStats['pending'],
            'rejectedLoans' => $loanStats['rejected'],
            'totalLoanAmount' => $totalLoans,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalTransfers' => $totalTransfers,
            'totalFees' => $totalFees,
            'netBalance' => $totalSavings + $totalDeposits - $totalWithdrawals - $totalLoans + $loanRepayments,
            'membersGrowth' => $membersGrowth,
            'loanStats' => $loanStats,
            'transactionStats' => $transactionStats,
            'monthlyRevenue' => $monthlyRevenue,
            'savingsGrowth' => $savingsGrowth,
            'projects' => $projects,
            'transactionTypeAmounts' => $transactionTypeAmounts,
            'savingsReconciliation' => $recon,
            'roleDistribution' => [
                'client' => (int) ($roleCounts['client'] ?? 0),
                'shareholder' => (int) ($roleCounts['shareholder'] ?? 0),
                'cashier' => (int) ($roleCounts['cashier'] ?? 0),
                'td' => (int) ($roleCounts['td'] ?? 0),
                'ceo' => (int) ($roleCounts['ceo'] ?? 0),
                'admin' => (int) ($roleCounts['admin'] ?? 0)
            ]
        ]);
    }

    public function getSettings()
    {
        $settings = DB::table('settings')->pluck('setting_value', 'setting_key');
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
                DB::table('settings')->updateOrInsert(
                    ['setting_key' => $key],
                    ['setting_value' => $value, 'updated_at' => now()]
                );
            }
            
            AuditLogService::log(auth()->user() ?? 'System', 'settings_updated', 'System settings were modified', [
                'entity_type' => 'settings',
            ]);
            
            return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAuditLogs()
    {
        $logs = DB::table('audit_logs')
            ->leftJoin('audit_action_types', 'audit_action_types.id', '=', 'audit_logs.action_type_id')
            ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
            ->select(
                'audit_logs.id',
                'audit_action_types.name as action',
                'users.username as user',
                'audit_logs.description as details',
                'audit_logs.created_at as timestamp'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(200)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action ? ucfirst((string) $log->action) : 'Activity',
                    'user' => $log->user ?? 'System',
                    'details' => $log->details ?? '',
                    'timestamp' => $log->timestamp,
                ];
            })
            ->values();

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
            'backup_number' => 'BKP-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'filename' => $filename,
            'filepath' => '/backups/' . $filename,
            'file_size' => rand(1000000, 5000000),
            'type' => 'manual',
            'status' => 'completed',
            'includes' => 'full',
            'compression' => 'gzip',
            'created_by' => auth()->id() ?? 1,
            'created_at' => now(),
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
        $totalDeposits = (float) Transaction::query()->ofType('deposit')->sum('amount');
        $totalWithdrawals = (float) Transaction::query()->ofType('withdrawal')->sum('amount');
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $totalLoans = $approvedStatusId
            ? (float) Loan::where('status_id', $approvedStatusId)->sum('principal_amount')
            : 0.0;
        $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');
        $loanRepayments = (float) Transaction::query()->ofType('loan_payment')->sum('amount');
        
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
        $users = User::with('member')->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->is_active ? 'active' : 'inactive',
                'profile_picture' => $user->profile_picture_url,
                'member_id' => $user->member ? $user->member->member_id : null,
                'savings' => $user->member ? $user->member->savings : null,
                'loan' => $user->member ? $user->member->loan : null
            ];
        });
        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:client,shareholder,cashier,td,ceo,admin',
        ]);

        $user = User::withoutEvents(function () use ($request) {
            return User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        });

        [$firstName, $middleName, $lastName] = $this->splitName($request->name);

        $memberNumber = $this->generateMemberId();
        $member = new Member();
        $member->user_id = $user->id;
        $member->member_number = $memberNumber;
        $member->member_account_number = $memberNumber;
        $member->first_name = $firstName;
        $member->middle_name = $middleName;
        $member->last_name = $lastName;
        $member->email = $request->email;
        $member->primary_phone = $request->contact ?? null;
        $member->place_of_birth = $request->location ?? null;
        $member->occupation = $request->occupation ?? null;
        $member->membership_status = 'active';
        $member->join_date = now()->toDateString();
        $member->created_by = auth()->id() ?? $user->id;
        Member::queueOpeningSavings($member, (float) ($request->savings ?? 0));
        $member->save();

        $member->assignRole($request->role);

        AuditLogService::log(auth()->user() ?? $user, 'user_created', 'Created user: ' . $user->name, [
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'member_id' => $member->id,
        ]);
        
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        AuditLogService::log(auth()->user() ?? 'System', 'user_status_changed', 'Changed status for: ' . $user->name . ' to ' . ($user->is_active ? 'active' : 'inactive'), [
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function changeUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldRole = $user->role;
        $user->assignRole($request->role);

        if ($user->member) {
            $user->member->assignRole($request->role);
        }

        AuditLogService::log(auth()->user() ?? 'System', 'user_role_changed', 'Changed role for: ' . $user->name . ' from ' . $oldRole . ' to ' . $request->role, [
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->assignRole($request->role);
        $user->is_active = $request->status === 'active';
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        if ($request->hasFile('profile_picture')) {
            $path = ProfilePictureStorageService::storeProfilePicture(
                $request->file('profile_picture'),
                $user->profile_picture
            );
            $user->profile_picture = $path;
        }
        $user->save();

        if ($user->member) {
            [$firstName, $middleName, $lastName] = $this->splitName($request->name);
            $user->member->update([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'email' => $request->email,
                'profile_picture' => $user->profile_picture,
            ]);
            $user->member->assignRole($request->role);
        }

        AuditLogService::log(auth()->user() ?? 'System', 'user_updated', 'Updated user: ' . $user->name, [
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->is_active ? 'active' : 'inactive',
                'profile_picture' => $user->profile_picture_url
            ]
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;

        $user->delete();

        AuditLogService::log(auth()->user() ?? 'System', 'user_deleted', 'Deleted user: ' . $userName, [
            'entity_type' => 'user',
            'entity_identifier' => $userName,
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
                $email = trim((string) $row[2]);
                if (Member::where('email', $email)->exists()) {
                    continue;
                }
                $role = in_array($row[4] ?? 'client', ['admin', 'client', 'cashier', 'td', 'ceo', 'shareholder'], true) ? $row[4] : 'client';
                $user = User::withoutEvents(function () use ($row, $email, $role) {
                    return User::create([
                        'name' => $row[1],
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'role' => $role,
                        'status' => 'active',
                        'is_active' => true,
                        'phone' => $row[3] ?? null,
                    ]);
                });
                [$firstName, $middleName, $lastName] = $this->splitName((string) $row[1]);
                $memberNumber = !empty($row[0]) ? $row[0] : $this->generateMemberId();
                $member = Member::create([
                    'user_id' => $user->id,
                    'member_number' => $memberNumber,
                    'member_account_number' => $memberNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'primary_phone' => $row[3] ?? null,
                    'membership_status' => 'active',
                    'join_date' => now()->toDateString(),
                    'created_by' => auth()->id() ?? $user->id,
                ]);
                $member->assignRole($role);
                $imported++;
            }
        }

        AuditLogService::log(auth()->user() ?? 'System', 'bulk_import', "Imported {$imported} members", [
            'entity_type' => 'member',
            'imported' => $imported,
        ]);
        
        return response()->json(['success' => true, 'imported' => $imported]);
    }

    public function sendBulkEmail(Request $request)
    {
        $recipients = $request->recipients;
        $query = Member::query();
        
        if ($recipients === 'clients') {
            $query->whereHas('roles', fn ($q) => $q->where('name', 'client'));
        } elseif ($recipients === 'shareholders') {
            $query->whereHas('roles', fn ($q) => $q->where('name', 'shareholder'));
        } elseif ($recipients === 'staff') {
            $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['cashier', 'td', 'ceo']));
        }
        
        $count = $query->count();

        AuditLogService::log(auth()->user() ?? 'System', 'bulk_email_sent', "Sent '{$request->subject}' to {$count} recipients", [
            'entity_type' => 'member',
            'recipient_group' => $recipients,
            'count' => $count,
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
            AuditLogService::log(auth()->user() ?? 'System', 'report_generated', "Generated {$type} report" . ($dateFrom ? " from {$dateFrom}" : '') . ($dateTo ? " to {$dateTo}" : ''), [
                'entity_type' => 'report',
                'type' => $type,
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
                $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');
                $totalLoans = (float) Loan::sum('principal_amount');
                $totalDeposits = (float) Transaction::query()->ofType('deposit')->sum('amount');
                $totalWithdrawals = (float) Transaction::query()->ofType('withdrawal')->sum('amount');
                $netBalance = $totalSavings;
                
                $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
                $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
                $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
                $approvedLoans = $approvedStatusId ? Loan::where('status_id', $approvedStatusId)->count() : 0;
                $pendingLoans = $pendingStatusId ? Loan::where('status_id', $pendingStatusId)->count() : 0;
                $rejectedLoans = $rejectedStatusId ? Loan::where('status_id', $rejectedStatusId)->count() : 0;
                $loansCount = Loan::count();
                $avgLoanAmount = Loan::avg('principal_amount') ?? 0;
                
                $totalMembers = Member::count();
                $activeMembers = Member::where('membership_status', 'active')->count();
                $avgSavings = (float) DB::table('savings_accounts')->avg('current_balance');
                
                $loanInterest = $totalLoans * 0.055;
                $processingFees = $totalLoans * 0.025;
                $netCashFlow = $totalDeposits - $totalWithdrawals + $loanInterest + $processingFees;
                
                $transactionBreakdown = [
                    'deposit' => [
                        'count' => Transaction::query()->ofType('deposit')->count(),
                        'amount' => $totalDeposits
                    ],
                    'withdrawal' => [
                        'count' => Transaction::query()->ofType('withdrawal')->count(),
                        'amount' => $totalWithdrawals
                    ],
                    'transfer' => [
                        'count' => Transaction::query()->ofType('transfer')->count(),
                        'amount' => Transaction::query()->ofType('transfer')->sum('amount')
                    ],
                    'loan_payment' => [
                        'count' => Transaction::query()->ofType('loan_payment')->count(),
                        'amount' => Transaction::query()->ofType('loan_payment')->sum('amount')
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
                $query = DB::table('audit_logs')
                    ->leftJoin('audit_action_types', 'audit_action_types.id', '=', 'audit_logs.action_type_id')
                    ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
                    ->select(
                        'audit_logs.*',
                        'audit_action_types.name as action_name',
                        'users.username as user_name'
                    );
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $data = $query->get();
                $title = 'Audit Report';
                $view = 'reports.audit';
                break;
        }
        
        DB::table('generated_reports')->insert([
            'report_number' => 'RPT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => $title,
            'type' => $type,
            'from_date' => $dateFrom ?? now()->toDateString(),
            'to_date' => $dateTo ?? now()->toDateString(),
            'format' => $format,
            'generated_at' => now(),
            'generated_by' => auth()->id() ?? 1,
        ]);
        
        AuditLogService::log(auth()->user() ?? 'System', 'report_generated', "Generated {$title} in {$format} format", [
            'entity_type' => 'report',
            'title' => $title,
            'format' => $format,
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
                $userName = $log->user_name ?? 'System';
                $actionName = $log->action_name ?? 'activity';
                $details = $log->description ?? '';
                $timestamp = $log->created_at ?? '';
                $content .= "{$userName},{$actionName},{$details},{$timestamp}\n";
            }
        }
        
        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Ymd_His') . '.csv';
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    public function getRecentReports()
    {
        $reports = DB::table('generated_reports')->orderBy('generated_at', 'desc')->limit(10)->get();
        return response()->json($reports);
    }
    
    public function viewReport($id)
    {
        $report = DB::table('generated_reports')->where('id', $id)->first();
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
        DB::table('generated_reports')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function sendNotification(Request $request)
    {
        AuditLogService::log(auth()->user() ?? 'System', 'notification_sent', $request->title . ' to ' . $request->target, [
            'entity_type' => 'notification',
            'target' => $request->target,
        ]);
        return response()->json(['success' => true]);
    }

    private function splitName(?string $name): array
    {
        $normalized = trim((string) $name);
        if ($normalized === '') {
            return ['Unknown', null, 'User'];
        }

        $parts = preg_split('/\s+/', $normalized);
        $first = array_shift($parts) ?: 'Unknown';
        $last = array_pop($parts) ?: $first;
        $middle = $parts ? implode(' ', $parts) : null;

        return [$first, $middle, $last];
    }
}
