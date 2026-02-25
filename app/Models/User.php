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
        if ($this->profile_picture) {
            // Handle both storage/ and uploads/ paths
            if (str_starts_with($this->profile_picture, 'storage/') || str_starts_with($this->profile_picture, 'uploads/')) {
                return asset($this->profile_picture);
            }
            return asset('storage/' . $this->profile_picture);
        }
        // Fall back to member's profile picture if user is linked to a member
        if ($this->member && $this->member->profile_picture) {
            if (str_starts_with($this->member->profile_picture, 'storage/') || str_starts_with($this->member->profile_picture, 'uploads/')) {
                return asset($this->member->profile_picture);
            }
            return asset('storage/' . $this->member->profile_picture);
        }
        return asset('images/default-avatar.svg');
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
