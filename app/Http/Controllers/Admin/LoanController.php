<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanSetting;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with('member');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('loan_id', 'like', "%{$request->search}%")
                  ->orWhereHas('member', function($q) use ($request) {
                      $q->where('full_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
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
                    $query->orderBy('amount', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('amount', 'asc');
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

        $loans = $query->paginate(15);
        
        if ($request->ajax()) {
            return view('admin.loans.partials.table', compact('loans'))->render();
        }
        
        return view('admin.loans.index', compact('loans'));
    }

    public function create()
    {
        $settings = LoanSetting::first();
        
        if (!$settings) {
            $settings = LoanSetting::create([
                'is_loan_available' => true,
                'default_interest_rate' => 10.00,
                'min_interest_rate' => 5.00,
                'max_interest_rate' => 30.00,
                'min_loan_amount' => 10000.00,
                'max_loan_amount' => 10000000.00,
                'max_loan_to_savings_ratio' => 300,
                'min_repayment_months' => 3,
                'max_repayment_months' => 60,
                'default_repayment_months' => 12,
                'processing_fee_percentage' => 2.00,
                'late_payment_penalty' => 5.00,
                'grace_period_days' => 7,
                'auto_approve_amount' => 0.00,
                'require_guarantors' => false,
                'guarantors_required' => 2,
                'email_notifications' => true,
                'sms_notifications' => true,
                'payment_reminder_days' => 3,
            ]);
        }
        
        if (!$settings->is_loan_available) {
            return redirect()->route('admin.loans.index')->with('error', 'Loan applications are currently disabled.');
        }
        
        $members = Member::all();
        return view('admin.loans.create', compact('members', 'settings'));
    }

    public function store(Request $request)
    {
        $settings = LoanSetting::first();
        
        if (!$settings) {
            $settings = LoanSetting::create([
                'is_loan_available' => true,
                'default_interest_rate' => 10.00,
                'min_interest_rate' => 5.00,
                'max_interest_rate' => 30.00,
                'min_loan_amount' => 10000.00,
                'max_loan_amount' => 10000000.00,
                'max_loan_to_savings_ratio' => 300,
                'min_repayment_months' => 3,
                'max_repayment_months' => 60,
                'default_repayment_months' => 12,
                'processing_fee_percentage' => 2.00,
                'late_payment_penalty' => 5.00,
                'grace_period_days' => 7,
                'auto_approve_amount' => 0.00,
                'require_guarantors' => false,
                'guarantors_required' => 2,
                'email_notifications' => true,
                'sms_notifications' => true,
                'payment_reminder_days' => 3,
            ]);
        }
        
        if (!$settings->is_loan_available) {
            return redirect()->route('admin.loans.index')->with('error', 'Loan applications are currently disabled.');
        }
        
        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:' . ($settings->min_loan_amount ?? 0) . '|max:' . ($settings->max_loan_amount ?? 999999999),
            'purpose' => 'required|string',
            'repayment_months' => 'required|integer|min:' . ($settings->min_repayment_months ?? 1) . '|max:' . ($settings->max_repayment_months ?? 120),
            'interest_rate' => 'nullable|numeric|min:' . ($settings->min_interest_rate ?? 0) . '|max:' . ($settings->max_interest_rate ?? 100),
            'applicant_comment' => 'nullable|string',
            'guarantor_1_name' => $settings->require_guarantors ? 'required|string' : 'nullable|string',
            'guarantor_1_phone' => $settings->require_guarantors ? 'required|string' : 'nullable|string',
            'guarantor_2_name' => ($settings->require_guarantors && $settings->guarantors_required >= 2) ? 'required|string' : 'nullable|string',
            'guarantor_2_phone' => ($settings->require_guarantors && $settings->guarantors_required >= 2) ? 'required|string' : 'nullable|string',
        ]);

        $interestRate = $validated['interest_rate'] ?? $settings->default_interest_rate;
        $interest = $validated['amount'] * ($interestRate / 100) * ($validated['repayment_months'] / 12);
        $processingFee = $validated['amount'] * ($settings->processing_fee_percentage / 100);
        $monthly_payment = ($validated['amount'] + $interest) / $validated['repayment_months'];

        $validated['interest'] = $interest;
        $validated['interest_rate'] = $interestRate;
        $validated['processing_fee'] = $processingFee;
        $validated['monthly_payment'] = $monthly_payment;
        
        // Auto-approve if below threshold
        if ($settings->auto_approve_amount > 0 && $validated['amount'] <= $settings->auto_approve_amount) {
            $validated['status'] = 'approved';
        }
        
        $lastLoan = Loan::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastLoan ? $lastLoan->id + 1 : 1;
        $validated['loan_id'] = 'LOAN' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Loan::create($validated);

        return redirect()->route('admin.loans.index')->with('success', 'Loan created successfully');
    }

    public function show($id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        return view('admin.loans.show', compact('loan'));
    }

    public function printPdf($id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        $pdf = \PDF::loadView('admin.loans.pdf', compact('loan'));
        return $pdf->download('loan-application-' . $loan->loan_id . '.pdf');
    }

    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $members = Member::all();
        $settings = LoanSetting::first();
        return view('admin.loans.edit', compact('loan', 'members', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $settings = LoanSetting::first();

        $validated = $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'repayment_months' => 'required|integer|min:1',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $interestRate = $loan->interest_rate ?? $settings->default_interest_rate ?? 10;
        $interest = $validated['amount'] * ($interestRate / 100) * ($validated['repayment_months'] / 12);
        $monthly_payment = ($validated['amount'] + $interest) / $validated['repayment_months'];

        $validated['interest'] = $interest;
        $validated['monthly_payment'] = $monthly_payment;

        $loan->update($validated);

        return redirect()->route('admin.loans.index')->with('success', 'Loan updated successfully');
    }

    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return redirect()->route('admin.loans.index')->with('success', 'Loan deleted successfully');
    }

    public function applications()
    {
        $loans = Loan::where('status', 'pending')->with('member')->latest()->paginate(15);
        return view('admin.loans.applications', compact('loans'));
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Loan approved successfully');
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Loan rejected successfully');
    }

    public function approvals()
    {
        $loans = Loan::where('status', 'approved')->with('member')->latest()->paginate(15);
        return view('admin.loans.approvals', compact('loans'));
    }

    public function repayments()
    {
        $loans = Loan::whereIn('status', ['approved'])->with('member')->latest()->paginate(15);
        return view('admin.loans.repayments', compact('loans'));
    }
}
