<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'profile_picture',
        'phone',
        'location',
        'bio',
        'preferences',
    ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $member = Member::withTrashed()
                ->where('user_id', $user->id)
                ->first();

            if (!$member) {
                return;
            }

            DB::table('loans')->where('member_id', $member->member_id)->delete();
            DB::table('transactions')->where('member_id', $member->member_id)->delete();
            DB::table('savings_history')->where('member_id', $member->member_id)->delete();

            $member->forceDelete();
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canManageLoans(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'treasurer']);
    }

    public function canViewReports(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'treasurer', 'secretary']);
    }

    public function permissions()
    {
        return RolePermission::where('role', $this->role)
            ->with('permission')
            ->get()
            ->pluck('permission.name')
            ->toArray();
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return !empty(array_intersect($permissions, $this->permissions()));
    }

    public function hasAllPermissions(array $permissions): bool
    {
        return empty(array_diff($permissions, $this->permissions()));
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role', 'id', 'name')
                    ->withTimestamps();
    }

    public function hasRole($role)
    {
        return DB::table('user_roles')
            ->where('user_id', $this->id)
            ->where('role', $role)
            ->exists();
    }

    public function assignRole($role)
    {
        if (!$this->hasRole($role)) {
            DB::table('user_roles')->insert([
                'user_id' => $this->id,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function removeRole($role)
    {
        DB::table('user_roles')
            ->where('user_id', $this->id)
            ->where('role', $role)
            ->delete();
    }

    public function syncRoles(array $roles)
    {
        DB::table('user_roles')->where('user_id', $this->id)->delete();
        foreach ($roles as $role) {
            $this->assignRole($role);
        }
    }

    public function getRolesListAttribute()
    {
        return DB::table('user_roles')
            ->where('user_id', $this->id)
            ->pluck('role')
            ->toArray();
    }

    public function getProfilePictureUrlAttribute()
    {
        // Check user's profile picture first
        $userPictureUrl = $this->resolveProfilePictureUrl($this->profile_picture);
        if ($userPictureUrl) {
            return $userPictureUrl;
        }

        // Fall back to member's profile picture only when relation is already eager loaded.
        if ($this->relationLoaded('member') && $this->member) {
            $memberPictureUrl = $this->resolveProfilePictureUrl($this->member->profile_picture);
            if ($memberPictureUrl) {
                return $memberPictureUrl;
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

    public function syncProfilePictureToMember($picturePath)
    {
        if ($this->member) {
            $this->member->update(['profile_picture' => $picturePath]);
        }
    }

    public function syncDataToMember()
    {
        if ($this->member) {
            $this->member->update([
                'email' => $this->email,
                'profile_picture' => $this->profile_picture,
                'location' => $this->location,
                'contact' => $this->phone,
            ]);
        }
    }
}
