<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fundraising extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'target_amount',
        'raised_amount',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function contributions()
    {
        return $this->hasMany(FundraisingContribution::class);
    }

    public function expenses()
    {
        return $this->hasMany(FundraisingExpense::class);
    }

    public function getTotalContributionsAttribute()
    {
        return $this->contributions()->sum('amount');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getNetAmountAttribute()
    {
        return $this->raised_amount - $this->total_expenses;
    }

    public function getProgressPercentageAttribute()
    {
        return $this->target_amount > 0 ? round(($this->raised_amount / $this->target_amount) * 100, 2) : 0;
    }
}
