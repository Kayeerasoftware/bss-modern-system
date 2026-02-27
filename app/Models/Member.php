<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'member_id', 'full_name', 'email', 'profile_picture', 'location', 'occupation',
        'contact', 'password', 'role', 'savings', 'loan', 'savings_balance', 'balance', 'user_id'
    ];

    protected $guarded = [];

    protected $casts = [
        'savings' => 'decimal:2',
        'loan' => 'decimal:2',
        'savings_balance' => 'decimal:2',
        'balance' => 'decimal:2',
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
        return $this->hasOne(BioData::class, 'member_id', 'id');
    }

    public function getTotalSharesAttribute()
    {
        return $this->shares()->sum('shares_owned');
    }

    public function getTotalShareValueAttribute()
    {
        return $this->shares()->sum(DB::raw('shares_owned * share_value'));
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePictureUrlAttribute()
    {
        // Check member's profile picture first
        $memberPictureUrl = $this->resolveProfilePictureUrl($this->profile_picture);
        if ($memberPictureUrl) {
            return $memberPictureUrl;
        }

        // Fall back to user's profile picture only when relation is already eager loaded.
        if ($this->relationLoaded('user') && $this->user) {
            $userPictureUrl = $this->resolveProfilePictureUrl($this->user->profile_picture);
            if ($userPictureUrl) {
                return $userPictureUrl;
            }
        }

        return asset('images/default-avatar.svg');
    }

    protected function resolveProfilePictureUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'public/')) {
            $normalizedPath = substr($normalizedPath, 7);
        }

        if (str_starts_with($normalizedPath, 'storage/') || str_starts_with($normalizedPath, 'uploads/')) {
            if (is_file(public_path($normalizedPath))) {
                return asset($normalizedPath);
            }

            if (str_starts_with($normalizedPath, 'uploads/')) {
                $storagePath = 'storage/' . substr($normalizedPath, 8);
                if (is_file(public_path($storagePath))) {
                    return asset($storagePath);
                }
            }

            if (str_starts_with($normalizedPath, 'storage/')) {
                $uploadsPath = 'uploads/' . substr($normalizedPath, 8);
                if (is_file(public_path($uploadsPath))) {
                    return asset($uploadsPath);
                }
            }

            return asset($normalizedPath);
        }

        $uploadsPath = 'uploads/' . $normalizedPath;
        if (is_file(public_path($uploadsPath))) {
            return asset($uploadsPath);
        }

        $storagePath = 'storage/' . $normalizedPath;
        if (is_file(public_path($storagePath))) {
            return asset($storagePath);
        }

        return asset('uploads/' . $normalizedPath);
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id', 'member_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id', 'member_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'member_roles', 'member_id', 'role', 'id', 'name')
                    ->withTimestamps();
    }

    public function hasRole($role)
    {
        return DB::table('member_roles')
            ->where('member_id', $this->id)
            ->where('role', $role)
            ->exists();
    }

    public function assignRole($role)
    {
        if (!$this->hasRole($role)) {
            DB::table('member_roles')->insert([
                'member_id' => $this->id,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function removeRole($role)
    {
        DB::table('member_roles')
            ->where('member_id', $this->id)
            ->where('role', $role)
            ->delete();
    }

    public function syncRoles(array $roles)
    {
        DB::table('member_roles')->where('member_id', $this->id)->delete();
        foreach ($roles as $role) {
            $this->assignRole($role);
        }
    }

    public function getRolesListAttribute()
    {
        return DB::table('member_roles')
            ->where('member_id', $this->id)
            ->pluck('role')
            ->toArray();
    }

    public function syncProfilePictureToUser($picturePath)
    {
        if ($this->user) {
            $this->user->update(['profile_picture' => $picturePath]);
        }
    }

    public function syncDataToUser()
    {
        if ($this->user) {
            $this->user->update([
                'email' => $this->email,
                'profile_picture' => $this->profile_picture,
                'location' => $this->location,
                'phone' => $this->contact,
            ]);
        }
    }
}
