<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use App\Models\User;

class LoanApplication extends Model
{
    protected $fillable = [
        'application_id',
        'member_id',
        'amount',
        'purpose',
        'applicant_comment',
        'repayment_months',
        'interest_rate',
        'status',
        'rejection_reason',
        'approval_comment',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getInterestAttribute()
    {
        return $this->amount * ($this->interest_rate / 100) * ($this->repayment_months / 12);
    }

    public function getTotalRepaymentAttribute()
    {
        return $this->amount + $this->interest;
    }

    public function getMonthlyPaymentAttribute()
    {
        return $this->total_repayment / $this->repayment_months;
    }
}
