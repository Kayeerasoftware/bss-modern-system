<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSetting extends Model
{
    protected $fillable = [
        'is_loan_available',
        'default_interest_rate',
        'min_interest_rate',
        'max_interest_rate',
        'min_loan_amount',
        'max_loan_amount',
        'max_loan_to_savings_ratio',
        'min_repayment_months',
        'max_repayment_months',
        'default_repayment_months',
        'processing_fee_percentage',
        'late_payment_penalty',
        'grace_period_days',
        'auto_approve_amount',
        'require_guarantors',
        'guarantors_required',
        'email_notifications',
        'sms_notifications',
        'payment_reminder_days',
    ];

    protected $casts = [
        'is_loan_available' => 'boolean',
        'require_guarantors' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
    ];
}
