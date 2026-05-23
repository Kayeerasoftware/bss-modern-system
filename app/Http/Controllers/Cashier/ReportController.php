<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Project;
use App\Models\System\AuditLog;
use App\Models\Reports\GeneratedReport;
use App\Services\Financial\SavingsReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $summary = Cache::remember('admin_reports:summary:v2', now()->addSeconds(60), static function () {
            $txSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions')
                ->first();

            $totalIncome = (float) Transaction::query()
                ->whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))
                ->sum('amount');
            $totalExpenses = (float) Transaction::query()
                ->whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))
                ->sum('amount');

            $netBalance = (float) DB::table('savings_accounts')->sum('current_balance');
            $recon = app(SavingsReconciliationService::class)->getSystemSummary(1000);

            return [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_balance' => $netBalance,
                'total_transactions' => (int) ($txSummary->total_transactions ?? 0),
                'reconciled_savings' => (float) ($recon['totals']['reconciled'] ?? 0),
            ];
        });
        
        $query = GeneratedReport::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('generated_at', '<=', $request->date_to);
        }

        $reports = $query->latest('generated_at')->paginate(15)->appends($request->query());
        
        return view('cashier.reports.index', compact('summary', 'reports'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:members,financial,loans,transactions,projects,audit,deposits,withdrawals',
            'from_date' => 'required|date|before_or_equal:to_date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'format' => 'required|in:html,csv',
        ], [
            'from_date.before_or_equal' => 'From date must be before or equal to the To date.',
            'to_date.after_or_equal' => 'To date must be after or equal to the From date.',
        ]);

        try {
            $data = $this->getReportData($validated['type'], $validated['from_date'], $validated['to_date']);
            
            // Save report to database
            $report = GeneratedReport::create([
                'report_number' => 'RPT-' . strtoupper(uniqid()),
                'name' => ucfirst($validated['type']) . ' Report',
                'type' => $validated['type'],
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'format' => $validated['format'],
                'row_count' => is_countable($data) ? count($data) : 0,
                'generated_at' => now(),
                'generated_by' => auth()->id(),
            ]);
            
            return view('cashier.reports.view', [
                'type' => $validated['type'],
                'data' => $data,
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'format' => $validated['format'],
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            \Log::error('Report generation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to generate report. Please try again.'])
                ->withInput();
        }
    }

    public function save(Request $request)
    {
        GeneratedReport::create([
            'report_number' => 'RPT-' . strtoupper(uniqid()),
            'name' => $request->name,
            'type' => $request->type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'format' => $request->format,
            'generated_by' => auth()->id(),
        ]);
        
        return redirect()->route('cashier.reports.index')->with('success', 'Report saved successfully');
    }

    public function getReportData($type, $from, $to)
    {
        try {
            return match($type) {
                'members' => Member::whereBetween('created_at', [$from, $to])->get(),
                'financial' => Transaction::with(['member', 'transactionType'])
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                'loans' => Loan::with('member')
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                'transactions' => Transaction::with(['member', 'transactionType'])
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                'deposits' => Transaction::with(['member', 'transactionType'])
                    ->whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                'withdrawals' => Transaction::with(['member', 'transactionType'])
                    ->whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                'projects' => Project::whereBetween('created_at', [$from, $to])->get(),
                'audit' => AuditLog::with('user')
                    ->whereBetween('created_at', [$from, $to])
                    ->get(),
                default => collect([]),
            };
        } catch (\Exception $e) {
            \Log::error('Report generation error: ' . $e->getMessage());
            return collect([]);
        }
    }
}
