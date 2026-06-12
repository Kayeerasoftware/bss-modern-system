<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fundraising extends Model
{
    protected $table = 'fundraising_campaigns';

    protected $fillable = [
        'campaign_number',
        'category_id',
        'title',
        'description',
        'target_amount',
        'raised_amount',
        'min_contribution',
        'max_contribution',
        'start_date',
        'end_date',
        'status_id',
        'organizer_id',
        'contact_person',
        'contact_phone',
        'contact_email',
        'location_text',
        'cover_image',
        'gallery',
        'video_url',
        'bank_account_details',
        'mobile_money_details',
        'is_tax_deductible',
        'tax_receipts_issued',
        'updates',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'gallery' => 'array',
        'bank_account_details' => 'array',
        'mobile_money_details' => 'array',
        'updates' => 'array',
        'metadata' => 'array',
        'is_tax_deductible' => 'boolean',
        'tax_receipts_issued' => 'boolean',
    ];

    public function contributions()
    {
        return $this->hasMany(FundraisingContribution::class, 'campaign_id');
    }

    public function expenses()
    {
        return $this->hasMany(FundraisingExpense::class, 'campaign_id');
    }

    public function statusRelation()
    {
        return $this->belongsTo(FundraisingStatus::class, 'status_id');
    }

    public function getStatusAttribute(): ?string
    {
        return $this->statusRelation?->name;
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = FundraisingStatus::query()
            ->where('name', strtolower((string) $value))
            ->value('id');
    }

    public function getCampaignIdAttribute(): ?string
    {
        return $this->campaign_number;
    }

    public function setCampaignIdAttribute($value): void
    {
        $this->campaign_number = $value;
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
