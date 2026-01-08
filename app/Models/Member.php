<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'member_id', 'full_name', 'email', 'profile_picture', 'location', 'occupation',
        'contact', 'password', 'role', 'savings', 'loan', 'savings_balance'
    ];

    protected $casts = [
        'savings' => 'decimal:2',
        'loan' => 'decimal:2',
        'balance' => 'decimal:2',
        'savings_balance' => 'decimal:2',
    ];

    protected $hidden = ['password'];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'member_id', 'member_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'member_id', 'member_id');
    }

    public function calculateInterest($amount, $months)
    {
        return round($amount * 0.1 * ($months / 12));
    }

    public function calculateMonthlyPayment($amount, $interest, $months)
    {
        return round(($amount + $interest) / $months);
    }

    public function shares()
    {
        return $this->hasMany(Share::class, 'member_id', 'member_id');
    }

    public function dividends()
    {
        return $this->hasMany(Dividend::class, 'member_id', 'member_id');
    }

    public function bioData()
    {
        return $this->hasOne(BioData::class, 'member_id', 'member_id');
    }

    public function getTotalSharesAttribute()
    {
        return $this->shares()->sum('shares_owned');
    }

    public function getTotalShareValueAttribute()
    {
        return $this->shares()->sum(DB::raw('shares_owned * share_value'));
    }

    public function getBalanceAttribute()
    {
        return $this->savings - $this->loan;
    }
}
