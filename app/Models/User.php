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

    protected $table = 'users';

    protected $appends = [
        'name',
        'role',
        'is_active',
        'roles_list',
    ];

    private array $pendingMemberAttributes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'role_id',
        'status',
        'status_reason',
        'profile_picture',
        'email_verified_at',
        'email_verification_token',
        'password_reset_token',
        'password_reset_expires',
        'two_factor_secret',
        'two_factor_enabled',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'login_count',
        'failed_login_attempts',
        'last_failed_login',
        'locked_until',
        'remember_token',
        'api_token',
        'api_token_expires',
        'created_by',
    ];

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
            'two_factor_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'password_reset_expires' => 'datetime',
            'api_token_expires' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            if (!$user->pendingMemberAttributes) {
                return;
            }

            $member = $user->member;
            if ($member) {
                $member->fill($user->pendingMemberAttributes);
                if ($member->isDirty()) {
                    $member->saveQuietly();
                }
            }

            $user->pendingMemberAttributes = [];
        });
    }

    private function resolveUniqueUsername(?string $value, ?int $ignoreUserId = null): string
    {
        $base = trim(preg_replace('/\s+/', ' ', (string) $value));
        if ($base === '') {
            $base = 'User';
        }

        $base = mb_substr($base, 0, 100);
        $candidate = $base;
        $suffix = 2;

        while ($this->usernameExists($candidate, $ignoreUserId)) {
            $suffixText = ' ' . $suffix;
            $maxBaseLength = max(1, 100 - mb_strlen($suffixText));
            $candidate = rtrim(mb_substr($base, 0, $maxBaseLength)) . $suffixText;
            $suffix++;
        }

        return $candidate;
    }

    private function usernameExists(string $username, ?int $ignoreUserId = null): bool
    {
        return self::query()
            ->when($ignoreUserId, function ($query) use ($ignoreUserId): void {
                $query->where('id', '!=', $ignoreUserId);
            })
            ->whereRaw('LOWER(TRIM(username)) = ?', [mb_strtolower(trim($username))])
            ->exists();
    }

    public function roleRecord()
    {
        return $this->belongsTo(Role::class, 'role_id');
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
        $roleIds = $this->collectRoleIds();
        if (empty($roleIds)) {
            return [];
        }

        return RolePermission::query()
            ->whereIn('role_id', $roleIds)
            ->with('permission')
            ->get()
            ->pluck('permission.name')
            ->unique()
            ->values()
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

    public function hasRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return false;
        }

        if (strtolower((string) $this->role) === $normalizedRole) {
            return true;
        }

        return DB::table('member_roles')
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->join('members', 'members.id', '=', 'member_roles.member_id')
            ->where('members.user_id', $this->id)
            ->whereRaw('LOWER(TRIM(roles.name)) = ?', [$normalizedRole])
            ->exists();
    }

    public function assignRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return;
        }

        $roleId = Role::query()->where('name', $normalizedRole)->value('id');
        if (!$roleId) {
            return;
        }

        $this->role_id = $roleId;
        if ($this->isDirty('role_id')) {
            $this->saveQuietly();
        }

        $member = $this->member;
        if ($member) {
            $exists = DB::table('member_roles')
                ->where('member_id', $member->id)
                ->where('role_id', $roleId)
                ->exists();

            if (!$exists) {
                DB::table('member_roles')->insert([
                    'member_id' => $member->id,
                    'role_id' => $roleId,
                    'is_primary' => 1,
                    'assigned_by' => $this->id,
                    'assigned_at' => now(),
                ]);
            }
        }
    }

    public function removeRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return;
        }

        $roleId = Role::query()->where('name', $normalizedRole)->value('id');
        if (!$roleId) {
            return;
        }

        $member = $this->member;
        if ($member) {
            DB::table('member_roles')
                ->where('member_id', $member->id)
                ->where('role_id', $roleId)
                ->delete();
        }
    }

    public function syncRoles(array $roles)
    {
        $member = $this->member;
        if ($member) {
            DB::table('member_roles')->where('member_id', $member->id)->delete();
        }
        foreach ($roles as $role) {
            $this->assignRole($role);
        }
    }

    public function getRolesListAttribute()
    {
        $memberRoles = DB::table('member_roles')
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->join('members', 'members.id', '=', 'member_roles.member_id')
            ->where('members.user_id', $this->id)
            ->selectRaw('LOWER(TRIM(roles.name)) as role')
            ->pluck('role')
            ->toArray();

        $roles = $memberRoles;

        if (!empty($this->role)) {
            $roles[] = strtolower((string) $this->role);
        }

        return array_values(array_unique(array_map(fn ($role) => strtolower((string) $role), $roles)));
    }

    public function getRoleAttribute(): ?string
    {
        if ($this->relationLoaded('roleRecord')) {
            return $this->roleRecord?->name;
        }

        $roleId = $this->attributes['role_id'] ?? null;
        if ($roleId) {
            return Role::query()->whereKey($roleId)->value('name');
        }

        return null;
    }

    public function setRoleAttribute($value): void
    {
        $normalizedRole = strtolower(trim((string) $value));
        if ($normalizedRole === '') {
            return;
        }

        $roleId = Role::query()->where('name', $normalizedRole)->value('id');
        if ($roleId) {
            $this->attributes['role_id'] = $roleId;
        }
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['username'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->setUsernameAttribute($value);
    }

    public function setUsernameAttribute($value): void
    {
        $this->attributes['username'] = $this->resolveUniqueUsername($value, $this->getKey());
    }

    public function getIsActiveAttribute(): bool
    {
        return ($this->attributes['status'] ?? 'active') === 'active';
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'active' : 'inactive';
    }

    public function getProfilePictureAttribute(): ?string
    {
        if (array_key_exists('profile_picture', $this->attributes)) {
            return $this->attributes['profile_picture'];
        }

        if ($this->relationLoaded('member') && $this->member) {
            return $this->member->profile_picture;
        }

        return null;
    }

    public function setProfilePictureAttribute($value): void
    {
        $this->attributes['profile_picture'] = $value;
        $this->syncMemberAttribute('profile_picture', $value);
    }

    public function getPhoneAttribute(): ?string
    {
        if (!$this->relationLoaded('member')) {
            return null; // Don't trigger query if member not loaded
        }
        $phone = $this->member?->primary_phone;
        return is_string($phone) ? $phone : null;
    }

    public function setPhoneAttribute($value): void
    {
        $this->syncMemberAttribute('primary_phone', $value);
    }

    public function getLocationAttribute(): ?string
    {
        if (!$this->relationLoaded('member')) {
            return null; // Don't trigger query if member not loaded
        }
        $location = $this->member?->place_of_birth;
        return is_string($location) ? $location : null;
    }

    public function setLocationAttribute($value): void
    {
        $this->syncMemberAttribute('place_of_birth', $value);
    }

    public function getBioAttribute(): ?string
    {
        if (!$this->relationLoaded('member')) {
            return null; // Don't trigger query if member not loaded
        }
        $bio = $this->member?->notes;
        return is_string($bio) ? $bio : null;
    }

    public function setBioAttribute($value): void
    {
        $this->syncMemberAttribute('notes', $value);
    }

    public function getPreferencesAttribute(): ?array
    {
        $prefs = $this->member?->communication_preferences;
        return is_array($prefs) ? $prefs : null;
    }

    public function setPreferencesAttribute($value): void
    {
        $this->syncMemberAttribute('communication_preferences', $value);
    }

    private function syncMemberAttribute(string $key, $value): void
    {
        if ($this->relationLoaded('member') && $this->member) {
            $this->member->setAttribute($key, $value);
            return;
        }

        if ($this->exists) {
            $member = $this->member;
            if ($member) {
                $member->setAttribute($key, $value);
                return;
            }
        }

        $this->pendingMemberAttributes[$key] = $value;
    }

    private function collectRoleIds(): array
    {
        $roleIds = [];
        if (!empty($this->role_id)) {
            $roleIds[] = (int) $this->role_id;
        }

        $memberRoleIds = DB::table('member_roles')
            ->join('members', 'members.id', '=', 'member_roles.member_id')
            ->where('members.user_id', $this->id)
            ->pluck('member_roles.role_id')
            ->toArray();

        foreach ($memberRoleIds as $id) {
            $roleIds[] = (int) $id;
        }

        return array_values(array_unique($roleIds));
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

        static $cache = [];
        if (isset($cache[$path])) {
            return $cache[$path];
        }

        // Full URL (Cloudinary or R2) — return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $cache[$path] = $path;
        }

        // Relative path — build R2 URL
        $s3Url = rtrim((string) env('AWS_URL', ''), '/');
        if ($s3Url !== '') {
            return $cache[$path] = $s3Url . '/' . ltrim($path, '/');
        }

        return $cache[$path] = null;
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
