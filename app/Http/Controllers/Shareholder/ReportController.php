<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Financial\Transaction;
use App\Models\Loans\Loan;
use App\Models\Reports\GeneratedReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        $summary = [
            'total_income' => $member ? Transaction::where('member_id', $member->member_id)->where('type', 'deposit')->sum('amount') : 0,
            'total_expenses' => $member ? Transaction::where('member_id', $member->member_id)->where('type', 'withdrawal')->sum('amount') : 0,
            'net_balance' => $member ? (Transaction::where('member_id', $member->member_id)->where('type', 'deposit')->sum('amount') - Transaction::where('member_id', $member->member_id)->where('type', 'withdrawal')->sum('amount')) : 0,
            'total_transactions' => $member ? Transaction::where('member_id', $member->member_id)->count() : 0,
        ];
        
        $reports = GeneratedReport::latest()->take(10)->get();
        
        return view('shareholder.reports.index', compact('summary', 'reports'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:portfolio,dividends,performance,tax,transactions,savings',
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
        ]);
        
        return redirect()->route('shareholder.reports.index')->with('success', 'Report generated successfully');
    }

    private function getReportData($type, $from, $to)
    {
        $user = auth()->user();
        $member = Member::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        
        if (!$member) return [];
        
        return match($type) {
            'portfolio' => Transaction::where('member_id', $member->member_id)->whereBetween('created_at', [$from, $to])->get(),
            'dividends' => Transaction::where('member_id', $member->member_id)->where('category', 'dividend')->whereBetween('created_at', [$from, $to])->get(),
            'performance' => Transaction::where('member_id', $member->member_id)->whereBetween('created_at', [$from, $to])->get(),
            'tax' => Transaction::where('member_id', $member->member_id)->whereBetween('created_at', [$from, $to])->get(),
            'transactions' => Transaction::where('member_id', $member->member_id)->whereBetween('created_at', [$from, $to])->get(),
            'savings' => Transaction::where('member_id', $member->member_id)->whereBetween('created_at', [$from, $to])->get(),
            default => [],
        };
    }

    public function view($id)
    {
        $report = GeneratedReport::findOrFail($id);
        $data = $this->getReportData($report->type, $report->from_date, $report->to_date);
        
        return view('shareholder.reports.view', [
            'type' => $report->type,
            'data' => $data,
            'from_date' => $report->from_date,
            'to_date' => $report->to_date,
            'format' => $report->format,
        ]);
    }

    public function portfolio()
    {
        return view('shareholder.reports.portfolio');
    }

    public function dividends()
    {
        return view('shareholder.reports.dividends');
    }

    public function performance()
    {
        return view('shareholder.reports.performance');
    }

    public function tax()
    {
        return view('shareholder.reports.tax');
    }
}
