<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanSetting;
use Illuminate\Http\Request;

class LoanSettingsController extends Controller
{
    public function index()
    {
        $settings = LoanSetting::first() ?? new LoanSetting();
        return view('admin.loan-settings', ['settings' => $settings->toArray()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'is_loan_available' => 'nullable|boolean',
            'default_interest_rate' => 'required|numeric|min:0|max:100',
            'min_interest_rate' => 'required|numeric|min:0|max:100',
            'max_interest_rate' => 'required|numeric|min:0|max:100',
            'min_loan_amount' => 'required|numeric|min:0',
            'max_loan_amount' => 'required|numeric|min:0',
            'max_loan_to_savings_ratio' => 'required|numeric|min:0',
            'min_repayment_months' => 'required|integer|min:1|max:120',
            'max_repayment_months' => 'required|integer|min:1|max:120',
            'default_repayment_months' => 'required|integer|min:1|max:120',
            'processing_fee_percentage' => 'required|numeric|min:0|max:100',
            'late_payment_penalty' => 'required|numeric|min:0|max:100',
            'grace_period_days' => 'required|integer|min:0|max:90',
            'auto_approve_amount' => 'nullable|numeric|min:0',
            'require_guarantors' => 'required|boolean',
            'guarantors_required' => 'required|integer|min:0|max:10',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'payment_reminder_days' => 'required|integer|min:0|max:30',
        ]);

        $validated['is_loan_available'] = $request->has('is_loan_available');
        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['sms_notifications'] = $request->has('sms_notifications');

        $settings = LoanSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            LoanSetting::create($validated);
        }

        return redirect()->route('admin.loan-settings')->with('success', 'Loan settings updated successfully!');
    }
}
