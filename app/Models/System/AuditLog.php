<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user', 'action', 'details', 'changes', 'timestamp'];
    
    protected $casts = [
        'timestamp' => 'datetime',
        'changes' => 'array',
    ];
}
