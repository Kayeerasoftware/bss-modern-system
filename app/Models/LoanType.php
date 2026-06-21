<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $table = 'loan_types';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'max_amount',
        'default_interest_rate',
        'min_repayment_months',
        'max_repayment_months',
        'requires_guarantors',
        'guarantors_required',
        'is_active',
    ];
}
