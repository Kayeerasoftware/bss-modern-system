<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'amount',
        'payment_date',
        'dividend_rate',
        'shares_eligible',
        'status'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'dividend_rate' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}