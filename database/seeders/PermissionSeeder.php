<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'view_members', 'category' => 'members'],
            ['name' => 'create_members', 'category' => 'members'],
            ['name' => 'edit_members', 'category' => 'members'],
            ['name' => 'delete_members', 'category' => 'members'],
            ['name' => 'view_loans', 'category' => 'financial'],
            ['name' => 'create_loans', 'category' => 'financial'],
            ['name' => 'approve_loans', 'category' => 'financial'],
            ['name' => 'delete_loans', 'category' => 'financial'],
            ['name' => 'view_transactions', 'category' => 'financial'],
            ['name' => 'create_transactions', 'category' => 'financial'],
            ['name' => 'edit_transactions', 'category' => 'financial'],
            ['name' => 'delete_transactions', 'category' => 'financial'],
            ['name' => 'view_projects', 'category' => 'projects'],
            ['name' => 'create_projects', 'category' => 'projects'],
            ['name' => 'edit_projects', 'category' => 'projects'],
            ['name' => 'delete_projects', 'category' => 'projects'],
            ['name' => 'view_reports', 'category' => 'reports'],
            ['name' => 'generate_reports', 'category' => 'reports'],
            ['name' => 'export_reports', 'category' => 'reports'],
            ['name' => 'view_settings', 'category' => 'settings'],
            ['name' => 'edit_settings', 'category' => 'settings'],
            ['name' => 'manage_users', 'category' => 'system'],
            ['name' => 'manage_permissions', 'category' => 'system'],
            ['name' => 'view_audit_logs', 'category' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        $rolePermissions = [
            'client' => ['view_loans', 'create_loans', 'view_transactions'],
            'shareholder' => ['view_members', 'view_loans', 'view_transactions', 'view_projects', 'view_reports'],
            'cashier' => ['view_members', 'view_loans', 'create_loans', 'approve_loans', 'view_transactions', 'create_transactions', 'edit_transactions', 'view_reports', 'generate_reports'],
            'td' => ['view_members', 'view_projects', 'create_projects', 'edit_projects', 'view_reports', 'generate_reports'],
            'ceo' => ['view_members', 'view_loans', 'approve_loans', 'view_transactions', 'view_projects', 'create_projects', 'edit_projects', 'view_reports', 'generate_reports', 'export_reports', 'view_settings'],
            'admin' => ['view_members', 'create_members', 'edit_members', 'delete_members', 'view_loans', 'create_loans', 'approve_loans', 'delete_loans', 'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions', 'view_projects', 'create_projects', 'edit_projects', 'delete_projects', 'view_reports', 'generate_reports', 'export_reports', 'view_settings', 'edit_settings', 'manage_users', 'manage_permissions', 'view_audit_logs'],
        ];

        RolePermission::truncate();

        foreach ($rolePermissions as $role => $perms) {
            foreach ($perms as $permName) {
                $permission = Permission::where('name', $permName)->first();
                if ($permission) {
                    RolePermission::create([
                        'role' => $role,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }
    }
}
