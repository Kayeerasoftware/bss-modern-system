<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'member_id',
        'transaction_number',
        'transaction_id',
        'transaction_type_id',
        'type',
        'category_id',
        'category',
        'status_id',
        'status',
        'amount',
        'fee',
        'tax_amount',
        'commission',
        'exchange_rate',
        'currency_id',
        'currency',
        'balance_before',
        'balance_after',
        'description',
        'notes',
        'payment_method_id',
        'payment_method',
        'channel',
        'terminal_id',
        'reference_number',
        'reference',
        'receipt_number',
        'receipt',
        'related_loan_id',
        'related_share_id',
        'related_dividend_id',
        'related_investment_id',
        'related_transfer_to_member_id',
        'requires_approval',
        'approval_level',
        'current_approval_level',
        'approved_by',
        'approved_at',
        'approval_notes',
        'processed_by',
        'processed_at',
        'processed_ip',
        'processed_location',
        'is_reversal',
        'parent_transaction_id',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
        'reconciled',
        'reconciled_at',
        'reconciled_by',
        'reconciliation_notes',
        'metadata',
        'transaction_date',
        'value_date',
        'is_scheduled',
        'scheduled_at',
        'schedule_rule',
        'deleted_by',
        'deleted_reason',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'approved_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'reversed_at' => 'datetime',
        'reconciled_at' => 'datetime',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'net_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'reconciled' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted()
    {
        $stripGenerated = function (self $model): void {
            if ($model->isDirty('net_amount')) {
                $model->offsetUnset('net_amount');
            }
        };

        static::creating($stripGenerated);
        static::updating($stripGenerated);
    }

    public function scopeOfType($query, ?string $type)
    {
        $normalized = strtolower(trim((string) $type));
        if ($normalized === '') {
            return $query;
        }

        $typeId = TransactionType::query()->where('name', $normalized)->value('id');
        if (!$typeId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('transaction_type_id', $typeId);
    }

    public function scopeOfStatus($query, ?string $status)
    {
        $normalized = strtolower(trim((string) $status));
        if ($normalized === '') {
            return $query;
        }

        $statusId = TransactionStatus::query()->where('name', $normalized)->value('id');
        if (!$statusId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('status_id', $statusId);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function reconciler()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function parentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    public function childTransactions()
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    public function calculateNetAmount()
    {
        $totalDeductions = $this->fee + $this->tax_amount + $this->commission;

        return $this->amount - $totalDeductions;
    }

    public function calculateTotalCharges()
    {
        return $this->fee + $this->tax_amount + $this->commission;
    }

    public function isReversed()
    {
        return !is_null($this->reversed_at);
    }

    public function isReconciled()
    {
        return $this->reconciled;
    }

    public function canBeReversed()
    {
        return $this->status === 'completed' && !$this->isReversed();
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        $this->reversal_reason = $reason;
        $this->save();
    }

    public function reverse($reason, $userId)
    {
        if (!$this->canBeReversed()) {
            return false;
        }

        $this->status = 'reversed';
        $this->reversed_at = now();
        $this->reversed_by = $userId;
        $this->reversal_reason = $reason;
        $this->save();

        return true;
    }

    public function reconcile($userId)
    {
        $this->update([
            'reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by' => $userId,
        ]);
    }

    public function updateMemberBalance()
    {
        if (!$this->member) return;

        $this->balance_before = $this->member->balance ?? 0;
        $netAmount = $this->net_amount ?? ($this->amount - (float) ($this->fee ?? 0) - (float) ($this->tax_amount ?? 0) - (float) ($this->commission ?? 0));

        if ($this->type === 'deposit') {
            $this->balance_after = $this->balance_before + $netAmount;
        } elseif ($this->type === 'withdrawal') {
            $this->balance_after = $this->balance_before - $netAmount;
        }

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                do {
                    $transaction->transaction_number = 'TXN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
                } while (self::where('transaction_number', $transaction->transaction_number)->exists());
            }

            if (empty($transaction->transaction_type_id) && !empty($transaction->type)) {
                $transaction->transaction_type_id = $transaction->resolveTransactionTypeId($transaction->type);
            }

            if (empty($transaction->status_id) && !empty($transaction->status)) {
                $transaction->status_id = $transaction->resolveStatusId($transaction->status);
            }

            if (empty($transaction->status_id)) {
                $transaction->status_id = TransactionStatus::query()->where('name', 'completed')->value('id')
                    ?? TransactionStatus::query()->where('name', 'pending')->value('id')
                    ?? TransactionStatus::query()->value('id');
            }

            if (empty($transaction->category_id) && !empty($transaction->category)) {
                $transaction->category_id = $transaction->resolveCategoryId($transaction->category, $transaction->transaction_type_id);
            }

            if (empty($transaction->category_id)) {
                $categoryQuery = TransactionCategory::query();
                if (!empty($transaction->transaction_type_id)) {
                    $categoryQuery->where('transaction_type_id', $transaction->transaction_type_id);
                }
                $transaction->category_id = $categoryQuery->value('id');
            }

            if (empty($transaction->payment_method_id) && !empty($transaction->payment_method)) {
                $transaction->payment_method_id = $transaction->resolvePaymentMethodId($transaction->payment_method);
            }

            if (empty($transaction->payment_method_id)) {
                $transaction->payment_method_id = PaymentMethod::query()->where('name', 'cash')->value('id')
                    ?? PaymentMethod::query()->value('id');
            }

            if (empty($transaction->currency_id) && !empty($transaction->currency)) {
                $transaction->currency_id = $transaction->resolveCurrencyId($transaction->currency);
            }

            if (empty($transaction->currency_id)) {
                $transaction->currency_id = Currency::query()->where('code', 'UGX')->value('id')
                    ?? Currency::query()->value('id');
            }

            if (empty($transaction->processed_by)) {
                $transaction->processed_by = auth()->id()
                    ?? $transaction->member?->user_id
                    ?? User::query()->value('id');
            }

            if (empty($transaction->balance_before) || empty($transaction->balance_after)) {
                $balanceBefore = (float) ($transaction->balance_before ?? 0);
                if ($balanceBefore === 0.0 && $transaction->member) {
                    $balanceBefore = (float) ($transaction->member->savings_balance ?? $transaction->member->balance ?? 0);
                }

                $impact = null;
                if (!empty($transaction->transaction_type_id)) {
                    $impact = TransactionType::query()->whereKey($transaction->transaction_type_id)->value('impact');
                }

                $amount = (float) ($transaction->amount ?? 0);
                $netAmount = (float) ($transaction->net_amount ?? ($amount - (float) ($transaction->fee ?? 0) - (float) ($transaction->tax_amount ?? 0) - (float) ($transaction->commission ?? 0)));

                if ($impact === 'credit') {
                    $transaction->balance_before = $balanceBefore;
                    $transaction->balance_after = $balanceBefore + $netAmount;
                } elseif ($impact === 'debit') {
                    $transaction->balance_before = $balanceBefore;
                    $transaction->balance_after = $balanceBefore - $netAmount;
                } else {
                    $transaction->balance_before = $balanceBefore;
                    $transaction->balance_after = $transaction->balance_after ?? $balanceBefore;
                }
            }
        });
    }

    public function getTransactionIdAttribute(): ?string
    {
        return $this->transaction_number;
    }

    public function setTransactionIdAttribute($value): void
    {
        $this->transaction_number = $value;
    }

    public function getReferenceAttribute(): ?string
    {
        return $this->reference_number;
    }

    public function setReferenceAttribute($value): void
    {
        $this->reference_number = $value;
    }

    public function getReceiptAttribute(): ?string
    {
        return $this->receipt_number;
    }

    public function setReceiptAttribute($value): void
    {
        $this->receipt_number = $value;
    }

    public function getTypeAttribute(): ?string
    {
        if ($this->relationLoaded('transactionType')) {
            return $this->transactionType?->name;
        }

        $typeId = $this->attributes['transaction_type_id'] ?? null;
        if ($typeId) {
            return TransactionType::query()->whereKey($typeId)->value('name');
        }

        return null;
    }

    public function setTypeAttribute($value): void
    {
        $this->transaction_type_id = $this->resolveTransactionTypeId($value);
    }

    public function getCategoryAttribute(): ?string
    {
        if ($this->relationLoaded('transactionCategory')) {
            return $this->transactionCategory?->name;
        }

        $categoryId = $this->attributes['category_id'] ?? null;
        if ($categoryId) {
            return TransactionCategory::query()->whereKey($categoryId)->value('name');
        }

        return null;
    }

    public function setCategoryAttribute($value): void
    {
        $this->category_id = $this->resolveCategoryId($value, $this->transaction_type_id);
    }

    public function getStatusAttribute(): ?string
    {
        if ($this->relationLoaded('statusRelation')) {
            return $this->statusRelation?->name;
        }

        $statusId = $this->attributes['status_id'] ?? null;
        if ($statusId) {
            return TransactionStatus::query()->whereKey($statusId)->value('name');
        }

        return null;
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = $this->resolveStatusId($value);
    }

    public function getPaymentMethodAttribute(): ?string
    {
        if ($this->relationLoaded('paymentMethod')) {
            return $this->paymentMethod?->name;
        }

        $methodId = $this->attributes['payment_method_id'] ?? null;
        if ($methodId) {
            return PaymentMethod::query()->whereKey($methodId)->value('name');
        }

        return null;
    }

    public function setPaymentMethodAttribute($value): void
    {
        $this->payment_method_id = $this->resolvePaymentMethodId($value);
    }

    public function getCurrencyAttribute(): ?string
    {
        if ($this->relationLoaded('currency')) {
            return $this->currency?->code;
        }

        $currencyId = $this->attributes['currency_id'] ?? null;
        if ($currencyId) {
            return Currency::query()->whereKey($currencyId)->value('code');
        }

        return null;
    }

    public function setCurrencyAttribute($value): void
    {
        $this->currency_id = $this->resolveCurrencyId($value);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function statusRelation()
    {
        return $this->belongsTo(TransactionStatus::class, 'status_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    private function resolveTransactionTypeId($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return TransactionType::query()->where('name', $value)->value('id');
    }

    private function resolveCategoryId($value, $transactionTypeId = null): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $query = TransactionCategory::query()->where('name', $value);
        if ($transactionTypeId) {
            $query->where('transaction_type_id', $transactionTypeId);
        }

        return $query->value('id');
    }

    private function resolveStatusId($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return TransactionStatus::query()->where('name', $value)->value('id');
    }

    private function resolvePaymentMethodId($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return PaymentMethod::query()->where('name', $value)->value('id');
    }

    private function resolveCurrencyId($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return Currency::query()->where('code', $value)->value('id');
    }
}
