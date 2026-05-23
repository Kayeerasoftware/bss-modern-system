<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvestmentOpportunity extends Model
{
    protected $table = 'investment_opportunities';

    protected $fillable = [
        'opportunity_number',
        'title',
        'description',
        'target_amount',
        'minimum_investment',
        'maximum_investment',
        'expected_roi',
        'projected_returns',
        'risk_level_id',
        'launch_date',
        'deadline_date',
        'close_date',
        'raised_amount',
        'investor_count',
        'status_id',
        'prospectus_document_id',
        'fund_manager_id',
        'lock_in_period_months',
        'dividend_frequency',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'launch_date' => 'date',
        'deadline_date' => 'date',
        'close_date' => 'date',
        'projected_returns' => 'array',
        'metadata' => 'array',
        'target_amount' => 'decimal:2',
        'minimum_investment' => 'decimal:2',
        'maximum_investment' => 'decimal:2',
        'expected_roi' => 'decimal:2',
        'raised_amount' => 'decimal:2'
    ];

    public function getNameAttribute(): ?string
    {
        return $this->title;
    }

    public function setNameAttribute($value): void
    {
        $this->title = $value;
    }

    public function getDeadlineAttribute()
    {
        return $this->deadline_date;
    }

    public function setDeadlineAttribute($value): void
    {
        $this->deadline_date = $value;
    }

    public function getStatusAttribute(): ?string
    {
        $statusId = $this->attributes['status_id'] ?? null;
        if (!$statusId) {
            return null;
        }

        return DB::table('investment_statuses')->whereKey($statusId)->value('name');
    }

    public function setStatusAttribute($value): void
    {
        if (empty($value)) {
            return;
        }

        if (is_numeric($value)) {
            $this->status_id = (int) $value;
            return;
        }

        $this->status_id = DB::table('investment_statuses')->where('name', $value)->value('id');
    }

    public function getRiskLevelAttribute(): ?string
    {
        $riskId = $this->attributes['risk_level_id'] ?? null;
        if (!$riskId) {
            return null;
        }

        return DB::table('investment_risk_levels')->whereKey($riskId)->value('name');
    }

    public function setRiskLevelAttribute($value): void
    {
        if (empty($value)) {
            return;
        }

        if (is_numeric($value)) {
            $this->risk_level_id = (int) $value;
            return;
        }

        $this->risk_level_id = DB::table('investment_risk_levels')->where('name', $value)->value('id');
    }
}
