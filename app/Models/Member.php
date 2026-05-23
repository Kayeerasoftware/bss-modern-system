<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\System\AccountNumberService;
use App\Services\Financial\TransactionPostingService;
use App\Models\SavingsHistory;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    private static array $userPictureCache = [];
    private static array $transactionSavingsCache = [];
    private static array $pendingOpeningSavings = [];

    private const TRANSACTION_SAVINGS_CATEGORIES = [
        'savings_deposit',
        'savings_withdrawal',
        'transfer_in',
        'transfer_out',
        'fundraising_transfer',
        'loan_disbursement',
    ];

    protected static function booted()
    {
        static::creating(function ($member) {
            if (empty($member->member_account_number)) {
                $member->member_account_number = AccountNumberService::generateMemberAccountNumber();
            }
        });

        static::created(function (self $member): void {
            $openingSavings = self::pullQueuedOpeningSavings($member);

            if (DB::table('transactions')->where('member_id', $member->id)->exists()) {
                self::syncLatestSavingsTransactionPointer($member);
                return;
            }

            app(TransactionPostingService::class)->createOpeningSavingsTransaction($member, $openingSavings, [
                'description' => 'Opening savings balance',
                'notes' => 'Automatically created from the member opening balance.',
            ]);

            self::syncLatestSavingsTransactionPointer($member);
        });
    }

    protected $fillable = [
        'user_id',
        'member_number',
        'member_account_number',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'primary_phone',
        'primary_phone_country_id',
        'alternative_phone',
        'alternative_phone_country_id',
        'whatsapp_phone',
        'email',
        'alternative_email',
        'profile_picture',
        'date_of_birth',
        'gender_id',
        'nationality_id',
        'place_of_birth',
        'occupation',
        'employer',
        'employment_status_id',
        'membership_status',
        'savings_transaction_id',
        'status_reason',
        'join_date',
        'exit_date',
        'exit_reason',
        'referred_by',
        'referral_code',
        'preferred_language',
        'notification_preferences',
        'communication_preferences',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'notes',
        'tags',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_reason',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'exit_date' => 'date',
        'notification_preferences' => 'array',
        'communication_preferences' => 'array',
        'tags' => 'array',
    ];

    protected $appends = ['full_name'];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'member_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'member_id', 'id');
    }

    public function calculateInterest($amount, $months, $rate = null)
    {
        $rate = $rate !== null ? (float) $rate : (float) setting('default_interest_rate', 10);
        return round($amount * ($rate / 100) * ($months / 12), 2);
    }

    public function calculateMonthlyPayment($amount, $interest, $months)
    {
        return round(($amount + $interest) / $months);
    }

    public function shares()
    {
        return $this->hasMany(Share::class, 'member_id', 'id');
    }

    public function savingsTransaction()
    {
        return $this->belongsTo(SavingsHistory::class, 'savings_transaction_id');
    }

    public function dividends()
    {
        return $this->hasMany(MemberDividend::class, 'member_id', 'id');
    }

    public function bioData()
    {
        return $this->hasOne(BioData::class, 'member_id', 'id');
    }

    public function getTotalSharesAttribute()
    {
        return $this->shares()->sum('shares_count');
    }

    public function getTotalShareValueAttribute()
    {
        return $this->shares()->sum('total_value');
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

        return asset('images/default-avatar.svg');
    }

    protected function resolveProfilePictureUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Cache the result to avoid repeated file checks
        static $cache = [];
        if (isset($cache[$path])) {
            return $cache[$path];
        }

        $normalizedPath = ltrim($path, '/');

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            return $cache[$path] = $normalizedPath;
        }

        if (str_starts_with($normalizedPath, 'public/')) {
            $normalizedPath = substr($normalizedPath, 7);
        }

        $trimmedPath = preg_replace('#^(storage|uploads)/#', '', $normalizedPath);
        
        // Quick check: most common path first
        $quickPath = 'uploads/' . $trimmedPath;
        if (is_file(public_path($quickPath))) {
            return $cache[$path] = asset($quickPath);
        }

        // Fallback to comprehensive check only if quick check fails
        $candidates = [
            'uploads/' . $trimmedPath,
            'storage/' . $trimmedPath,
        ];

        foreach ($candidates as $candidate) {
            if (is_file(public_path($candidate))) {
                return $cache[$path] = asset($candidate);
            }
        }

        return $cache[$path] = null;
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id', 'id');
    }

    public function receivedMessages()
    {
        return $this->belongsToMany(ChatMessage::class, 'chat_message_receipts', 'member_id', 'message_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'member_roles', 'member_id', 'role_id')
            ->withPivot(['is_primary', 'assigned_by', 'assigned_at', 'expires_at', 'revoked_by', 'revoked_at', 'revoked_reason'])
            ->withTimestamps();
    }

    public function hasRole($role)
    {
        $normalizedRole = strtolower(trim((string) $role));
        if ($normalizedRole === '') {
            return false;
        }

        return DB::table('member_roles')
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->where('member_roles.member_id', $this->id)
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

        $exists = DB::table('member_roles')
            ->where('member_id', $this->id)
            ->where('role_id', $roleId)
            ->exists();

        if (!$exists) {
            DB::table('member_roles')->insert([
                'member_id' => $this->id,
                'role_id' => $roleId,
                'is_primary' => 1,
                'assigned_by' => $this->user_id ?? 1,
                'assigned_at' => now(),
            ]);
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

        DB::table('member_roles')
            ->where('member_id', $this->id)
            ->where('role_id', $roleId)
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
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->where('member_roles.member_id', $this->id)
            ->pluck('roles.name')
            ->map(fn ($role) => strtolower((string) $role))
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
            ]);
        }
    }

    public function getMemberIdAttribute(): ?string
    {
        return $this->member_account_number ?? $this->member_number;
    }

    public function getMemberNumberAttribute($value): ?string
    {
        return $this->member_account_number ?? $value;
    }

    public function setMemberIdAttribute($value): void
    {
        $this->attributes['member_number'] = $value;
    }

    public function getContactAttribute(): ?string
    {
        return $this->primary_phone;
    }

    public function setContactAttribute($value): void
    {
        $this->attributes['primary_phone'] = $value;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->membership_status;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['membership_status'] = $value;
    }

    public function getRoleAttribute(): ?string
    {
        $role = DB::table('member_roles')
            ->join('roles', 'roles.id', '=', 'member_roles.role_id')
            ->where('member_roles.member_id', $this->id)
            ->orderByDesc('member_roles.is_primary')
            ->orderBy('member_roles.assigned_at')
            ->value('roles.name');

        return $role ?: $this->user?->role;
    }

    public function setRoleAttribute($value): void
    {
        $this->assignRole($value);
    }

    public function getSavingsBalanceAttribute(): float
    {
        return (float) DB::table('savings_accounts')
            ->where('member_id', $this->id)
            ->sum('current_balance');
    }

    public function getSavingsAttribute($value): float
    {
        if (array_key_exists('calculated_savings', $this->attributes)) {
            return round((float) ($this->attributes['calculated_savings'] ?? 0), 2);
        }

        return $this->calculateTransactionSavings();
    }

    public function getBalanceAttribute(): float
    {
        return $this->getSavingsBalanceAttribute();
    }

    public function getLoanAttribute(): float
    {
        return (float) DB::table('loans')
            ->where('member_id', $this->id)
            ->sum('balance_due');
    }

    public function getFullNameAttribute($value = null): string
    {
        $fullName = trim((string) ($value ?? $this->getRawOriginal('full_name') ?? ''));

        if ($fullName !== '') {
            return $fullName;
        }

        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts) ?: 'Unknown';
    }

    public function setFullNameAttribute($value): void
    {
        $name = trim((string) $value);
        if ($name === '') {
            return;
        }

        $parts = preg_split('/\s+/', $name);
        $first = array_shift($parts);
        $last = array_pop($parts);

        $this->attributes['first_name'] = $first;
        $this->attributes['last_name'] = $last ?: $first;
        if (!empty($parts)) {
            $this->attributes['middle_name'] = implode(' ', $parts);
        }
    }

    public static function queueOpeningSavings(self $member, float $amount): void
    {
        self::$pendingOpeningSavings[spl_object_id($member)] = max($amount, 0);
    }

    private static function pullQueuedOpeningSavings(self $member): float
    {
        $key = spl_object_id($member);
        $amount = (float) (self::$pendingOpeningSavings[$key] ?? 0);
        unset(self::$pendingOpeningSavings[$key]);

        return max($amount, 0);
    }

    private static function syncLatestSavingsTransactionPointer(self $member): void
    {
        if (!Schema::hasColumn('members', 'savings_transaction_id')) {
            return;
        }

        $latestSavingsTransactionId = SavingsHistory::query()
            ->forMember($member->id)
            ->latest('id')
            ->value('id');

        if (!$latestSavingsTransactionId) {
            return;
        }

        DB::table('members')
            ->where('id', $member->id)
            ->update(['savings_transaction_id' => $latestSavingsTransactionId]);
    }

    public function scopeWithTransactionSavings(Builder $query): Builder
    {
        $savingsSubquery = static::transactionSavingsSubquery();

        return $query
            ->leftJoinSub($savingsSubquery, 'member_transaction_savings', 'members.id', '=', 'member_transaction_savings.member_id')
            ->select('members.*')
            ->addSelect(DB::raw('COALESCE(member_transaction_savings.transaction_savings, 0) as calculated_savings'));
    }

    public static function transactionSavingsSubquery()
    {
        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';

        return DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', self::TRANSACTION_SAVINGS_CATEGORIES)
            ->whereNull('transactions.deleted_at')
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as transaction_savings")
            ->groupBy('transactions.member_id');
    }

    public function calculateTransactionSavings(): float
    {
        return self::transactionSavingsForMemberId((int) $this->id);
    }

    public static function transactionSavingsForMemberId(int $memberId): float
    {
        if ($memberId <= 0) {
            return 0.0;
        }

        if (array_key_exists($memberId, self::$transactionSavingsCache)) {
            return self::$transactionSavingsCache[$memberId];
        }

        $savings = (float) DB::query()
            ->fromSub(static::transactionSavingsSubquery(), 'member_transaction_savings')
            ->where('member_id', $memberId)
            ->value('transaction_savings') ?? 0;

        return self::$transactionSavingsCache[$memberId] = round($savings, 2);
    }

    public static function transactionSavingsTotal(): float
    {
        if (array_key_exists('total', self::$transactionSavingsCache)) {
            return self::$transactionSavingsCache['total'];
        }

        $total = (float) DB::query()
            ->fromSub(static::transactionSavingsSubquery(), 'member_transaction_savings')
            ->sum('transaction_savings');

        return self::$transactionSavingsCache['total'] = round($total, 2);
    }
}
