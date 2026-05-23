<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class GeneratedReport extends Model
{
    protected $table = 'generated_reports';
    public $timestamps = false;

    protected $fillable = [
        'report_number',
        'name',
        'type',
        'from_date',
        'to_date',
        'format',
        'file_path',
        'file_size',
        'parameters',
        'filters',
        'columns',
        'row_count',
        'generated_at',
        'generated_by',
        'downloaded_count',
        'last_downloaded_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'parameters' => 'array',
        'filters' => 'array',
        'columns' => 'array',
        'generated_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
