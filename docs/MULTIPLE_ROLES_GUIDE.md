# Multiple Roles Implementation Guide

## Overview
This guide explains how to migrate from single role to multiple roles per member.

## Changes Made

### 1. Database Migration
- Created `member_roles` pivot table to store multiple roles per member
- File: `database/migrations/2024_03_15_000001_create_member_roles_table.php`

### 2. Model Updates
- Added `roles()` relationship method
- Added `hasRole($role)` - Check if member has a specific role
- Added `assignRole($role)` - Assign a role to member
- Added `removeRole($role)` - Remove a role from member
- Added `syncRoles(array $roles)` - Sync all roles at once
- Added `getRolesListAttribute()` - Get array of member's roles

### 3. View Updates
- Changed from single dropdown to multiple checkboxes
- File: `resources/views/admin/members/edit.blade.php`

### 4. Controller Updates
- Modified `update()` method to handle multiple roles
- File: `app/Http/Controllers/Admin/MemberController.php`

### 5. Validation Updates
- Changed validation from single `role` to `roles` array
- File: `app/Http/Requests/UpdateMemberRequest.php`

## Migration Steps

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Migrate Existing Data (Optional)
If you have existing members with single roles, run this to migrate them:

```php
// Run in tinker: php artisan tinker
DB::table('members')->get()->each(function($member) {
    DB::table('member_roles')->insert([
        'member_id' => $member->id,
        'role' => $member->role,
        'created_at' => now(),
        'updated_at' => now()
    ]);
});
```

### Step 3: Update Member Creation
You'll also need to update the `store()` method in MemberController to handle multiple roles during member creation.

## Usage Examples

### Check if member has a role
```php
if ($member->hasRole('admin')) {
    // Do something
}
```

### Assign a role
```php
$member->assignRole('cashier');
```

### Remove a role
```php
$member->removeRole('client');
```

### Sync multiple roles
```php
$member->syncRoles(['client', 'shareholder']);
```

### Get all member roles
```php
$roles = $member->roles_list; // Returns array
```

## Notes
- The old `role` column in the `members` table is still present for backward compatibility
- You can keep it as the "primary role" or remove it in a future migration
- Consider updating the authentication logic to handle multiple roles
- Update any role-based access control (gates/policies) to check the pivot table
