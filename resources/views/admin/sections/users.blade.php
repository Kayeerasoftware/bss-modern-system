<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'users'" id="users">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">User Accounts</h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">User Accounts</h3>
        <button @click="showAddUserModal = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i class="fas fa-user-plus mr-2"></i>Add User
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Email</th>
                    <th class="text-left py-2">Role</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-left py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="user in users" :key="user.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2" x-text="user.name"></td>
                        <td class="py-2" x-text="user.email"></td>
                        <td class="py-2"><span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs" x-text="user.role"></span></td>
                        <td class="py-2"><span class="px-2 py-1 rounded text-xs" :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" x-text="user.status"></span></td>
                        <td class="py-2">
                            <button @click="toggleUserStatus(user.id)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-toggle-on"></i></button>
                            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
