<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Financial\Transaction;
use App\Models\Loans\Loan;
use App\Models\Projects\Project;
use App\Models\System\AuditLog;
use App\Models\Reports\GeneratedReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $summary = [
            'total_income' => Transaction::where('type', 'deposit')->sum('amount'),
            'total_expenses' => Transaction::where('type', 'withdrawal')->sum('amount'),
            'net_balance' => Member::sum('balance'),
            'total_transactions' => Transaction::count(),
        ];
        
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
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->latest()->paginate(15)->appends($request->query());
        
        return view('admin.reports.index', compact('summary', 'reports'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:members,financial,loans,transactions,projects,audit,deposits,withdrawals',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'format' => 'required|in:html,csv',
        ]);

        $data = $this->getReportData($validated['type'], $validated['from_date'], $validated['to_date']);
        
        // Save report to database
        GeneratedReport::create([
            'name' => ucfirst($validated['type']) . ' Report',
            'type' => $validated['type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'format' => $validated['format'],
            'user_id' => auth()->id(),
        ]);
        
        return view('admin.reports.view', [
            'type' => $validated['type'],
            'data' => $data,
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'format' => $validated['format'],
        ]);
    }

    public function save(Request $request)
    {
        GeneratedReport::create([
            'name' => $request->name,
            'type' => $request->type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'format' => $request->format,
        ]);
        
        return redirect()->route('admin.reports.index')->with('success', 'Report saved successfully');
    }

    public function getReportData($type, $from, $to)
    {
        return match($type) {
            'members' => Member::whereBetween('created_at', [$from, $to])->get(),
            'financial' => Transaction::with('member')->whereBetween('created_at', [$from, $to])->get(),
            'loans' => Loan::with('member')->whereBetween('created_at', [$from, $to])->get(),
            'transactions' => Transaction::with('member')->whereBetween('created_at', [$from, $to])->get(),
            'deposits' => Transaction::with('member')->where('type', 'deposit')->whereBetween('created_at', [$from, $to])->get(),
            'withdrawals' => Transaction::with('member')->where('type', 'withdrawal')->whereBetween('created_at', [$from, $to])->get(),
            'projects' => Project::whereBetween('created_at', [$from, $to])->get(),
            'audit' => AuditLog::whereBetween('created_at', [$from, $to])->get(),
            default => [],
        };
    }
}
