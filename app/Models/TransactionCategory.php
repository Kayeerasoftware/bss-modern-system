<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $table = 'transaction_categories';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'display_name',
        'transaction_type_id',
        'description',
        'is_system',
        'requires_reference',
        'requires_approval',
        'fee_percentage',
        'fee_fixed',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
