<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'title', 'message', 'type', 'is_read', 'created_by', 'roles'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'roles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}