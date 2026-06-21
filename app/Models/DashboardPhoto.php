<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_number',
        'type',
        'photo_path',
        'thumbnail_path',
        'title',
        'description',
        'display_order',
        'is_active',
        'uploaded_by',
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

    public function getOrderAttribute(): ?int
    {
        return $this->display_order;
    }

    public function setOrderAttribute($value): void
    {
        $this->attributes['display_order'] = $value;
    }
}
