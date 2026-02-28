<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return false;
        }

        // Backward compatibility: many legacy records store only users.role.
        if (strtolower((string) $this->role) === $normalizedRole) {
            return true;
        }

        return DB::table('user_roles')
            ->where('user_id', $this->id)
            ->whereRaw('LOWER(TRIM(role)) = ?', [$normalizedRole])
            ->exists()
            || DB::table('member_roles')
                ->join('members', 'members.id', '=', 'member_roles.member_id')
                ->where('members.user_id', $this->id)
                ->whereRaw('LOWER(TRIM(member_roles.role)) = ?', [$normalizedRole])
                ->exists();
    }

    public function assignRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return;
        }

        $alreadyAssignedToUser = DB::table('user_roles')
            ->where('user_id', $this->id)
            ->whereRaw('LOWER(TRIM(role)) = ?', [$normalizedRole])
            ->exists();

        if (!$alreadyAssignedToUser) {
            DB::table('user_roles')->insert([
                'user_id' => $this->id,
                'role' => $normalizedRole,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function removeRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return;
        }

        DB::table('user_roles')
            ->where('user_id', $this->id)
            ->whereRaw('LOWER(TRIM(role)) = ?', [$normalizedRole])
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
        $userRoles = DB::table('user_roles')
            ->where('user_id', $this->id)
            ->selectRaw('LOWER(TRIM(role)) as role')
            ->pluck('role')
            ->toArray();

        $memberRoles = DB::table('member_roles')
            ->join('members', 'members.id', '=', 'member_roles.member_id')
            ->where('members.user_id', $this->id)
            ->selectRaw('LOWER(TRIM(member_roles.role)) as role')
            ->pluck('role')
            ->toArray();

        $roles = array_merge($userRoles, $memberRoles);

        if (!empty($this->role)) {
            $roles[] = strtolower((string) $this->role);
        }

        return array_values(array_unique(array_map(fn ($role) => strtolower((string) $role), $roles)));
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

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            return $normalizedPath;
        }

        if (str_starts_with($normalizedPath, 'public/')) {
            $normalizedPath = substr($normalizedPath, 7);
        }

        $trimmedPath = preg_replace('#^(storage|uploads)/#', '', $normalizedPath);
        $storageCandidates = [
            $normalizedPath,
            $trimmedPath,
        ];

        if (str_starts_with($trimmedPath, 'profile_pictures/')) {
            $storageCandidates[] = 'profile-pictures/' . substr($trimmedPath, strlen('profile_pictures/'));
        } elseif (str_starts_with($trimmedPath, 'profile-pictures/')) {
            $storageCandidates[] = 'profile_pictures/' . substr($trimmedPath, strlen('profile-pictures/'));
        }

        foreach (array_unique($storageCandidates) as $candidate) {
            if (!$candidate || str_starts_with($candidate, 'uploads/')) {
                continue;
            }
            if (Storage::disk('public')->exists($candidate)) {
                return Storage::disk('public')->url($candidate);
            }
        }

        $candidates = [
            $normalizedPath,
            'uploads/' . $trimmedPath,
            'storage/' . $trimmedPath,
        ];

        // Support both underscore and hyphen naming used by legacy + new uploads.
        if (str_starts_with($trimmedPath, 'profile_pictures/')) {
            $hyphenPath = 'profile-pictures/' . substr($trimmedPath, strlen('profile_pictures/'));
            $candidates[] = 'uploads/' . $hyphenPath;
            $candidates[] = 'storage/' . $hyphenPath;
        } elseif (str_starts_with($trimmedPath, 'profile-pictures/')) {
            $underscorePath = 'profile_pictures/' . substr($trimmedPath, strlen('profile-pictures/'));
            $candidates[] = 'uploads/' . $underscorePath;
            $candidates[] = 'storage/' . $underscorePath;
        }

        foreach (array_unique($candidates) as $candidate) {
            if (is_file(public_path($candidate))) {
                return asset($candidate);
            }

            // File may exist on the public disk even if symlink is missing in dev.
            if (str_starts_with($candidate, 'storage/')) {
                $diskPath = storage_path('app/public/' . substr($candidate, strlen('storage/')));
                if (is_file($diskPath)) {
                    return asset($candidate);
                }
            }
        }

        return null;
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
