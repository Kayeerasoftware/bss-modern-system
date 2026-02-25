@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 p-3 md:p-6" x-data="permissionsManager()">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-lock text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent mb-1 md:mb-2">Permissions</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Manage user roles and permissions</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Permissions...</span>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <template x-for="role in roles" :key="role.name">
                <div class="border-2 rounded-xl p-6 hover:shadow-lg transition" :class="role.colorClass">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3" :class="role.iconBg">
                                <i class="fas text-xl" :class="role.icon"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg" x-text="role.name"></h4>
                                <p class="text-xs text-gray-600" x-text="role.description"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Permissions (<span x-text="role.permissions.length"></span>)</p>
                        <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto">
                            <template x-for="(perm, idx) in role.permissions.slice(0, 6)" :key="idx">
                                <span class="px-2 py-1 bg-white rounded text-xs border" x-text="perm"></span>
                            </template>
                            <span x-show="role.permissions.length > 6" class="px-2 py-1 bg-gray-200 rounded text-xs">+<span x-text="role.permissions.length - 6"></span> more</span>
                        </div>
                    </div>
                    <button @click="editRole(role)" class="w-full px-4 py-2 bg-white border-2 rounded-lg hover:bg-gray-50 transition font-semibold text-sm" :class="role.btnClass">
                        <i class="fas fa-edit mr-2"></i>Edit Permissions
                    </button>
                </div>
            </template>
        </div>

        <!-- Permission Categories Info -->
        <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Permission Categories
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="font-semibold text-blue-600 mb-2">Member Management</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• View Members</li>
                        <li>• Create Members</li>
                        <li>• Edit Members</li>
                        <li>• Delete Members</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-green-600 mb-2">Financial Operations</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• View Transactions</li>
                        <li>• Process Deposits</li>
                        <li>• Process Withdrawals</li>
                        <li>• Approve Loans</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-purple-600 mb-2">System Administration</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• System Settings</li>
                        <li>• User Management</li>
                        <li>• View Reports</li>
                        <li>• Manage Backups</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Permissions Modal -->
    <div x-show="showEditModal" @click.self="showEditModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[85vh] overflow-y-auto m-4">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Edit Permissions</h3>
                    <p class="text-sm text-gray-600" x-text="'Role: ' + (editingRole.name || '')"></p>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form @submit.prevent="savePermissions()" class="p-6">
                <template x-for="category in availablePermissions" :key="category.category">
                    <div class="mb-6 border rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b">
                            <h4 class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-folder text-blue-600 mr-2"></i>
                                <span x-text="category.category"></span>
                                <span class="ml-auto text-xs text-gray-600" x-text="'(' + category.permissions.length + ')'"></span>
                            </h4>
                        </div>
                        <div class="p-4 bg-white grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="permission in category.permissions" :key="permission">
                                <label class="flex items-start p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition group">
                                    <input type="checkbox" :value="permission" x-model="editingRole.permissions" class="w-5 h-5 text-blue-600 rounded mt-0.5 mr-3">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700" x-text="permission.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="sticky bottom-0 bg-white border-t pt-4 flex justify-between items-center">
                    <div class="flex gap-2">
                        <button type="button" @click="selectAll()" class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                            <i class="fas fa-check-double mr-1"></i>All
                        </button>
                        <button type="button" @click="editingRole.permissions = []" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-times mr-1"></i>None
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <span class="px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm font-medium">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span x-text="(editingRole.permissions?.length || 0) + ' selected'"></span>
                        </span>
                        <button type="button" @click="showEditModal = false" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function permissionsManager() {
    return {
        showEditModal: false,
        editingRole: {},
        roles: [
            {name: 'Client', description: 'Member access', permissions: ['view_profile', 'view_transactions', 'apply_loan', 'view_savings'], colorClass: 'border-blue-200 bg-blue-50', iconBg: 'bg-blue-200', icon: 'fa-user text-blue-600', btnClass: 'border-blue-400 text-blue-600 hover:bg-blue-50'},
            {name: 'Shareholder', description: 'Investment access', permissions: ['view_portfolio', 'view_dividends', 'view_projects', 'view_reports', 'invest'], colorClass: 'border-green-200 bg-green-50', iconBg: 'bg-green-200', icon: 'fa-chart-line text-green-600', btnClass: 'border-green-400 text-green-600 hover:bg-green-50'},
            {name: 'Cashier', description: 'Transaction processing', permissions: ['process_deposits', 'process_withdrawals', 'view_members', 'create_transactions', 'view_loans', 'process_loans'], colorClass: 'border-yellow-200 bg-yellow-50', iconBg: 'bg-yellow-200', icon: 'fa-cash-register text-yellow-600', btnClass: 'border-yellow-400 text-yellow-600 hover:bg-yellow-50'},
            {name: 'TD', description: 'Technical oversight', permissions: ['manage_projects', 'view_reports', 'view_members', 'view_transactions', 'view_loans', 'system_health', 'data_management'], colorClass: 'border-purple-200 bg-purple-50', iconBg: 'bg-purple-200', icon: 'fa-tools text-purple-600', btnClass: 'border-purple-400 text-purple-600 hover:bg-purple-50'},
            {name: 'CEO', description: 'Executive access', permissions: ['view_all_reports', 'approve_loans', 'view_financials', 'view_members', 'view_projects', 'strategic_decisions', 'approve_budgets', 'view_analytics', 'executive_dashboard', 'policy_management'], colorClass: 'border-red-200 bg-red-50', iconBg: 'bg-red-200', icon: 'fa-crown text-red-600', btnClass: 'border-red-400 text-red-600 hover:bg-red-50'},
            {name: 'Admin', description: 'Full system access', permissions: ['full_access', 'manage_users', 'manage_permissions', 'system_settings', 'view_audit_logs', 'manage_backups', 'manage_roles', 'system_config', 'security_settings', 'database_management', 'api_access', 'bulk_operations', 'notification_management', 'integration_settings', 'advanced_reports'], colorClass: 'border-gray-200 bg-gray-50', iconBg: 'bg-gray-200', icon: 'fa-user-shield text-gray-600', btnClass: 'border-gray-400 text-gray-600 hover:bg-gray-100'}
        ],
        availablePermissions: [
            {category: 'Members', permissions: ['view_members', 'create_members', 'edit_members', 'delete_members']},
            {category: 'Financial', permissions: ['view_loans', 'create_loans', 'approve_loans', 'delete_loans', 'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions', 'process_deposits', 'process_withdrawals']},
            {category: 'Projects', permissions: ['view_projects', 'create_projects', 'edit_projects', 'delete_projects', 'manage_projects']},
            {category: 'Reports', permissions: ['view_reports', 'generate_reports', 'export_reports', 'view_all_reports', 'advanced_reports']},
            {category: 'Settings', permissions: ['view_settings', 'edit_settings', 'system_settings', 'system_config']},
            {category: 'System', permissions: ['manage_users', 'manage_permissions', 'view_audit_logs', 'manage_backups', 'system_health', 'full_access']}
        ],
        editRole(role) {
            this.editingRole = JSON.parse(JSON.stringify(role));
            this.showEditModal = true;
        },
        selectAll() {
            this.editingRole.permissions = this.availablePermissions.flatMap(c => c.permissions);
        },
        savePermissions() {
            const roleIndex = this.roles.findIndex(r => r.name === this.editingRole.name);
            if (roleIndex !== -1) {
                this.roles[roleIndex].permissions = [...this.editingRole.permissions];
            }
            this.showEditModal = false;
            alert('Permissions updated successfully!');
        }
    }
}
</script>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endsection
