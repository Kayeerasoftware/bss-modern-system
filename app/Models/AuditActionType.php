<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditActionType extends Model
{
    protected $table = 'audit_action_types';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'severity',
    ];
}
