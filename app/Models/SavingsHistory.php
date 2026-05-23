<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsHistory extends Model
{
    use HasFactory;

    protected $table = 'savings_transactions';
    public $timestamps = false;

    protected $fillable = [
        'savings_account_id',
        'transaction_id',
        'amount',
        'running_balance',
        'transaction_type',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
    ];

    public function savingsAccount()
    {
        return $this->belongsTo(SavingsAccount::class, 'savings_account_id');
    }

    public function scopeForMember($query, int $memberId)
    {
        return $query->whereHas('savingsAccount', fn ($q) => $q->where('member_id', $memberId));
    }

    public function scopeForMembers($query, $memberIds)
    {
        $ids = is_array($memberIds) ? $memberIds : $memberIds->toArray();
        return $query->whereHas('savingsAccount', fn ($q) => $q->whereIn('member_id', $ids));
    }

    public function getMemberAttribute(): ?Member
    {
        return $this->savingsAccount?->member;
    }

    public function getBalanceAfterAttribute(): float
    {
        return (float) ($this->running_balance ?? 0);
    }

    public function getTransactionDateAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->created_at;
    }
}
