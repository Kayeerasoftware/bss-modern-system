<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    protected $table = 'transaction_types';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'impact',
        'requires_approval',
        'affects_savings',
        'affects_loan',
        'affects_share',
        'is_fee',
        'is_taxable',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];
}
