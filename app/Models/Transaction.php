<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'member_id',
        'amount',
        'type',
        'category',
        'fee',
        'tax_amount',
        'commission',
        'exchange_rate',
        'currency',
        'net_amount',
        'balance_before',
        'balance_after',
        'description',
        'notes',
        'payment_method',
        'channel',
        'device_info',
        'ip_address',
        'location',
        'reference',
        'receipt_number',
        'reversal_reason',
        'reversed_at',
        'reversed_by',
        'parent_transaction_id',
        'batch_id',
        'reconciled',
        'reconciled_at',
        'reconciled_by',
        'attachments',
        'metadata',
        'priority',
        'status',
        'processed_by',
        'approved_by',
        'transaction_date',
        'scheduled_at',
        'completed_at',
        'failed_at',
        'failure_reason',
        'retry_count',
        'approved_at',
        'notification_sent',
        'notification_sent_at',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'approved_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'reversed_at' => 'datetime',
        'reconciled_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'net_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'reconciled' => 'boolean',
        'notification_sent' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
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
        
        if ($this->type === 'withdrawal') {
            return $this->amount - $totalDeductions;
        }
        return $this->amount;
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
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    public function reverse($reason, $userId)
    {
        if (!$this->canBeReversed()) {
            return false;
        }

        $this->update([
            'status' => 'reversed',
            'reversed_at' => now(),
            'reversed_by' => $userId,
            'reversal_reason' => $reason,
        ]);

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

        if ($this->type === 'deposit') {
            $this->balance_after = $this->balance_before + $this->amount;
        } elseif ($this->type === 'withdrawal') {
            $this->balance_after = $this->balance_before - $this->net_amount;
        }

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                do {
                    $transaction->transaction_id = 'TXN' . strtoupper(Str::random(8));
                } while (self::where('transaction_id', $transaction->transaction_id)->exists());
            }
        });
    }
}