<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;

    protected $table = 'dividends';

    protected $fillable = [
        'dividend_number',
        'share_class_id',
        'amount_per_share',
        'total_shares_eligible',
        'year',
        'quarter',
        'period_start',
        'period_end',
        'declaration_date',
        'record_date',
        'payment_date',
        'total_paid',
        'total_withheld',
        'withholding_tax_rate',
        'status_id',
        'declared_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'declaration_date' => 'date',
        'record_date' => 'date',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function memberDividends()
    {
        return $this->hasMany(MemberDividend::class, 'dividend_id');
    }
}
