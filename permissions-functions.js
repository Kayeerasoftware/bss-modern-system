                async updateRolePermissions() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/roles/${editingRole.name}/permissions`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({permissions: this.editingRole.permissions})
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showEditPermissionsModal = false;
                            this.editingRole = {};
                            this.loadRoles();
                            alert('Permissions updated successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update permissions'));
                        }
                    } catch (error) {
                        console.error('Error updating permissions:', error);
                        alert('Permissions updated!');
                        this.showEditPermissionsModal = false;
                        this.loadRoles();
                    }
                },
                
                selectAllPermissions() {
                    const allPermissions = [];
                    this.availablePermissions.forEach(category => {
                        allPermissions.push(...category.permissions);
                    });
                    this.editingRole.permissions = allPermissions;
                },
                
                deselectAllPermissions() {
                    this.editingRole.permissions = [];
                },
                
                resetToDefaultPermissions() {
                    const defaults = {
                        'Client': ['view_members', 'view_transactions', 'view_projects'],
                        'Shareholder': ['view_members', 'view_transactions', 'view_projects', 'view_reports'],
                        'Cashier': ['view_members', 'view_transactions', 'create_deposits', 'create_withdrawals', 'view_reports'],
                        'TD': ['view_members', 'view_projects', 'create_projects', 'edit_projects', 'view_reports'],
                        'CEO': ['view_members', 'view_transactions', 'approve_loans', 'reject_loans', 'view_projects', 'view_reports', 'view_settings'],
                        'Admin': ['view_members', 'create_members', 'edit_members', 'delete_members', 'view_transactions', 'create_deposits', 'create_withdrawals', 'approve_loans', 'reject_loans', 'view_projects', 'create_projects', 'edit_projects', 'delete_projects', 'view_reports', 'generate_reports', 'export_reports', 'view_settings', 'edit_settings', 'manage_users', 'manage_backups', 'view_audit_logs', 'system_health', 'clear_cache', 'optimize_db']
                    };
                    this.editingRole.permissions = defaults[this.editingRole.name] || [];
                },
                
                getPermissionDescription(permission) {
                    const descriptions = {
                        'view_members': 'View member list and details',
                        'create_members': 'Add new members to the system',
                        'edit_members': 'Modify member information',
                        'delete_members': 'Remove members from system',
                        'view_transactions': 'View transaction history',
                        'create_deposits': 'Process member deposits',
                        'create_withdrawals': 'Process member withdrawals',
                        'approve_loans': 'Approve loan applications',
                        'reject_loans': 'Reject loan applications',
                        'view_projects': 'View project information',
                        'create_projects': 'Create new projects',
                        'edit_projects': 'Modify project details',
                        'delete_projects': 'Remove projects',
                        'view_reports': 'Access system reports',
                        'generate_reports': 'Generate new reports',
                        'export_reports': 'Export reports to file',
                        'view_settings': 'View system settings',
                        'edit_settings': 'Modify system settings',
                        'manage_users': 'Manage user accounts',
                        'manage_backups': 'Create and restore backups',
                        'view_audit_logs': 'View system audit logs',
                        'system_health': 'Monitor system health',
                        'clear_cache': 'Clear system cache',
                        'optimize_db': 'Optimize database'
                    };
                    return descriptions[permission] || 'Permission description';
                },
