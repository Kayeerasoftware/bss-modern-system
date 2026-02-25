<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'member_id',
        'amount',
        'repayment_months',
        'purpose',
        'loan_type',
        'employment_status',
        'monthly_income',
        'employer_name',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'approval_comment',
        'applicant_comment'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'monthly_income' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->application_id)) {
                $model->application_id = 'LA' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}