<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Share extends Model
{
    use HasFactory;

    protected $table = 'shares';

    protected $fillable = [
        'share_number',
        'certificate_number',
        'member_id',
        'share_class_id',
        'purchase_id',
        'shares_count',
        'purchase_price',
        'current_value',
        'purchase_date',
        'vesting_date',
        'expiry_date',
        'status_id',
        'sold_date',
        'sold_price',
        'sold_to_member_id',
        'sale_transaction_id',
        'dividend_eligible',
        'last_dividend_paid',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'vesting_date' => 'date',
        'expiry_date' => 'date',
        'sold_date' => 'date',
        'last_dividend_paid' => 'date',
        'dividend_eligible' => 'boolean',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'sold_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Share $share): void {
            if (!empty($share->share_number)) {
                return;
            }

            do {
                $share->share_number = 'SHR-' . now()->format('Ym') . '-' . strtoupper(Str::random(4));
            } while (self::where('share_number', $share->share_number)->exists());
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function getSharesOwnedAttribute(): ?int
    {
        return $this->shares_count;
    }

    public function setSharesOwnedAttribute($value): void
    {
        $this->shares_count = $value;
    }

    public function getShareValueAttribute(): ?float
    {
        return $this->current_value;
    }

    public function setShareValueAttribute($value): void
    {
        $this->current_value = $value;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->statusRelation?->name;
    }

    public function setStatusAttribute($value): void
    {
        $this->status_id = ShareStatus::query()->where('name', strtolower((string) $value))->value('id');
    }

    public function statusRelation()
    {
        return $this->belongsTo(ShareStatus::class, 'status_id');
    }
}
