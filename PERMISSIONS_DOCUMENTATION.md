# BSS Investment Group - Permissions System

## Overview
The BSS system now has a comprehensive role-based permissions system that allows fine-grained access control.

## Database Structure

### Tables Created
1. **permissions** - Stores all available permissions
   - id, name, category, timestamps

2. **role_permissions** - Maps permissions to roles
   - id, role, permission_id, timestamps

## Available Permissions

### Members Category (4 permissions)
- `view_members` - View member list and details
- `create_members` - Add new members
- `edit_members` - Modify member information
- `delete_members` - Remove members

### Financial Category (8 permissions)
- `view_loans` - View loan applications
- `create_loans` - Create loan applications
- `approve_loans` - Approve loan applications
- `delete_loans` - Delete loan records
- `view_transactions` - View transaction history
- `create_transactions` - Create new transactions
- `edit_transactions` - Modify transactions
- `delete_transactions` - Delete transactions

### Projects Category (4 permissions)
- `view_projects` - View project information
- `create_projects` - Create new projects
- `edit_projects` - Modify project details
- `delete_projects` - Remove projects

### Reports Category (3 permissions)
- `view_reports` - Access system reports
- `generate_reports` - Generate new reports
- `export_reports` - Export reports to file

### Settings Category (2 permissions)
- `view_settings` - View system settings
- `edit_settings` - Modify system settings

### System Category (3 permissions)
- `manage_users` - Manage user accounts
- `manage_permissions` - Manage role permissions
- `view_audit_logs` - View system audit logs

## Default Role Permissions

### Client (3 permissions)
- view_loans, create_loans, view_transactions

### Shareholder (5 permissions)
- view_members, view_loans, view_transactions, view_projects, view_reports

### Cashier (9 permissions)
- view_members, view_loans, create_loans, approve_loans, view_transactions, create_transactions, edit_transactions, view_reports, generate_reports

### TD (6 permissions)
- view_members, view_projects, create_projects, edit_projects, view_reports, generate_reports

### CEO (11 permissions)
- view_members, view_loans, approve_loans, view_transactions, view_projects, create_projects, edit_projects, view_reports, generate_reports, export_reports, view_settings

### Admin (24 permissions)
- ALL permissions

## Usage

### In Controllers
```php
// Check single permission
if (auth()->user()->hasPermission('view_members')) {
    // Allow access
}

// Check any permission
if (auth()->user()->hasAnyPermission(['view_members', 'edit_members'])) {
    // Allow if user has any of these
}

// Check all permissions
if (auth()->user()->hasAllPermissions(['view_members', 'edit_members'])) {
    // Allow only if user has all
}
```

### In Routes
```php
// Single permission
Route::get('/members', [MemberController::class, 'index'])
    ->middleware(['auth', 'permission:view_members']);

// Multiple routes with same permission
Route::middleware(['auth', 'permission:view_members'])->group(function () {
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
});
```

### In Blade Templates
```blade
@if(auth()->user()->hasPermission('view_members'))
    <a href="/members">View Members</a>
@endif

@if(auth()->user()->hasPermission('create_members'))
    <button>Add Member</button>
@endif
```

## API Endpoints

### Get All Permissions
```
GET /api/permissions
```

### Get Role Permissions
```
GET /api/permissions/role/{role}
```

### Update Role Permissions
```
POST /api/permissions/role/{role}
Body: {"permissions": ["view_members", "create_members"]}
```

## Admin Dashboard

Navigate to the **Permissions** section in the admin dashboard to:
- View all roles and their permissions
- Edit permissions for each role
- Use quick actions (Select All, Deselect All, Reset to Default)
- See permission counts and categories

## Testing

Run the test script:
```bash
php test-permissions.php
```

## Migration

The permissions system was added via migration:
```bash
php artisan migrate --path=database/migrations/2026_01_12_000001_create_permissions_tables.php
php artisan db:seed --class=PermissionSeeder
```

## Security Notes

1. Permissions are checked at the application level
2. Middleware protects routes from unauthorized access
3. Admin role has all permissions by default
4. Permissions can be modified through the admin dashboard
5. Changes take effect immediately without requiring logout

## Future Enhancements

Potential additions:
- User-specific permissions (override role permissions)
- Permission groups/bundles
- Time-based permissions
- IP-based restrictions
- Audit logging for permission changes
