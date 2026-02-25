<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'permissions'" id="permissions">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Role Permissions Management</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="role in roles" :key="role.name">
            <div class="border-2 rounded-xl p-6">
                <h4 class="font-bold text-lg" x-text="role.name"></h4>
                <button @click="editRolePermissions(role)" class="w-full mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Edit Permissions
                </button>
            </div>
        </template>
    </div>
</div>
