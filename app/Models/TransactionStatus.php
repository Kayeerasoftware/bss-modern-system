<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    protected $table = 'transaction_statuses';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
        'is_final',
        'sort_order',
    ];
}
