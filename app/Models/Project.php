<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'project_number',
        'name',
        'description',
        'category_id',
        'budget_amount',
        'committed_amount',
        'spent_amount',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'status_id',
        'progress_percentage',
        'milestones',
        'expected_revenue',
        'actual_revenue',
        'expected_roi',
        'actual_roi',
        'potential_roi',
        'risk_level_id',
        'risk_score',
        'project_manager_id',
        'supervisor_id',
        'location_text',
        'village_id',
        'latitude',
        'longitude',
        'proposal_document_id',
        'contract_document_id',
        'is_featured',
        'is_public',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'milestones' => 'array',
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'budget_amount' => 'decimal:2',
        'committed_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'expected_revenue' => 'decimal:2',
        'actual_revenue' => 'decimal:2',
        'expected_roi' => 'decimal:2',
        'actual_roi' => 'decimal:2',
        'potential_roi' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (empty($project->created_by)) {
                $project->created_by = auth()->id() ?? \App\Models\User::query()->value('id');
            }

            if (empty($project->status_id)) {
                $project->status_id = self::resolveStatusId('planning');
            }

            if (!empty($project->project_number)) {
                return;
            }

            do {
                $project->project_number = 'PRJ-' . now()->format('Ym') . '-' . strtoupper(Str::random(4));
            } while (self::where('project_number', $project->project_number)->exists());
        });

        static::updating(function (Project $project): void {
            if (empty($project->updated_by)) {
                $project->updated_by = auth()->id() ?? \App\Models\User::query()->value('id');
            }
        });
    }

    public function fundraisingCampaigns()
    {
        return $this->hasMany(\App\Models\Fundraising\FundraisingCampaign::class, 'project_id', 'id');
    }

    public function getProjectIdAttribute(): ?string
    {
        return $this->project_number;
    }

    public function setProjectIdAttribute($value): void
    {
        $this->project_number = $value;
    }

    public function getBudgetAttribute(): ?float
    {
        return $this->budget_amount;
    }

    public function setBudgetAttribute($value): void
    {
        $this->budget_amount = $value;
    }

    public function getProgressAttribute(): ?int
    {
        return $this->progress_percentage;
    }

    public function setProgressAttribute($value): void
    {
        $this->progress_percentage = $value;
    }

    public function getRoiAttribute(): ?float
    {
        return $this->actual_roi;
    }

    public function setRoiAttribute($value): void
    {
        $this->actual_roi = $value;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->statusRelation?->name;
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = self::resolveStatusId($value);
    }

    public function statusRelation()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    private static function resolveStatusId($value): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $name = strtolower(trim((string) $value));
        $name = $name !== '' ? $name : 'planning';

        $statusId = ProjectStatus::query()->where('name', $name)->value('id');
        if ($statusId) {
            return (int) $statusId;
        }

        $statusId = ProjectStatus::query()
            ->whereIn('name', ['planning', 'pending', 'active'])
            ->orderByRaw("FIELD(name, 'planning', 'pending', 'active')")
            ->value('id');

        if ($statusId) {
            return (int) $statusId;
        }

        $status = ProjectStatus::query()->firstOrCreate(
            ['name' => 'planning'],
            [
                'display_name' => 'Planning',
                'description' => 'Project is in planning stage',
                'color' => 'blue',
            ]
        );

        return (int) $status->id;
    }
}
