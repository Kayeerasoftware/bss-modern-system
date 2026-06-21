<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_base',
        'exchange_rate',
    ];
}
