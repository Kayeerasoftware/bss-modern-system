@extends('layouts.app')

@section('title', 'User Management - BSS')

@section('content')
    <div x-data="userManagement()" class="container mx-auto px-6 py-8 pt-16">
        <!-- Add User Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 glass-card">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Add New User</h2>
            <form @submit.prevent="createUser()" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input x-model="newUser.name" type="text" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input x-model="newUser.email" type="email" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input x-model="newUser.password" type="password" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select x-model="newUser.role" required class="form-input">
                        <option value="">Select Role</option>
                        <option value="admin">Administrator</option>
                        <option value="manager">Manager</option>
                        <option value="treasurer">Treasurer</option>
                        <option value="secretary">Secretary</option>
                        <option value="member">Member</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors w-full">
                        <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-plus'" class="mr-2"></i>
                        <span x-text="loading ? 'Adding...' : 'Add User'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 glass-card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Users</h2>
                <button @click="loadUsers()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Role</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Created</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="user.name"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="user.email"></td>
                                <td class="px-4 py-3">
                                    <span :class="getRoleBadge(user.role)" class="px-2 py-1 text-xs font-medium rounded-full" x-text="user.role"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                          class="px-2 py-1 text-xs font-medium rounded-full" 
                                          x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="formatDate(user.created_at)"></td>
                                <td class="px-4 py-3 space-x-2">
                                    <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="toggleUserStatus(user)" :class="user.is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'">
                                        <i :class="user.is_active ? 'fas fa-ban' : 'fas fa-check'"></i>
                                    </button>
                                    <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Role Permissions Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mt-8 glass-card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Role Permissions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <h4 class="font-medium text-red-800 dark:text-red-200">Administrator</h4>
                    <p class="text-sm text-red-600 dark:text-red-300">Full system access, user management</p>
                </div>
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h4 class="font-medium text-blue-800 dark:text-blue-200">Manager</h4>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Manage members, loans, projects</p>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <h4 class="font-medium text-green-800 dark:text-green-200">Treasurer</h4>
                    <p class="text-sm text-green-600 dark:text-green-300">Handle finances, approve loans</p>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200">Secretary</h4>
                    <p class="text-sm text-yellow-600 dark:text-yellow-300">Manage records, meetings</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="font-medium text-gray-800 dark:text-gray-200">Member</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Basic access, view own data</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userManagement() {
            return {
                loading: false,
                users: [],
                newUser: {
                    name: '',
                    email: '',
                    password: '',
                    role: ''
                },

                init() {
                    this.loadUsers();
                },

                async loadUsers() {
                    try {
                        const response = await fetch('/api/users');
                        const result = await response.json();
                        if (result.success) {
                            this.users = result.data;
                        }
                    } catch (error) {
                        console.error('Failed to load users:', error);
                    }
                },

                async createUser() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/users', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.newUser)
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.users.unshift(result.data);
                            this.newUser = { name: '', email: '', password: '', role: '' };
                            alert('User created successfully!');
                        } else {
                            alert('Failed to create user');
                        }
                    } catch (error) {
                        alert('Error creating user');
                    } finally {
                        this.loading = false;
                    }
                },

                async toggleUserStatus(user) {
                    try {
                        const response = await fetch(`/api/users/${user.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ is_active: !user.is_active })
                        });

                        if (response.ok) {
                            user.is_active = !user.is_active;
                        }
                    } catch (error) {
                        alert('Failed to update user status');
                    }
                },

                async deleteUser(user) {
                    if (!confirm('Are you sure you want to delete this user?')) return;

                    try {
                        const response = await fetch(`/api/users/${user.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (response.ok) {
                            this.users = this.users.filter(u => u.id !== user.id);
                        }
                    } catch (error) {
                        alert('Failed to delete user');
                    }
                },

                getRoleBadge(role) {
                    const badges = {
                        'admin': 'bg-red-100 text-red-800',
                        'manager': 'bg-blue-100 text-blue-800',
                        'treasurer': 'bg-green-100 text-green-800',
                        'secretary': 'bg-yellow-100 text-yellow-800',
                        'member': 'bg-gray-100 text-gray-800'
                    };
                    return badges[role] || 'bg-gray-100 text-gray-800';
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString();
                }
            }
        }
    </script>
@endsection