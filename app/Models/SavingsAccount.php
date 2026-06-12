<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\System\AccountNumberService;

class SavingsAccount extends Model
{
    protected $table = 'savings_accounts';

    protected static function booted()
    {
        static::creating(function ($account) {
            if (empty($account->account_number)) {
                $account->account_number = AccountNumberService::generateSavingsAccountNumber();
            }
        });
    }

    protected $fillable = [
        'account_number',
        'member_id',
        'plan_id',
        'account_name',
        'opening_balance',
        'current_balance',
        'available_balance',
        'opening_date',
        'maturity_date',
        'closing_date',
        'last_interest_calculation',
        'accrued_interest',
        'overdraft_limit',
        'overdraft_used',
        'is_joint',
        'joint_holders',
        'status',
        'status_reason',
        'frozen_by',
        'frozen_at',
        'standing_instructions',
        'notes',
        'closed_at',
        'closed_by',
        'closed_reason',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'maturity_date' => 'date',
        'closing_date' => 'date',
        'last_interest_calculation' => 'date',
        'frozen_at' => 'datetime',
        'closed_at' => 'datetime',
        'joint_holders' => 'array',
        'standing_instructions' => 'array',
        'is_joint' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
