<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundraisingExpense extends Model
{
    protected $fillable = [
        'expense_id',
        'fundraising_id',
        'description',
        'amount',
        'category',
        'expense_date',
        'receipt_number'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function fundraising()
    {
        return $this->belongsTo(Fundraising::class);
    }
}
