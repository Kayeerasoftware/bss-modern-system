<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoanApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanApplication::with(['member', 'statusRelation']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('application_number', 'like', "%{$request->search}%")
                    ->orWhere('purpose', 'like', "%{$request->search}%")
                    ->orWhereHas('member', function($q) use ($request) {
                        $q->where('full_name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->status) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
        }

        if ($request->amount_min) {
            $query->where('requested_amount', '>=', $request->amount_min);
        }

        if ($request->amount_max) {
            $query->where('requested_amount', '<=', $request->amount_max);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->sort) {
            switch ($request->sort) {
                case 'amount_high':
                    $query->orderBy('requested_amount', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('requested_amount', 'asc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $applications = $query->paginate(15);
        return view('cashier.loan-applications.index', compact('applications'));
    }

    public function create()
    {
        return redirect()->route('cashier.loans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'applicant_comment' => 'nullable|string',
            'repayment_months' => 'required|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $loanTypeId = LoanType::query()->where('is_active', 1)->value('id') ?? LoanType::query()->value('id');
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');

        LoanApplication::create([
            'member_id' => $memberId,
            'loan_type_id' => $loanTypeId,
            'requested_amount' => $validated['amount'],
            'requested_tenure_months' => $validated['repayment_months'],
            'purpose' => $validated['purpose'],
            'applicant_comment' => $validated['applicant_comment'] ?? null,
            'status_id' => $pendingStatusId,
            'submission_date' => now(),
        ]);

        return redirect()->route('cashier.loan-applications.index')->with('success', 'Loan application created successfully');
    }

    public function show($id)
    {
        $application = LoanApplication::with(['member', 'statusRelation'])->findOrFail($id);
        return view('cashier.loan-applications.show', compact('application'));
    }

    public function edit($id)
    {
        $application = LoanApplication::findOrFail($id);
        $members = Cache::remember('loan_application_form:members:v1', now()->addMinutes(2), static function () {
            return Member::query()
                ->select('id', 'member_number', 'full_name', 'email', 'primary_phone', 'membership_status')
                ->orderBy('full_name')
                ->get()
                ->each
                ->append(['member_id', 'contact', 'status']);
        });
        return view('cashier.loan-applications.edit', compact('application', 'members'));
    }

    public function update(Request $request, $id)
    {
        $application = LoanApplication::findOrFail($id);

        $validated = $request->validate([
            'member_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'applicant_comment' => 'nullable|string',
            'repayment_months' => 'required|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $memberId = resolve_member_id($validated['member_id']);
        if (!$memberId) {
            return back()->withErrors(['member_id' => 'Invalid member selected.'])->withInput();
        }

        $statusId = LoanStatus::query()->where('name', $validated['status'])->value('id');

        $application->update([
            'member_id' => $memberId,
            'requested_amount' => $validated['amount'],
            'requested_tenure_months' => $validated['repayment_months'],
            'purpose' => $validated['purpose'],
            'applicant_comment' => $validated['applicant_comment'] ?? null,
            'status_id' => $statusId,
        ]);

        return redirect()->route('cashier.loan-applications.index')->with('success', 'Loan application updated successfully');
    }

    public function destroy($id)
    {
        $application = LoanApplication::findOrFail($id);
        $application->delete();

        return redirect()->route('cashier.loan-applications.index')->with('success', 'Loan application deleted successfully');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_comment' => 'nullable|string'
        ]);

        $application = LoanApplication::findOrFail($id);
        $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
        $application->update([
            'status_id' => LoanStatus::query()->where('name', 'approved')->value('id'),
            'decision_notes' => $request->approval_comment,
            'decision_by' => auth()->id(),
            'decision_date' => now()
        ]);

        Loan::query()
            ->where('application_id', $application->id)
            ->update([
                'status_id' => $approvedStatusId ?? $application->status_id,
                'approval_date' => now()->toDateString(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approved_ip' => request()->ip(),
            ]);

        return redirect()->back()->with('success', 'Loan application approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $application = LoanApplication::findOrFail($id);
        $rejectedStatusId = LoanStatus::query()->where('name', 'rejected')->value('id');
        $application->update([
            'status_id' => $rejectedStatusId,
            'rejection_reason' => $request->rejection_reason,
            'decision_by' => auth()->id(),
            'decision_date' => now()
        ]);

        Loan::query()
            ->where('application_id', $application->id)
            ->update([
                'status_id' => $rejectedStatusId ?? $application->status_id,
            ]);

        return redirect()->back()->with('success', 'Loan application rejected');
    }
}
