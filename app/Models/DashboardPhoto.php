<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'photo_path',
        'title',
        'description',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProjects($query)
    {
        return $query->where('type', 'project');
    }

    public function scopeMeetings($query)
    {
        return $query->where('type', 'meeting');
    }
}
