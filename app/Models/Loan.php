<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DISBURSED = 'disbursed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    protected static ?array $statusNameCache = null;
    protected static ?array $statusDisplayCache = null;

    protected $table = 'loans';

    protected $fillable = [
        'loan_number',
        'application_id',
        'member_id',
        'loan_type_id',
        'principal_amount',
        'interest_rate',
        'interest_type',
        'total_interest',
        'repayment_months',
        'repayment_frequency',
        'processing_fee',
        'insurance_fee',
        'legal_fee',
        'other_fees',
        'guarantor1_id',
        'guarantor2_id',
        'has_collateral',
        'collateral_details',
        'application_date',
        'approval_date',
        'disbursement_date',
        'first_payment_date',
        'completed_date',
        'disbursement_transaction_id',
        'disbursement_method_id',
        'amount_paid',
        'last_payment_date',
        'last_payment_amount',
        'payments_made',
        'status_id',
        'approved_by',
        'approved_at',
        'approved_ip',
        'disbursed_by',
        'disbursed_at',
        'closed_by',
        'closed_at',
        'closed_reason',
        'is_defaulted',
        'defaulted_date',
        'default_amount',
        'days_overdue',
        'last_reminder_sent',
        'is_restructured',
        'original_loan_id',
        'restructure_date',
        'restructure_reason',
        'notes',
        'metadata',
        'deleted_by',
        'deleted_reason',
    ];

    protected $casts = [
        'application_date' => 'date',
        'approval_date' => 'date',
        'disbursement_date' => 'date',
        'first_payment_date' => 'date',
        'completed_date' => 'date',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'closed_at' => 'datetime',
        'defaulted_date' => 'date',
        'last_reminder_sent' => 'datetime',
        'restructure_date' => 'date',
        'has_collateral' => 'boolean',
        'is_defaulted' => 'boolean',
        'is_restructured' => 'boolean',
        'collateral_details' => 'array',
        'metadata' => 'array',
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'legal_fee' => 'decimal:2',
        'other_fees' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'last_payment_amount' => 'decimal:2',
        'default_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Loan $loan): void {
            if (!empty($loan->loan_number)) {
                return;
            }

            do {
                $loan->loan_number = 'LOAN-' . now()->format('Ym') . '-' . strtoupper(Str::random(4));
            } while (self::where('loan_number', $loan->loan_number)->exists());
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'application_id');
    }

    public function statusRelation()
    {
        return $this->belongsTo(LoanStatus::class, 'status_id');
    }

    public function getLoanIdAttribute(): ?string
    {
        return $this->loan_number;
    }

    public function setLoanIdAttribute($value): void
    {
        $this->loan_number = $value;
    }

    public function getAmountAttribute(): ?float
    {
        return $this->principal_amount;
    }

    public function setAmountAttribute($value): void
    {
        $this->principal_amount = $value;
    }

    public function getPurposeAttribute(): ?string
    {
        return $this->loanApplication?->purpose ?? $this->notes;
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
        $label = null;
        if ($this->relationLoaded('statusRelation')) {
            $label = $this->statusRelation?->display_name
                ?? $this->statusRelation?->name;
        }

        if (!$label) {
            $label = self::statusDisplayForId($this->status_id);
        }

        if (!$label) {
            $label = $this->getStatusAttribute();
        }

        return $label ? ucwords(str_replace('_', ' ', (string) $label)) : null;
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = self::resolveStatusId($value);
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

    public function scopeFilterStatus(Builder $query, $status): Builder
    {
        return $this->scopeStatus($query, $status);
    }

    public function getRemainingBalanceAttribute(): float
    {
        $balance = $this->balance_due;
        if ($balance !== null) {
            return (float) $balance;
        }

        $total = (float) ($this->total_amount ?? 0);
        $paid = (float) ($this->amount_paid ?? 0);
        return max($total - $paid, 0.0);
    }

    public function getBalanceAttribute(): float
    {
        return $this->getRemainingBalanceAttribute();
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) ($this->amount_paid ?? 0);
    }

    public function setPaidAmountAttribute($value): void
    {
        $this->amount_paid = $value;
    }

    public function getInterestAttribute(): float
    {
        if ($this->total_interest !== null) {
            return (float) $this->total_interest;
        }

        return (float) ($this->interest_amount ?? 0);
    }

    public function setInterestAttribute($value): void
    {
        $this->total_interest = $value;
    }

    public function getMonthlyPaymentAttribute(): float
    {
        $raw = $this->getRawOriginal('monthly_payment');
        if ($raw !== null) {
            return (float) $raw;
        }

        $total = (float) ($this->total_amount ?? 0);
        $months = (int) ($this->repayment_months ?? 0);
        return $months > 0 ? round($total / $months, 2) : 0.0;
    }

    public function setMonthlyPaymentAttribute($value): void
    {
        // monthly_payment is generated in the database; ignore writes.
        unset($this->attributes['monthly_payment']);
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
