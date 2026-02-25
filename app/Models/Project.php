<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'name', 'budget', 'timeline', 'description',
        'progress', 'roi', 'risk_score', 'category', 'status',
        'start_date', 'end_date', 'manager', 'location', 'notes'
    ];

    protected $casts = [
        'timeline' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($project) {
            $project->project_id = 'PRJ' . Str::random(6);
        });
        
        static::deleting(function ($project) {
            // Delete related fundraising campaigns
            $project->fundraisingCampaigns()->delete();
        });
    }
    
    public function fundraisingCampaigns()
    {
        return $this->hasMany(\App\Models\Fundraising::class, 'project_id', 'id');
    }
}