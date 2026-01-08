<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'scheduled_at', 'location', 'status', 
        'attendees', 'minutes', 'created_by'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'attendees' => 'array'
    ];
}