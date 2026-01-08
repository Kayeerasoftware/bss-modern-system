<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentOpportunity extends Model
{
    protected $fillable = [
        'title', 'description', 'target_amount', 'minimum_investment',
        'expected_roi', 'risk_level', 'launch_date', 'deadline', 'status'
    ];

    protected $casts = [
        'launch_date' => 'date',
        'deadline' => 'date',
        'target_amount' => 'decimal:2',
        'minimum_investment' => 'decimal:2',
        'expected_roi' => 'decimal:2'
    ];
}
