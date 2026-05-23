<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'log_number',
        'user_id',
        'member_id',
        'ip_address',
        'user_agent',
        'session_id',
        'action_type_id',
        'entity_type_id',
        'entity_id',
        'entity_identifier',
        'description',
        'details',
        'created_at',
    ];
    
    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];
}
