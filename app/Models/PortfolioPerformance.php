<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioPerformance extends Model
{
    protected $fillable = [
        'member_id', 'period', 'portfolio_value', 'market_value', 
        'performance_percentage', 'benchmark_comparison'
    ];

    protected $casts = [
        'period' => 'date',
        'portfolio_value' => 'decimal:2',
        'market_value' => 'decimal:2',
        'performance_percentage' => 'decimal:2',
        'benchmark_comparison' => 'decimal:2'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
