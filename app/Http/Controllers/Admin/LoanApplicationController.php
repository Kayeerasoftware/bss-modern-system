<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loans\LoanApplication;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoanApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanApplication::with(['member', 'reviewer']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('application_id', 'like', "%{$request->search}%")
                  ->orWhereHas('member', function($q) use ($request) {
                      $q->where('full_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(15);
        return view('admin.loan-applications.index', compact('applications'));
    }

    public function create()
    {
        return redirect()->route('admin.loans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'applicant_comment' => 'nullable|string',
            'repayment_months' => 'required|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['application_id'] = 'APP' . str_pad(LoanApplication::count() + 1, 6, '0', STR_PAD_LEFT);
        $validated['interest_rate'] = $validated['interest_rate'] ?? 10.00;

        LoanApplication::create($validated);

        return redirect()->route('admin.loan-applications.index')->with('success', 'Loan application created successfully');
    }

    public function show($id)
    {
        $application = LoanApplication::with(['member', 'reviewer'])->findOrFail($id);
        return view('admin.loan-applications.show', compact('application'));
    }

    public function edit($id)
    {
        $application = LoanApplication::findOrFail($id);
        $members = Cache::remember('loan_application_form:members:v1', now()->addMinutes(2), static function () {
            return Member::query()
                ->select('id', 'member_id', 'full_name', 'email', 'contact', 'status')
                ->orderBy('full_name')
                ->get();
        });
        return view('admin.loan-applications.edit', compact('application', 'members'));
    }

    public function update(Request $request, $id)
    {
        $application = LoanApplication::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'applicant_comment' => 'nullable|string',
            'repayment_months' => 'required|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $application->update($validated);

        return redirect()->route('admin.loan-applications.index')->with('success', 'Loan application updated successfully');
    }

    public function destroy($id)
    {
        $application = LoanApplication::findOrFail($id);
        $application->delete();

        return redirect()->route('admin.loan-applications.index')->with('success', 'Loan application deleted successfully');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_comment' => 'nullable|string'
        ]);

        $application = LoanApplication::findOrFail($id);
        $application->update([
            'status' => 'approved',
            'approval_comment' => $request->approval_comment,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Loan application approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $application = LoanApplication::findOrFail($id);
        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Loan application rejected');
    }
}
