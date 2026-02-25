<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id', 'member_id', 'amount', 'purpose', 'applicant_comment', 'repayment_months',
        'interest_rate', 'interest', 'processing_fee', 'monthly_payment', 'status',
        'guarantor_1_name', 'guarantor_1_phone', 'guarantor_2_name', 'guarantor_2_phone',
        'settings_min_interest_rate', 'settings_max_interest_rate', 'settings_min_loan_amount',
        'settings_max_loan_amount', 'settings_max_loan_to_savings_ratio', 'settings_min_repayment_months',
        'settings_max_repayment_months', 'settings_default_repayment_months', 'settings_processing_fee_percentage',
        'settings_late_payment_penalty', 'settings_grace_period_days', 'settings_auto_approve_amount',
        'settings_require_guarantors', 'settings_guarantors_required', 'settings_email_notifications',
        'settings_sms_notifications', 'settings_payment_reminder_days'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}