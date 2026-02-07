<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundraisingContribution extends Model
{
    protected $fillable = [
        'contribution_id',
        'fundraising_id',
        'contributor_name',
        'contributor_email',
        'contributor_phone',
        'amount',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function fundraising()
    {
        return $this->belongsTo(Fundraising::class);
    }
}
