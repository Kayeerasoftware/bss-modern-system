<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'shares_owned',
        'share_value',
        'total_value',
        'purchase_date',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'share_value' => 'decimal:2',
        'total_value' => 'decimal:2'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function calculateTotalValue()
    {
        $this->total_value = $this->shares_owned * $this->share_value;
        return $this->total_value;
    }
}