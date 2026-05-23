<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected static ?array $statusNameCache = null;
    protected static ?array $statusDisplayCache = null;

    protected $table = 'loan_applications';

    protected $fillable = [
        'application_number',
        'member_id',
        'loan_type_id',
        'requested_amount',
        'approved_amount',
        'requested_tenure_months',
        'approved_tenure_months',
        'purpose',
        'applicant_comment',
        'monthly_income',
        'monthly_expenses',
        'existing_loan_commitments',
        'credit_score',
        'risk_rating',
        'assessment_notes',
        'assessed_by',
        'assessed_at',
        'status_id',
        'submission_date',
        'decision_date',
        'decision_by',
        'decision_notes',
        'rejection_reason',
        'requires_approval',
        'approval_level',
        'current_approval_level',
        'converted_to_loan_id',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'monthly_expenses' => 'decimal:2',
        'existing_loan_commitments' => 'decimal:2',
        'assessed_at' => 'datetime',
        'submission_date' => 'datetime',
        'decision_date' => 'datetime',
        'requires_approval' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (LoanApplication $model): void {
            if (!empty($model->application_number)) {
                return;
            }

            do {
                $model->application_number = 'LAPP-' . now()->format('Ym') . '-' . strtoupper(Str::random(4));
            } while (self::where('application_number', $model->application_number)->exists());
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function getApplicationIdAttribute(): ?string
    {
        return $this->application_number;
    }

    public function setApplicationIdAttribute($value): void
    {
        $this->application_number = $value;
    }

    public function getAmountAttribute(): ?float
    {
        return $this->requested_amount;
    }

    public function setAmountAttribute($value): void
    {
        $this->requested_amount = $value;
    }

    public function getStatusAttribute(): ?string
    {
        if ($this->relationLoaded('statusRelation')) {
            return $this->statusRelation?->name;
        }

        return self::statusNameForId($this->status_id);
    }

    public function getStatusLabelAttribute(): ?string
    {
        $label = $this->statusRelation?->display_name
            ?? $this->statusRelation?->name
            ?? self::statusDisplayForId($this->status_id);

        if (!$label) {
            $label = $this->getStatusAttribute();
        }

        return $label ? ucwords(str_replace('_', ' ', (string) $label)) : null;
    }

    public function getRepaymentMonthsAttribute(): ?int
    {
        $months = $this->approved_tenure_months ?? $this->requested_tenure_months;
        return $months !== null ? (int) $months : null;
    }

    public function setRepaymentMonthsAttribute($value): void
    {
        $this->requested_tenure_months = $value;
    }

    public function getApprovalCommentAttribute(): ?string
    {
        return $this->decision_notes ?? $this->assessment_notes;
    }

    public function setApprovalCommentAttribute($value): void
    {
        $this->decision_notes = $value;
    }

    public function getInterestRateAttribute(): float
    {
        if ($this->relationLoaded('loanType')) {
            $rate = $this->loanType?->default_interest_rate;
            if ($rate !== null) {
                return (float) $rate;
            }
        }

        $rate = $this->loanType()?->value('default_interest_rate');
        if ($rate !== null) {
            return (float) $rate;
        }

        return (float) setting('default_interest_rate', 10);
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = self::resolveStatusId($value);
    }

    public function statusRelation()
    {
        return $this->belongsTo(LoanStatus::class, 'status_id');
    }

    public function scopeStatus(Builder $query, $status): Builder
    {
        if ($status === null || $status === '') {
            return $query;
        }

        $statusId = self::resolveStatusId($status);
        if (!$statusId) {
            return $query->whereRaw('1=0');
        }

        return $query->where('status_id', $statusId);
    }

    protected static function resolveStatusId($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $statusId = LoanStatus::query()->where('name', strtolower((string) $value))->value('id');

        return $statusId ? (int) $statusId : null;
    }

    protected static function statusNameForId(?int $statusId): ?string
    {
        if (!$statusId) {
            return null;
        }

        if (self::$statusNameCache === null) {
            self::$statusNameCache = LoanStatus::query()
                ->pluck('name', 'id')
                ->all();
        }

        return self::$statusNameCache[$statusId] ?? null;
    }

    protected static function statusDisplayForId(?int $statusId): ?string
    {
        if (!$statusId) {
            return null;
        }

        if (self::$statusDisplayCache === null) {
            self::$statusDisplayCache = LoanStatus::query()
                ->select('id', 'display_name', 'name')
                ->get()
                ->mapWithKeys(fn ($row) => [$row->id => $row->display_name ?: $row->name])
                ->all();
        }

        return self::$statusDisplayCache[$statusId] ?? null;
    }
}
