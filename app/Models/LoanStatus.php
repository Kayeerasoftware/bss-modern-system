<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanStatus extends Model
{
    protected $table = 'loan_statuses';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];
}
