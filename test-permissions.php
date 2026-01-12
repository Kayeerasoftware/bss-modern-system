<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;

echo "=== BSS Permissions System Test ===\n\n";

// Test 1: Check permissions table
echo "1. Checking permissions table...\n";
$permissions = Permission::all();
echo "   Total permissions: " . $permissions->count() . "\n";
foreach ($permissions->groupBy('category') as $category => $perms) {
    echo "   - $category: " . $perms->count() . " permissions\n";
}
echo "\n";

// Test 2: Check role permissions
echo "2. Checking role permissions...\n";
$roles = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
foreach ($roles as $role) {
    $count = RolePermission::where('role', $role)->count();
    echo "   - " . ucfirst($role) . ": $count permissions\n";
}
echo "\n";

// Test 3: Test user permissions
echo "3. Testing user permission methods...\n";
$user = User::where('role', 'admin')->first();
if ($user) {
    echo "   Testing with user: {$user->name} (Role: {$user->role})\n";
    $userPerms = $user->permissions();
    echo "   User has " . count($userPerms) . " permissions\n";
    
    // Test specific permissions
    $testPerms = ['view_members', 'create_members', 'delete_members'];
    foreach ($testPerms as $perm) {
        $has = $user->hasPermission($perm);
        echo "   - hasPermission('$perm'): " . ($has ? 'YES' : 'NO') . "\n";
    }
    
    // Test any permission
    $hasAny = $user->hasAnyPermission(['view_members', 'fake_permission']);
    echo "   - hasAnyPermission(['view_members', 'fake_permission']): " . ($hasAny ? 'YES' : 'NO') . "\n";
    
    // Test all permissions
    $hasAll = $user->hasAllPermissions(['view_members', 'create_members']);
    echo "   - hasAllPermissions(['view_members', 'create_members']): " . ($hasAll ? 'YES' : 'NO') . "\n";
} else {
    echo "   No admin user found\n";
}
echo "\n";

// Test 4: Test client permissions
echo "4. Testing client role permissions...\n";
$clientUser = User::where('role', 'member')->first();
if ($clientUser) {
    echo "   Testing with user: {$clientUser->name} (Role: {$clientUser->role})\n";
    $clientPerms = $clientUser->permissions();
    echo "   User has " . count($clientPerms) . " permissions\n";
    echo "   Permissions: " . implode(', ', $clientPerms) . "\n";
} else {
    echo "   No member user found\n";
}
echo "\n";

echo "=== Test Complete ===\n";
