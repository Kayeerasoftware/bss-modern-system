<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'processing_time',
        'fee_percentage',
        'fee_fixed',
        'requires_reference',
        'icon',
        'sort_order',
    ];
}
