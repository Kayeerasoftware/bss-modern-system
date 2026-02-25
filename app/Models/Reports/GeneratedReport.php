<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class GeneratedReport extends Model
{
    protected $fillable = ['name', 'type', 'from_date', 'to_date', 'format', 'file_path', 'user_id'];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}
