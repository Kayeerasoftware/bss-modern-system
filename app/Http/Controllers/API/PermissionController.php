<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function getRolePermissions($role)
    {
        $permissions = RolePermission::where('role', $role)
            ->with('permission')
            ->get()
            ->pluck('permission.name');

        return response()->json(['permissions' => $permissions]);
    }

    public function updateRolePermissions(Request $request, $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        RolePermission::where('role', $role)->delete();

        foreach ($request->permissions as $permName) {
            $permission = Permission::where('name', $permName)->first();
            if ($permission) {
                RolePermission::create([
                    'role' => $role,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        return response()->json(['message' => 'Permissions updated successfully']);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all()->groupBy('category');
        return response()->json(['permissions' => $permissions]);
    }
}
