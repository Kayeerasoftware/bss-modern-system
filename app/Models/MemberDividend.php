<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberDividend extends Model
{
    protected $table = 'member_dividends';

    protected $fillable = [
        'dividend_id',
        'member_id',
        'shares_eligible',
        'amount_per_share',
        'withholding_tax',
        'transaction_id',
        'paid_at',
        'paid_by',
        'status',
        'payment_method_id',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function getAmountAttribute(): float
    {
        return (float) ($this->net_amount ?? 0);
    }

    public function getPaymentDateAttribute()
    {
        return $this->paid_at;
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function dividend()
    {
        return $this->belongsTo(Dividend::class, 'dividend_id');
    }
}
