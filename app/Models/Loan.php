<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    public const LEGACY_STATUS_ALIASES = [
        'active' => self::STATUS_APPROVED,
        'disbursed' => self::STATUS_APPROVED,
        'completed' => self::STATUS_APPROVED,
        'paid' => self::STATUS_APPROVED,
        'defaulted' => self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'loan_id', 'member_id', 'amount', 'purpose', 'applicant_comment', 'repayment_months',
        'interest_rate', 'interest', 'processing_fee', 'monthly_payment', 'paid_amount', 'status',
        'updated_by', 'approved_at',
        'guarantor_1_name', 'guarantor_1_phone', 'guarantor_2_name', 'guarantor_2_phone',
        'settings_min_interest_rate', 'settings_max_interest_rate', 'settings_min_loan_amount',
        'settings_max_loan_amount', 'settings_max_loan_to_savings_ratio', 'settings_min_repayment_months',
        'settings_max_repayment_months', 'settings_default_repayment_months', 'settings_processing_fee_percentage',
        'settings_late_payment_penalty', 'settings_grace_period_days', 'settings_auto_approve_amount',
        'settings_require_guarantors', 'settings_guarantors_required', 'settings_email_notifications',
        'settings_sms_notifications', 'settings_payment_reminder_days'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'interest' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'duration',
        'amount_paid',
        'remaining_balance',
        'balance',
        'status_label',
    ];

    protected static function booted(): void
    {
        static::creating(function (Loan $loan): void {
            if (!empty($loan->loan_id)) {
                return;
            }

            do {
                $loan->loan_id = 'LOAN' . strtoupper(Str::random(8));
            } while (self::where('loan_id', $loan->loan_id)->exists());
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id')
            ->withDefault([
                'member_id' => 'N/A',
                'full_name' => 'Unknown Member',
                'email' => null,
                'contact' => null,
                'profile_picture' => null,
            ]);
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        if (!$status) {
            return $query;
        }

        if ($status === 'repaid') {
            return $query->where('status', self::STATUS_APPROVED)
                ->whereRaw('(COALESCE(amount, 0) + COALESCE(interest, 0) + COALESCE(processing_fee, 0)) <= COALESCE(paid_amount, 0)');
        }

        return $query->whereIn('status', self::mapStatusForQuery($status));
    }

    public static function normalizeStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        if ($value === '') {
            return self::STATUS_PENDING;
        }

        if (array_key_exists($value, self::LEGACY_STATUS_ALIASES)) {
            $value = self::LEGACY_STATUS_ALIASES[$value];
        }

        return in_array($value, self::VALID_STATUSES, true) ? $value : self::STATUS_PENDING;
    }

    public static function mapStatusForQuery(string $status): array
    {
        $value = strtolower(trim($status));
        $mapped = self::LEGACY_STATUS_ALIASES[$value] ?? $value;

        return in_array($mapped, self::VALID_STATUSES, true) ? [$mapped] : self::VALID_STATUSES;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = self::normalizeStatus($value);
    }

    public function setDurationAttribute($value): void
    {
        $this->attributes['repayment_months'] = (int) $value;
    }

    public function getDurationAttribute(): int
    {
        return (int) ($this->attributes['repayment_months'] ?? 0);
    }

    public function setAmountPaidAttribute($value): void
    {
        $this->attributes['paid_amount'] = max((float) $value, 0);
    }

    public function getAmountPaidAttribute(): float
    {
        return (float) ($this->attributes['paid_amount'] ?? 0);
    }

    public function setApprovedByAttribute($value): void
    {
        $this->attributes['updated_by'] = $value;
    }

    public function getApprovedByAttribute(): ?string
    {
        return $this->attributes['updated_by'] ?? null;
    }

    public function setApprovedDateAttribute($value): void
    {
        $this->attributes['approved_at'] = $value;
    }

    public function getApprovedDateAttribute()
    {
        return $this->approved_at;
    }

    public function setApplicationDateAttribute($value): void
    {
        $this->attributes['created_at'] = $value;
    }

    public function getApplicationDateAttribute()
    {
        return $this->created_at;
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) ($this->attributes['paid_amount'] ?? 0);
    }

    public function getRemainingBalanceAttribute(): float
    {
        $total = (float) ($this->amount ?? 0) + (float) ($this->interest ?? 0) + (float) ($this->processing_fee ?? 0);
        $remaining = $total - $this->paid_amount;

        return $remaining > 0 ? $remaining : 0.0;
    }

    public function getBalanceAttribute(): float
    {
        return $this->remaining_balance;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === self::STATUS_APPROVED && $this->remaining_balance <= 0) {
            return 'Repaid';
        }

        return Str::title((string) $this->status);
    }
}
