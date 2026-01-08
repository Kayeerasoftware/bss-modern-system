<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'member_id', 'amount', 'purpose', 'repayment_months',
        'interest', 'monthly_payment', 'status'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loan) {
            $loan->loan_id = 'LOAN' . rand(100, 999);
        });
    }
}