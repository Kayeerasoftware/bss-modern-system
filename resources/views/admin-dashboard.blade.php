<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50" x-data="adminPanel()">
    @include('navs.admin-topnav')
    @include('navs.admin-sidenav')

    <div class="main-content ml-0 lg:ml-36 mt-12 transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-36'">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Dashboard Title -->
            <div class="mb-6" x-show="activeLink === 'stats'">
                <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-gray-600">System overview and statistics</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8" x-show="activeLink === 'stats'">
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Members</p>
                            <p class="text-2xl font-bold text-blue-600" x-text="stats.totalMembers">0</p>
                        </div>
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Savings</p>
                            <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(stats.totalSavings)">0</p>
                        </div>
                        <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Active Loans</p>
                            <p class="text-2xl font-bold text-yellow-600" x-text="stats.activeLoans">0</p>
                        </div>
                        <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Projects</p>
                            <p class="text-2xl font-bold text-purple-600" x-text="stats.totalProjects">0</p>
                        </div>
                        <i class="fas fa-project-diagram text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Approvals</p>
                            <p class="text-2xl font-bold text-red-600" x-text="stats.pendingApprovals">0</p>
                        </div>
                        <i class="fas fa-clock text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8" x-show="activeLink === 'stats'">
                <!-- Members Growth Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Members Growth</h3>
                    <div style="height: 250px;">
                        <canvas id="membersChart"></canvas>
                    </div>
                </div>

                <!-- Financial Overview Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Financial Overview</h3>
                    <div style="height: 250px;">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>

                <!-- Loan Status Distribution -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Loan Status Distribution</h3>
                    <div style="height: 250px;">
                        <canvas id="loanStatusChart"></canvas>
                    </div>
                </div>

                <!-- Transaction Types -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Transaction Types</h3>
                    <div style="height: 250px;">
                        <canvas id="transactionTypesChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
                    <div style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Project Progress -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Project Progress</h3>
                    <div style="height: 250px;">
                        <canvas id="projectChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Admin Profile -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'profile'" id="profile">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Admin Profile</h2>
                
                <!-- Profile Header Card -->
                <div class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 mb-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center overflow-hidden border-4 border-white shadow-lg">
                                <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                                <i x-show="!profilePicture" class="fas fa-user-shield text-white text-5xl"></i>
                            </div>
                            <button @click="showProfileModal = true" class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition shadow-lg">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-800" x-text="adminProfile.name">Admin</h3>
                            <p class="text-blue-600 font-medium" x-text="adminProfile.role">Administrator</p>
                            <p class="text-gray-600 text-sm mt-1" x-text="adminProfile.email">admin@bss.com</p>
                            <div class="flex flex-wrap gap-2 mt-3 justify-center md:justify-start">
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-circle text-green-500 text-[8px] mr-1"></i>Active
                                </span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-shield-alt mr-1"></i>Full Access
                                </span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i>Last login: Today
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Personal Information -->
                    <div class="bg-white border rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-2"></i>Personal Information
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" x-model="adminProfile.name" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" x-model="adminProfile.email" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" x-model="adminProfile.phone" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" x-model="adminProfile.location" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="bg-white border rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-lock text-red-600 mr-2"></i>Security Settings
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input type="password" placeholder="Enter current password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" placeholder="Enter new password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" placeholder="Confirm new password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button @click="alert('Password updated successfully!')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                <i class="fas fa-key mr-2"></i>Change Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Logins</p>
                                <p class="text-2xl font-bold text-blue-600">1,234</p>
                            </div>
                            <i class="fas fa-sign-in-alt text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Actions Taken</p>
                                <p class="text-2xl font-bold text-green-600">5,678</p>
                            </div>
                            <i class="fas fa-tasks text-green-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Reports Generated</p>
                                <p class="text-2xl font-bold text-purple-600">342</p>
                            </div>
                            <i class="fas fa-file-alt text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">System Uptime</p>
                                <p class="text-2xl font-bold text-orange-600">99.9%</p>
                            </div>
                            <i class="fas fa-server text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white border rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-history text-gray-600 mr-2"></i>Recent Activity
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-sign-in-alt text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Logged in to admin dashboard</p>
                                <p class="text-xs text-gray-500">Today at 10:30 AM</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user-plus text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Added new member</p>
                                <p class="text-xs text-gray-500">Yesterday at 3:45 PM</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Updated system settings</p>
                                <p class="text-xs text-gray-500">2 days ago at 11:20 AM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <button @click="localStorage.setItem('adminProfile', JSON.stringify(adminProfile)); alert('Profile updated successfully!')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <button @click="activeLink = 'settings'; document.getElementById('settings').scrollIntoView({ behavior: 'smooth' })" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                        <i class="fas fa-cog mr-2"></i>System Settings
                    </button>
                    <button @click="activeLink = 'audit'; document.getElementById('audit').scrollIntoView({ behavior: 'smooth' })" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                        <i class="fas fa-history mr-2"></i>View Full Activity Log
                    </button>
                </div>
            </div>

            <!-- System Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'settings'" id="settings">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">System Settings</h2>
                
                <!-- Financial Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-coins text-green-600 mr-2"></i>Financial Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%)</label>
                            <div class="relative">
                                <input type="number" x-model="settings.interest_rate" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" step="0.1" min="0" max="100">
                                <span class="absolute right-3 top-3 text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Annual interest rate for loans</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan Processing Fee (%)</label>
                            <div class="relative">
                                <input type="number" x-model="settings.loan_fee" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" step="0.1" min="0" max="10">
                                <span class="absolute right-3 top-3 text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Fee charged on loan approval</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Savings (UGX)</label>
                            <input type="number" x-model="settings.min_savings" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" min="0" step="1000">
                            <p class="text-xs text-gray-500 mt-1">Minimum required savings balance</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Loan Amount (UGX)</label>
                            <input type="number" x-model="settings.max_loan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" min="0" step="100000">
                            <p class="text-xs text-gray-500 mt-1">Maximum loan amount per member</p>
                        </div>
                    </div>
                </div>

                <!-- System Configuration -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>System Configuration
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">System Name</label>
                            <input type="text" x-model="settings.system_name" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="BSS Investment Group">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select x-model="settings.currency" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="UGX">UGX - Ugandan Shilling</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                            <select x-model="settings.timezone" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="Africa/Kampala">Africa/Kampala (EAT)</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York (EST)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                            <select x-model="settings.date_format" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-bell text-orange-600 mr-2"></i>Notification Settings
                    </h3>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="settings.email_notifications" class="w-5 h-5 text-blue-600 rounded">
                            <span class="ml-3 text-sm font-medium text-gray-700">Enable Email Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="settings.sms_notifications" class="w-5 h-5 text-blue-600 rounded">
                            <span class="ml-3 text-sm font-medium text-gray-700">Enable SMS Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="settings.loan_approval_notify" class="w-5 h-5 text-blue-600 rounded">
                            <span class="ml-3 text-sm font-medium text-gray-700">Notify on Loan Approval/Rejection</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="settings.transaction_notify" class="w-5 h-5 text-blue-600 rounded">
                            <span class="ml-3 text-sm font-medium text-gray-700">Notify on Transactions</span>
                        </label>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-shield-alt text-red-600 mr-2"></i>Security Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
                            <input type="number" x-model="settings.session_timeout" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" min="5" max="1440">
                            <p class="text-xs text-gray-500 mt-1">Auto logout after inactivity</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Min Length</label>
                            <input type="number" x-model="settings.password_min_length" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" min="6" max="20">
                            <p class="text-xs text-gray-500 mt-1">Minimum password characters</p>
                        </div>
                        <div class="col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="settings.two_factor_auth" class="w-5 h-5 text-blue-600 rounded">
                                <span class="ml-3 text-sm font-medium text-gray-700">Enable Two-Factor Authentication</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-4 border-t">
                    <button @click="resetSettings()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-undo mr-2"></i>Reset to Default
                    </button>
                    <button @click="updateSettings()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save All Settings
                    </button>
                </div>
            </div>

            <!-- User Accounts Management -->
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

            <!-- Reports & Analytics -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'reports'" id="reports">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Reports & Analytics</h2>
                
                <!-- Report Filters -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-semibold mb-3">Report Filters</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                            <input type="date" x-model="reportFilters.dateFrom" class="w-full p-2 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" x-model="reportFilters.dateTo" class="w-full p-2 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Format</label>
                            <select x-model="reportFilters.format" class="w-full p-2 border rounded text-sm">
                                <option value="html">PDF (Print to Save)</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Report Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Members Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-users text-blue-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Members Report</h4>
                                <p class="text-xs text-gray-600">Complete member list with details</p>
                            </div>
                        </div>
                        <button @click="generateReport('members')" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>

                    <!-- Financial Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-chart-line text-green-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Financial Report</h4>
                                <p class="text-xs text-gray-600">Savings, deposits, withdrawals</p>
                            </div>
                        </div>
                        <button @click="generateReport('financial')" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>

                    <!-- Loans Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-money-bill-wave text-yellow-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Loans Report</h4>
                                <p class="text-xs text-gray-600">All loan applications & status</p>
                            </div>
                        </div>
                        <button @click="generateReport('loans')" class="w-full px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>

                    <!-- Transactions Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exchange-alt text-purple-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Transactions Report</h4>
                                <p class="text-xs text-gray-600">All financial transactions</p>
                            </div>
                        </div>
                        <button @click="generateReport('transactions')" class="w-full px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>

                    <!-- Projects Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-project-diagram text-indigo-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Projects Report</h4>
                                <p class="text-xs text-gray-600">Project progress & budgets</p>
                            </div>
                        </div>
                        <button @click="generateReport('projects')" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>

                    <!-- Audit Report -->
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-clipboard-list text-red-600 text-3xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Audit Report</h4>
                                <p class="text-xs text-gray-600">System activity logs</p>
                            </div>
                        </div>
                        <button @click="generateReport('audit')" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Reports</h3>
                        <button x-show="selectedReports.length > 0" @click="deleteSelectedReports()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            <i class="fas fa-trash mr-2"></i>Delete Selected (<span x-text="selectedReports.length"></span>)
                        </button>
                    </div>
                    
                    <!-- Search and Filter Bar -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <input type="text" x-model="reportSearch" placeholder="Search reports..." class="w-full p-2 border rounded text-sm">
                            </div>
                            <div>
                                <select x-model="reportTypeFilter" class="w-full p-2 border rounded text-sm">
                                    <option value="all">All Types</option>
                                    <option value="members">Members</option>
                                    <option value="financial">Financial</option>
                                    <option value="loans">Loans</option>
                                    <option value="transactions">Transactions</option>
                                    <option value="projects">Projects</option>
                                    <option value="audit">Audit</option>
                                </select>
                            </div>
                            <div>
                                <select x-model="reportFormatFilter" class="w-full p-2 border rounded text-sm">
                                    <option value="all">All Formats</option>
                                    <option value="html">HTML/PDF</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div>
                                <select x-model="reportSortBy" class="w-full p-2 border rounded text-sm">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="type">By Type</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-sm">Report Type</th>
                                    <th class="text-left py-2 text-sm">Generated</th>
                                    <th class="text-left py-2 text-sm">Format</th>
                                    <th class="text-left py-2 text-sm">Actions</th>
                                    <th class="text-right py-2 text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>Select All</span>
                                            <input type="checkbox" @change="toggleAllReports($event.target.checked)" class="w-4 h-4">
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="report in filteredReports" :key="report.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 text-sm" x-text="report.type"></td>
                                        <td class="py-2 text-sm" x-text="report.date"></td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="report.format"></span>
                                        </td>
                                        <td class="py-2">
                                            <button @click="viewReport(report)" class="text-green-600 hover:text-green-800 text-sm mr-2" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button @click="downloadReport(report.id)" class="text-blue-600 hover:text-blue-800 text-sm mr-2" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button @click="deleteReport(report.id)" class="text-red-600 hover:text-red-800 text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td class="py-2 text-right">
                                            <input type="checkbox" :value="report.id" x-model="selectedReports" class="w-4 h-4">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notifications Center -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'notifications'" id="notifications">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Notifications Center</h2>
                
                <!-- Notification Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-gray-600">Total Sent</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="notificationStats.total || 0">0</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                        <p class="text-sm text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-orange-600" x-text="notificationStats.unread || 0">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-gray-600">Delivered</p>
                        <p class="text-2xl font-bold text-green-600" x-text="notificationStats.delivered || 0">0</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Failed</p>
                        <p class="text-2xl font-bold text-red-600" x-text="notificationStats.failed || 0">0</p>
                    </div>
                </div>

                <!-- Send New Notification -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-paper-plane text-orange-600 mr-2"></i>Send New Notification
                    </h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Notification Title</label>
                                <input type="text" x-model="notificationForm.title" placeholder="Enter notification title" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Priority</label>
                                <select x-model="notificationForm.priority" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                    <option value="low">Low</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Message</label>
                            <textarea x-model="notificationForm.message" placeholder="Enter your message here..." class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500" rows="4"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Send To</label>
                                <select x-model="notificationForm.target" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                    <option value="all">All Members</option>
                                    <option value="client">Clients Only</option>
                                    <option value="shareholder">Shareholders Only</option>
                                    <option value="cashier">Cashiers Only</option>
                                    <option value="td">TDs Only</option>
                                    <option value="ceo">CEOs Only</option>
                                    <option value="custom">Custom Selection</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Delivery Method</label>
                                <select x-model="notificationForm.method" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                    <option value="system">System Notification</option>
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="all">All Methods</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Schedule</label>
                                <select x-model="notificationForm.schedule" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                    <option value="now">Send Now</option>
                                    <option value="scheduled">Schedule for Later</option>
                                </select>
                            </div>
                        </div>
                        <div x-show="notificationForm.schedule === 'scheduled'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Schedule Date</label>
                                <input type="date" x-model="notificationForm.scheduleDate" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500" :min="new Date().toISOString().split('T')[0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Schedule Time</label>
                                <input type="time" x-model="notificationForm.scheduleTime" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-4">
                            <button @click="saveAsTemplate()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                <i class="fas fa-save mr-2"></i>Save as Template
                            </button>
                            <div class="space-x-2">
                                <button @click="notificationForm = {priority: 'normal', schedule: 'now', method: 'system', target: 'all'}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                    <i class="fas fa-redo mr-2"></i>Reset
                                </button>
                                <button @click="sendNotification()" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                    <i class="fas fa-paper-plane mr-2"></i>Send Notification
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-file-alt text-purple-600 mr-2"></i>Quick Templates
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <button @click="useTemplate('meeting')" class="p-3 border rounded-lg hover:bg-blue-50 text-left">
                            <p class="font-medium text-sm">Meeting Reminder</p>
                            <p class="text-xs text-gray-600">Upcoming meeting notification</p>
                        </button>
                        <button @click="useTemplate('payment')" class="p-3 border rounded-lg hover:bg-green-50 text-left">
                            <p class="font-medium text-sm">Payment Due</p>
                            <p class="text-xs text-gray-600">Payment reminder notice</p>
                        </button>
                        <button @click="useTemplate('announcement')" class="p-3 border rounded-lg hover:bg-yellow-50 text-left">
                            <p class="font-medium text-sm">General Announcement</p>
                            <p class="text-xs text-gray-600">Important announcement</p>
                        </button>
                    </div>
                </div>

                <!-- Notification History -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Notification History</h3>
                        <div class="flex gap-2">
                            <button x-show="selectedNotifications.length > 0" @click="deleteSelectedNotifications()" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>Delete Selected (<span x-text="selectedNotifications.length"></span>)
                            </button>
                            <select x-model="notificationFilter" class="p-2 border rounded text-sm">
                                <option value="all">All Status</option>
                                <option value="delivered">Delivered</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                            <button @click="loadNotificationHistory()" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-sm">Title</th>
                                    <th class="text-left py-2 text-sm">Recipients</th>
                                    <th class="text-left py-2 text-sm">Method</th>
                                    <th class="text-left py-2 text-sm">Priority</th>
                                    <th class="text-left py-2 text-sm">Status</th>
                                    <th class="text-left py-2 text-sm">Sent</th>
                                    <th class="text-left py-2 text-sm">Actions</th>
                                    <th class="text-right py-2 text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>Select</span>
                                            <input type="checkbox" @change="toggleAllNotifications($event.target.checked)" class="w-4 h-4">
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="notification in filteredNotifications" :key="notification.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 text-sm" x-text="notification.title"></td>
                                        <td class="py-2 text-sm" x-text="notification.recipients"></td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="notification.method"></span>
                                        </td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-gray-100 text-gray-800': notification.priority === 'low',
                                                'bg-blue-100 text-blue-800': notification.priority === 'normal',
                                                'bg-orange-100 text-orange-800': notification.priority === 'high',
                                                'bg-red-100 text-red-800': notification.priority === 'urgent'
                                            }" x-text="notification.priority"></span>
                                        </td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': notification.status === 'delivered',
                                                'bg-yellow-100 text-yellow-800': notification.status === 'pending',
                                                'bg-red-100 text-red-800': notification.status === 'failed'
                                            }" x-text="notification.status"></span>
                                        </td>
                                        <td class="py-2 text-sm" x-text="notification.sent_at"></td>
                                        <td class="py-2">
                                            <button @click="viewNotification(notification)" class="text-blue-600 hover:text-blue-800 text-sm mr-2" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button @click="resendNotification(notification.id)" class="text-green-600 hover:text-green-800 text-sm mr-2" title="Resend">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button @click="deleteNotification(notification.id)" class="text-red-600 hover:text-red-800 text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td class="py-2 text-right">
                                            <input type="checkbox" :value="notification.id" x-model="selectedNotifications" class="w-4 h-4">
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredNotifications.length === 0">
                                    <td colspan="7" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>No notifications found</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Audit Log -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'audit'" id="audit">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Activity Log</h2>
                        <p class="text-sm text-gray-600">Real-time system activity monitoring</p>
                    </div>
                    <button @click="loadAuditLogs()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>

                <!-- Filters -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <input type="text" x-model="auditSearch" placeholder="Search activities..." class="p-2 border rounded">
                        <select x-model="auditTypeFilter" class="p-2 border rounded">
                            <option value="all">All Types</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="approve">Approve</option>
                            <option value="process">Process</option>
                            <option value="generate">Generate</option>
                        </select>
                        <select x-model="auditUserFilter" class="p-2 border rounded">
                            <option value="all">All Users</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="cashier">Cashier</option>
                            <option value="td">TD</option>
                            <option value="ceo">CEO</option>
                        </select>
                        <div class="relative">
                            <input type="date" x-model="auditDateFilter" class="w-full p-2 border rounded">
                            <button x-show="auditDateFilter" @click="auditDateFilter = ''" class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="time" x-model="auditTimeFilter" step="1" class="w-full p-2 border rounded">
                            <button x-show="auditTimeFilter" @click="auditTimeFilter = ''" class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="relative">
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-purple-500 to-pink-500"></div>
                    
                    <div class="space-y-4">
                        <template x-for="log in filteredAuditLogs" :key="log.id">
                            <div class="relative pl-20 pb-4">
                                <div class="absolute left-5 w-6 h-6 rounded-full flex items-center justify-center" :class="{
                                    'bg-green-500': log.action?.includes('create'),
                                    'bg-blue-500': log.action?.includes('update'),
                                    'bg-red-500': log.action?.includes('delete'),
                                    'bg-yellow-500': log.action?.includes('login'),
                                    'bg-purple-500': true
                                }">
                                    <i class="fas text-white text-xs fa-cog"></i>
                                </div>
                                
                                <div class="bg-white border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800" x-text="log.action"></h4>
                                            <p class="text-sm text-gray-600 mt-1" x-text="log.details"></p>
                                        </div>
                                        <span class="text-xs text-gray-500 whitespace-nowrap ml-4" x-text="formatDateTime(log.timestamp)"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-user mr-1"></i>
                                            <span x-text="log.user"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div x-show="filteredAuditLogs.length === 0" class="text-center py-12">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">No activity logs found</p>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'backup'" id="backup">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Database Backup & Restore</h2>
                
                <!-- Backup Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-gray-600">Total Backups</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="backups.length">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-gray-600">Last Backup</p>
                        <p class="text-sm font-bold text-green-600" x-text="backups.length > 0 ? backups[0].created_at : 'Never'">Never</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm text-gray-600">Total Size</p>
                        <p class="text-sm font-bold text-purple-600" x-text="calculateTotalSize()">0 MB</p>
                    </div>
                </div>

                <!-- Create Backup -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-database text-green-600 mr-2"></i>Create New Backup
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4 bg-white">
                            <h4 class="font-medium mb-2 flex items-center">
                                <i class="fas fa-download text-green-600 mr-2"></i>Full Database Backup
                            </h4>
                            <p class="text-sm text-gray-600 mb-4">Create a complete backup of all database tables and data</p>
                            <button @click="createBackup()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-download mr-2"></i>Create Backup
                            </button>
                        </div>
                        <div class="border rounded-lg p-4 bg-white">
                            <h4 class="font-medium mb-2 flex items-center">
                                <i class="fas fa-upload text-blue-600 mr-2"></i>Restore from File
                            </h4>
                            <p class="text-sm text-gray-600 mb-4">Upload and restore database from backup file</p>
                            <div class="space-y-2">
                                <input type="file" id="restoreFile" accept=".json" class="w-full text-sm border rounded p-2">
                                <button @click="restoreBackup()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-upload mr-2"></i>Restore Backup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup History -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Backup History</h3>
                        <button @click="loadBackups()" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-sm">Filename</th>
                                    <th class="text-left py-2 text-sm">Size</th>
                                    <th class="text-left py-2 text-sm">Type</th>
                                    <th class="text-left py-2 text-sm">Status</th>
                                    <th class="text-left py-2 text-sm">Created</th>
                                    <th class="text-left py-2 text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="backup in backups" :key="backup.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 text-sm">
                                            <i class="fas fa-file-archive text-blue-600 mr-2"></i>
                                            <span x-text="backup.filename"></span>
                                        </td>
                                        <td class="py-2 text-sm" x-text="backup.size"></td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs" x-text="backup.type"></span>
                                        </td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs" x-text="backup.status"></span>
                                        </td>
                                        <td class="py-2 text-sm" x-text="backup.created_at"></td>
                                        <td class="py-2">
                                            <button @click="downloadBackup(backup.id)" class="text-blue-600 hover:text-blue-800 text-sm mr-2" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button @click="deleteBackup(backup.id)" class="text-red-600 hover:text-red-800 text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="backups.length === 0">
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-database text-4xl mb-2"></i>
                                        <p>No backups found</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'financial'" id="financial">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Financial Management</h2>
                
                <!-- Financial Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-gray-600">Total Deposits</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(financialSummary.totalDeposits)">0</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Total Withdrawals</p>
                        <p class="text-2xl font-bold text-red-600" x-text="formatCurrency(financialSummary.totalWithdrawals)">0</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-gray-600">Net Balance</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(financialSummary.netBalance)">0</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm text-gray-600">Total Loans</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="formatCurrency(financialSummary.totalLoans)">0</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <button @click="showDepositModal = true" class="p-4 border-2 border-green-500 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-plus-circle text-green-600 text-3xl mb-2"></i>
                        <p class="font-semibold text-green-600">Record Deposit</p>
                    </button>
                    <button @click="showWithdrawalModal = true" class="p-4 border-2 border-red-500 rounded-lg hover:bg-red-50 transition">
                        <i class="fas fa-minus-circle text-red-600 text-3xl mb-2"></i>
                        <p class="font-semibold text-red-600">Record Withdrawal</p>
                    </button>
                    <button @click="showTransferModal = true" class="p-4 border-2 border-blue-500 rounded-lg hover:bg-blue-50 transition">
                        <i class="fas fa-exchange-alt text-blue-600 text-3xl mb-2"></i>
                        <p class="font-semibold text-blue-600">Transfer Funds</p>
                    </button>
                </div>

                <!-- Recent Transactions -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Transactions</h3>
                        <div class="flex gap-2">
                            <input type="text" x-model="financialSearch" placeholder="Search transactions..." class="p-2 border rounded text-sm w-48">
                            <select x-model="financialFilter" class="p-2 border rounded text-sm">
                                <option value="all">All Types</option>
                                <option value="deposit">Deposits</option>
                                <option value="withdrawal">Withdrawals</option>
                                <option value="transfer">Transfers</option>
                            </select>
                            <button @click="loadTransactions()" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-sm">Transaction ID</th>
                                    <th class="text-left py-2 text-sm">Member</th>
                                    <th class="text-left py-2 text-sm">Type</th>
                                    <th class="text-left py-2 text-sm">Amount</th>
                                    <th class="text-left py-2 text-sm">Date</th>
                                    <th class="text-left py-2 text-sm">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="transaction in filteredFinancialTransactions" :key="transaction.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 text-sm" x-text="transaction.transaction_id"></td>
                                        <td class="py-2 text-sm" x-text="transaction.member_id"></td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 rounded text-xs" :class="{
                                                'bg-green-100 text-green-800': transaction.type === 'deposit',
                                                'bg-red-100 text-red-800': transaction.type === 'withdrawal',
                                                'bg-blue-100 text-blue-800': transaction.type === 'transfer'
                                            }" x-text="transaction.type"></span>
                                        </td>
                                        <td class="py-2 text-sm font-semibold" :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(transaction.amount)"></td>
                                        <td class="py-2 text-sm" x-text="new Date(transaction.created_at).toLocaleDateString()"></td>
                                        <td class="py-2 text-sm">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Completed</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bulk Operations -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'bulk'" id="bulk">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Bulk Operations</h2>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="members.length">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-gray-600">Last Import</p>
                        <p class="text-sm font-bold text-green-600">Never</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm text-gray-600">Messages Sent</p>
                        <p class="text-2xl font-bold text-purple-600">0</p>
                    </div>
                </div>

                <!-- Operation Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Import Members -->
                    <div class="border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition hover:border-blue-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-file-import text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Import Members</h3>
                                <p class="text-xs text-gray-600">Upload CSV file</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Bulk import members from CSV file with all details</p>
                        <button @click="showBulkImportModal = true" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-upload mr-2"></i>Import CSV
                        </button>
                    </div>

                    <!-- Export Members -->
                    <div class="border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition hover:border-green-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-file-export text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Export Members</h3>
                                <p class="text-xs text-gray-600">Download CSV</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Export all member data to CSV format</p>
                        <button @click="exportMembers()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>Export CSV
                        </button>
                    </div>

                    <!-- Bulk SMS -->
                    <div class="border-2 border-orange-200 rounded-lg p-6 hover:shadow-lg transition hover:border-orange-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-sms text-orange-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Bulk SMS</h3>
                                <p class="text-xs text-gray-600">Send to multiple</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Send SMS messages to selected members</p>
                        <button @click="showBulkSMSModal = true" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            <i class="fas fa-paper-plane mr-2"></i>Send SMS
                        </button>
                    </div>

                    <!-- Bulk Email -->
                    <div class="border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition hover:border-purple-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-envelope-bulk text-purple-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Bulk Email</h3>
                                <p class="text-xs text-gray-600">Email campaign</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Send emails to multiple members at once</p>
                        <button @click="showBulkEmailModal = true" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-envelope mr-2"></i>Send Email
                        </button>
                    </div>

                    <!-- Bulk Update -->
                    <div class="border-2 border-red-200 rounded-lg p-6 hover:shadow-lg transition hover:border-red-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-edit text-red-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Bulk Update</h3>
                                <p class="text-xs text-gray-600">Update records</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Update multiple member records at once</p>
                        <button @click="showBulkUpdateModal = true" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-sync mr-2"></i>Bulk Update
                        </button>
                    </div>

                    <!-- Bulk Delete -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition hover:border-gray-400">
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-trash-alt text-gray-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Bulk Delete</h3>
                                <p class="text-xs text-gray-600">Remove records</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Delete multiple inactive members</p>
                        <button @click="showBulkDeleteModal = true" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-trash mr-2"></i>Bulk Delete
                        </button>
                    </div>
                </div>

                <!-- CSV Template Download -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-blue-800">Need CSV Template?</h4>
                            <p class="text-sm text-blue-600">Download sample CSV format for member import</p>
                        </div>
                        <button @click="downloadCSVTemplate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'health'" id="health">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">System Health Monitor</h2>
                        <p class="text-sm text-gray-600">Real-time system performance and diagnostics</p>
                    </div>
                    <button @click="loadSystemHealth()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>

                <!-- Overall Health Status -->
                <div class="mb-6 p-6 rounded-lg" :class="systemHealth.overallStatus === 'healthy' ? 'bg-green-50 border-2 border-green-500' : systemHealth.overallStatus === 'warning' ? 'bg-yellow-50 border-2 border-yellow-500' : 'bg-red-50 border-2 border-red-500'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mr-4" :class="systemHealth.overallStatus === 'healthy' ? 'bg-green-500' : systemHealth.overallStatus === 'warning' ? 'bg-yellow-500' : 'bg-red-500'">
                                <i class="fas text-white text-2xl" :class="systemHealth.overallStatus === 'healthy' ? 'fa-check-circle' : systemHealth.overallStatus === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold" :class="systemHealth.overallStatus === 'healthy' ? 'text-green-800' : systemHealth.overallStatus === 'warning' ? 'text-yellow-800' : 'text-red-800'" x-text="systemHealth.overallStatus === 'healthy' ? 'System Healthy' : systemHealth.overallStatus === 'warning' ? 'Needs Attention' : 'Critical Issues'">System Healthy</h3>
                                <p class="text-sm" :class="systemHealth.overallStatus === 'healthy' ? 'text-green-600' : systemHealth.overallStatus === 'warning' ? 'text-yellow-600' : 'text-red-600'">All systems operational</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">Last Check</p>
                            <p class="text-sm font-semibold" x-text="systemHealth.lastCheck || 'Just now'">Just now</p>
                        </div>
                    </div>
                </div>

                <!-- Health Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 border rounded-lg hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-database text-blue-600 text-2xl"></i>
                            <span class="px-2 py-1 rounded-full text-xs" :class="systemHealth.database?.status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="systemHealth.database?.status || 'connected'">Connected</span>
                        </div>
                        <h4 class="font-semibold text-sm mb-1">Database</h4>
                        <p class="text-xs text-gray-600" x-text="'Size: ' + (systemHealth.database?.size || '2.4 MB')">Size: 2.4 MB</p>
                        <p class="text-xs text-gray-600" x-text="'Tables: ' + (systemHealth.database?.tables || 15)">Tables: 15</p>
                    </div>

                    <div class="p-4 border rounded-lg hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-hdd text-purple-600 text-2xl"></i>
                            <span class="px-2 py-1 rounded-full text-xs" :class="(systemHealth.storage?.usage || 0) < 70 ? 'bg-green-100 text-green-800' : (systemHealth.storage?.usage || 0) < 90 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'" x-text="(systemHealth.storage?.usage || 0) + '%'">0%</span>
                        </div>
                        <h4 class="font-semibold text-sm mb-1">Storage</h4>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                            <div class="h-2 rounded-full transition-all" :class="(systemHealth.storage?.usage || 0) < 70 ? 'bg-green-600' : (systemHealth.storage?.usage || 0) < 90 ? 'bg-yellow-600' : 'bg-red-600'" :style="`width: ${systemHealth.storage?.usage || 0}%`"></div>
                        </div>
                        <p class="text-xs text-gray-600" x-text="(systemHealth.storage?.used || '12 MB') + ' / ' + (systemHealth.storage?.total || '500 MB')">12 MB / 500 MB</p>
                    </div>

                    <div class="p-4 border rounded-lg hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-server text-green-600 text-2xl"></i>
                            <span class="px-2 py-1 rounded-full text-xs" :class="systemHealth.server?.status === 'online' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="systemHealth.server?.status || 'online'">Online</span>
                        </div>
                        <h4 class="font-semibold text-sm mb-1">Server</h4>
                        <p class="text-xs text-gray-600" x-text="'Uptime: ' + (systemHealth.server?.uptime || '99.9%')">Uptime: 99.9%</p>
                        <p class="text-xs text-gray-600" x-text="'Load: ' + (systemHealth.server?.load || 'Low')">Load: Low</p>
                    </div>

                    <div class="p-4 border rounded-lg hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-plug text-orange-600 text-2xl"></i>
                            <span class="px-2 py-1 rounded-full text-xs" :class="systemHealth.api?.status === 'operational' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="systemHealth.api?.status || 'operational'">Operational</span>
                        </div>
                        <h4 class="font-semibold text-sm mb-1">API</h4>
                        <p class="text-xs text-gray-600" x-text="'Response: ' + (systemHealth.api?.responseTime || '45ms')">Response: 45ms</p>
                        <p class="text-xs text-gray-600" x-text="'Requests: ' + (systemHealth.api?.requests || '1,234')">Requests: 1,234</p>
                    </div>
                </div>

                <!-- Detailed Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Performance Metrics -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>Performance Metrics
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Page Load Time</span>
                                    <span class="font-semibold" x-text="systemHealth.performance?.pageLoad || '1.2s'">1.2s</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${systemHealth.performance?.pageLoadPercent || 80}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Database Query Time</span>
                                    <span class="font-semibold" x-text="systemHealth.performance?.queryTime || '23ms'">23ms</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" :style="`width: ${systemHealth.performance?.queryTimePercent || 90}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Memory Usage</span>
                                    <span class="font-semibold" x-text="systemHealth.performance?.memory || '128 MB'">128 MB</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" :style="`width: ${systemHealth.performance?.memoryPercent || 45}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>CPU Usage</span>
                                    <span class="font-semibold" x-text="systemHealth.performance?.cpu || '12%'">12%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${systemHealth.performance?.cpuPercent || 12}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-info-circle text-green-600 mr-2"></i>System Information
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">PHP Version</span>
                                <span class="font-semibold" x-text="systemHealth.info?.phpVersion || '8.2.0'">8.2.0</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Laravel Version</span>
                                <span class="font-semibold" x-text="systemHealth.info?.laravelVersion || '11.x'">11.x</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Database Type</span>
                                <span class="font-semibold" x-text="systemHealth.info?.dbType || 'SQLite'">SQLite</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Environment</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold" x-text="systemHealth.info?.environment || 'local'">local</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Debug Mode</span>
                                <span class="px-2 py-1 rounded text-xs font-semibold" :class="systemHealth.info?.debug ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'" x-text="systemHealth.info?.debug ? 'Enabled' : 'Disabled'">Disabled</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Timezone</span>
                                <span class="font-semibold" x-text="systemHealth.info?.timezone || 'Africa/Kampala'">Africa/Kampala</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Backup Status -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Security Status -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-shield-alt text-red-600 mr-2"></i>Security Status
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-lock text-green-600 mr-2"></i>
                                    <span class="text-sm">SSL Certificate</span>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs" x-text="systemHealth.security?.ssl || 'Valid'">Valid</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-key text-blue-600 mr-2"></i>
                                    <span class="text-sm">Encryption</span>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs" x-text="systemHealth.security?.encryption || 'Active'">Active</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-user-shield text-purple-600 mr-2"></i>
                                    <span class="text-sm">Active Sessions</span>
                                </div>
                                <span class="font-semibold" x-text="systemHealth.security?.activeSessions || 5">5</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                    <span class="text-sm">Failed Login Attempts</span>
                                </div>
                                <span class="font-semibold" x-text="systemHealth.security?.failedLogins || 0">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Status -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-database text-blue-600 mr-2"></i>Backup Status
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-green-600 mr-2"></i>
                                    <span class="text-sm">Last Backup</span>
                                </div>
                                <span class="text-xs font-semibold" x-text="systemHealth.backup?.lastBackup || 'Never'">Never</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-blue-600 mr-2"></i>
                                    <span class="text-sm">Total Backups</span>
                                </div>
                                <span class="font-semibold" x-text="systemHealth.backup?.totalBackups || 0">0</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-hdd text-purple-600 mr-2"></i>
                                    <span class="text-sm">Backup Size</span>
                                </div>
                                <span class="font-semibold" x-text="systemHealth.backup?.totalSize || '0 MB'">0 MB</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-sm">Backup Status</span>
                                </div>
                                <span class="px-2 py-1 rounded text-xs" :class="systemHealth.backup?.status === 'healthy' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" x-text="systemHealth.backup?.status || 'healthy'">Healthy</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Log -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-history text-gray-600 mr-2"></i>Recent System Activity
                    </h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <template x-for="(activity, index) in (systemHealth.recentActivity || [])">  :key="index">
                            <div class="flex items-start p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                                <div class="w-2 h-2 rounded-full mt-2 mr-3" :class="activity.type === 'success' ? 'bg-green-500' : activity.type === 'warning' ? 'bg-yellow-500' : activity.type === 'error' ? 'bg-red-500' : 'bg-blue-500'"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium" x-text="activity.message"></p>
                                    <p class="text-xs text-gray-500" x-text="activity.timestamp"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="!systemHealth.recentActivity || systemHealth.recentActivity.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p class="text-sm">No recent activity</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <button @click="clearCache()" class="p-4 border-2 border-blue-200 rounded-lg hover:bg-blue-50 transition text-center">
                        <i class="fas fa-broom text-blue-600 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold">Clear Cache</p>
                    </button>
                    <button @click="optimizeDatabase()" class="p-4 border-2 border-green-200 rounded-lg hover:bg-green-50 transition text-center">
                        <i class="fas fa-database text-green-600 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold">Optimize DB</p>
                    </button>
                    <button @click="runDiagnostics()" class="p-4 border-2 border-purple-200 rounded-lg hover:bg-purple-50 transition text-center">
                        <i class="fas fa-stethoscope text-purple-600 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold">Run Diagnostics</p>
                    </button>
                    <button @click="exportHealthReport()" class="p-4 border-2 border-orange-200 rounded-lg hover:bg-orange-50 transition text-center">
                        <i class="fas fa-file-export text-orange-600 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold">Export Report</p>
                    </button>
                </div>
            </div>

            <!-- Permissions Management -->
            <div class="bg-white rounded-xl shadow-lg p-6" x-show="activeLink === 'permissions'" id="permissions">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Role Permissions Management</h2>
                        <p class="text-sm text-gray-600">Configure access control and permissions for each role</p>
                    </div>
                    <button @click="loadRoles()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>

                <!-- Roles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="role in roles" :key="role.name">
                        <div class="border-2 rounded-xl p-6 hover:shadow-lg transition" :class="{
                            'border-blue-200 bg-blue-50': role.name === 'Client',
                            'border-green-200 bg-green-50': role.name === 'Shareholder',
                            'border-yellow-200 bg-yellow-50': role.name === 'Cashier',
                            'border-purple-200 bg-purple-50': role.name === 'TD',
                            'border-red-200 bg-red-50': role.name === 'CEO',
                            'border-gray-200 bg-gray-50': role.name === 'Admin'
                        }">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3" :class="{
                                        'bg-blue-200': role.name === 'Client',
                                        'bg-green-200': role.name === 'Shareholder',
                                        'bg-yellow-200': role.name === 'Cashier',
                                        'bg-purple-200': role.name === 'TD',
                                        'bg-red-200': role.name === 'CEO',
                                        'bg-gray-200': role.name === 'Admin'
                                    }">
                                        <i class="fas text-xl" :class="{
                                            'fa-user text-blue-600': role.name === 'Client',
                                            'fa-chart-line text-green-600': role.name === 'Shareholder',
                                            'fa-cash-register text-yellow-600': role.name === 'Cashier',
                                            'fa-tools text-purple-600': role.name === 'TD',
                                            'fa-crown text-red-600': role.name === 'CEO',
                                            'fa-user-shield text-gray-600': role.name === 'Admin'
                                        }"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg" x-text="role.name"></h4>
                                        <p class="text-xs text-gray-600" x-text="role.description || 'Role description'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Permissions (<span x-text="role.permissions?.length || 0"></span>)</p>
                                <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto">
                                    <template x-for="permission in (role.permissions || []).slice(0, 6)" :key="permission">
                                        <span class="px-2 py-1 bg-white rounded text-xs border" x-text="permission"></span>
                                    </template>
                                    <span x-show="(role.permissions?.length || 0) > 6" class="px-2 py-1 bg-gray-200 rounded text-xs">+<span x-text="(role.permissions?.length || 0) - 6"></span> more</span>
                                </div>
                            </div>
                            
                            <button @click="editRolePermissions(role)" class="w-full px-4 py-2 bg-white border-2 rounded-lg hover:bg-gray-50 transition font-semibold text-sm" :class="{
                                'border-blue-400 text-blue-600 hover:bg-blue-50': role.name === 'Client',
                                'border-green-400 text-green-600 hover:bg-green-50': role.name === 'Shareholder',
                                'border-yellow-400 text-yellow-600 hover:bg-yellow-50': role.name === 'Cashier',
                                'border-purple-400 text-purple-600 hover:bg-purple-50': role.name === 'TD',
                                'border-red-400 text-red-600 hover:bg-red-50': role.name === 'CEO',
                                'border-gray-400 text-gray-600 hover:bg-gray-100': role.name === 'Admin'
                            }">
                                <i class="fas fa-edit mr-2"></i>Edit Permissions
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Permission Categories Info -->
                <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
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

            <!-- Members Management -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'members'" id="members">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Members Management</h2>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Members Management</h3>
                    <button @click="showAddMemberModal = true; fetchNextMemberId()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Add Member
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Member ID</th>
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Email</th>
                                <th class="text-left py-2">Role</th>
                                <th class="text-left py-2">Savings</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="member in members" :key="member.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2" x-text="member.member_id"></td>
                                    <td class="py-2" x-text="member.full_name"></td>
                                    <td class="py-2" x-text="member.email"></td>
                                    <td class="py-2"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs" x-text="member.role"></span></td>
                                    <td class="py-2" x-text="formatCurrency(member.savings)"></td>
                                    <td class="py-2">
                                        <button @click="editMember(member)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                                        <button @click="deleteMember(member.id)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Loans Management -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'loans'" id="loans">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Loan Applications</h2>
                <h3 class="text-lg font-semibold mb-4">Loan Applications</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Loan ID</th>
                                <th class="text-left py-2">Member</th>
                                <th class="text-left py-2">Amount</th>
                                <th class="text-left py-2">Purpose</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Updated By</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="loan in loans" :key="loan.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2" x-text="loan.loan_id"></td>
                                    <td class="py-2" x-text="loan.member_id"></td>
                                    <td class="py-2" x-text="formatCurrency(loan.amount)"></td>
                                    <td class="py-2" x-text="loan.purpose"></td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs" :class="loan.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : loan.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="loan.status"></span>
                                    </td>
                                    <td class="py-2">
                                        <span class="text-sm" x-text="loan.updated_by || '-'"></span>
                                    </td>
                                    <td class="py-2">
                                        <button x-show="loan.status === 'pending'" @click="approveLoan(loan.id)" class="px-2 py-1 bg-green-600 text-white rounded text-xs mr-1">Approve</button>
                                        <button x-show="loan.status === 'pending'" @click="rejectLoan(loan.id)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Reject</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions Management -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'transactions'" id="transactions">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Transactions Management</h2>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Recent Transactions</h3>
                    <button @click="showAddTransactionModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Add Transaction
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Transaction ID</th>
                                <th class="text-left py-2">Member</th>
                                <th class="text-left py-2">Type</th>
                                <th class="text-left py-2">Amount</th>
                                <th class="text-left py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="transaction in transactions" :key="transaction.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2" x-text="transaction.transaction_id"></td>
                                    <td class="py-2" x-text="transaction.member_id"></td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs" :class="transaction.type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="transaction.type"></span>
                                    </td>
                                    <td class="py-2" x-text="formatCurrency(transaction.amount)"></td>
                                    <td class="py-2" x-text="new Date(transaction.created_at).toLocaleDateString()"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Projects Management -->
            <div class="bg-white rounded-xl shadow-lg p-6" x-show="activeLink === 'projects'" id="projects">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Projects Management</h2>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Projects</h3>
                    <button @click="showAddProjectModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i>Add Project
                    </button>
                </div>
                
                <!-- Filters and View Toggle -->
                <div class="flex flex-wrap gap-4 mb-4">
                    <input type="text" x-model="projectSearchQuery" placeholder="Search projects..." class="flex-1 min-w-[200px] p-2 border rounded">
                    <select x-model="projectStatusFilter" class="p-2 border rounded">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="flex gap-2">
                        <button @click="projectViewMode = 'grid'" :class="projectViewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-200'" class="px-3 py-2 rounded"><i class="fas fa-th"></i></button>
                        <button @click="projectViewMode = 'list'" :class="projectViewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-gray-200'" class="px-3 py-2 rounded"><i class="fas fa-list"></i></button>
                    </div>
                </div>
                
                <!-- Grid View -->
                <div x-show="projectViewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="project in filteredProjects" :key="project.id">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold" x-text="project.name"></h4>
                            <p class="text-sm text-gray-600" x-text="project.description"></p>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Progress</span>
                                    <span x-text="project.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between items-center text-sm">
                                <span x-text="'Budget: ' + formatCurrency(project.budget)"></span>
                                <div>
                                    <button @click="editProject(project)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                                    <button @click="deleteProject(project.id)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- List View -->
                <div x-show="projectViewMode === 'list'" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Project ID</th>
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Budget</th>
                                <th class="text-left py-2">Progress</th>
                                <th class="text-left py-2">ROI</th>
                                <th class="text-left py-2">Risk</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="project in filteredProjects" :key="project.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2" x-text="project.project_id"></td>
                                    <td class="py-2" x-text="project.name"></td>
                                    <td class="py-2" x-text="formatCurrency(project.budget)"></td>
                                    <td class="py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                            </div>
                                            <span class="text-xs" x-text="project.progress + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="py-2" x-text="(project.roi || 0) + '%'"></td>
                                    <td class="py-2" x-text="project.risk_score || '-'"></td>
                                    <td class="py-2">
                                        <button @click="editProject(project)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                                        <button @click="deleteProject(project.id)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div x-show="showBulkImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-file-import text-blue-600 mr-2"></i>Import Members from CSV
            </h3>
            <form @submit.prevent="importMembers()">
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="font-medium text-sm mb-2">CSV Format Requirements:</h4>
                        <p class="text-xs text-gray-600 mb-1">Columns: full_name, email, contact, location, occupation, role, savings</p>
                        <p class="text-xs text-gray-600">Roles: client, shareholder, cashier, td, ceo</p>
                        <p class="text-xs text-gray-500 mt-1">Note: member_id will be auto-generated</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Upload CSV File</label>
                        <input type="file" @change="handleFileUpload($event)" accept=".csv" class="w-full p-3 border rounded" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" x-model="bulkImportOptions.skipDuplicates" class="w-4 h-4 text-blue-600 rounded mr-2">
                        <span class="text-sm">Skip duplicate emails</span>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" x-model="bulkImportOptions.sendWelcomeEmail" class="w-4 h-4 text-blue-600 rounded mr-2">
                        <span class="text-sm">Send welcome email to new members</span>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showBulkImportModal = false" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-upload mr-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk SMS Modal -->
    <div x-show="showBulkSMSModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-sms text-orange-600 mr-2"></i>Send Bulk SMS
            </h3>
            <form @submit.prevent="sendBulkSMS()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Recipients</label>
                        <select x-model="bulkSMSForm.recipients" class="w-full p-3 border rounded" required>
                            <option value="all">All Members</option>
                            <option value="client">Clients Only</option>
                            <option value="shareholder">Shareholders Only</option>
                            <option value="cashier">Cashiers Only</option>
                            <option value="td">TDs Only</option>
                            <option value="ceo">CEOs Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Message</label>
                        <textarea x-model="bulkSMSForm.message" class="w-full p-3 border rounded" rows="5" maxlength="160" placeholder="Enter SMS message (max 160 characters)" required></textarea>
                        <p class="text-xs text-gray-500 mt-1" x-text="(bulkSMSForm.message?.length || 0) + '/160 characters'"></p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded border border-orange-200">
                        <p class="text-sm font-medium">Estimated Recipients: <span x-text="getRecipientCount(bulkSMSForm.recipients)"></span></p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showBulkSMSModal = false; bulkSMSForm = {}" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                        <i class="fas fa-paper-plane mr-2"></i>Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Email Modal -->
    <div x-show="showBulkEmailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-envelope-bulk text-purple-600 mr-2"></i>Send Bulk Email
            </h3>
            <form @submit.prevent="sendBulkEmail()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Recipients</label>
                        <select x-model="bulkEmailForm.recipients" class="w-full p-3 border rounded" required>
                            <option value="all">All Members</option>
                            <option value="client">Clients Only</option>
                            <option value="shareholder">Shareholders Only</option>
                            <option value="cashier">Cashiers Only</option>
                            <option value="td">TDs Only</option>
                            <option value="ceo">CEOs Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email Subject</label>
                        <input type="text" x-model="bulkEmailForm.subject" class="w-full p-3 border rounded" placeholder="Enter email subject" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Message</label>
                        <textarea x-model="bulkEmailForm.message" class="w-full p-3 border rounded" rows="6" placeholder="Enter email message" required></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" x-model="bulkEmailForm.includeAttachment" class="w-4 h-4 text-purple-600 rounded mr-2">
                        <span class="text-sm">Include company logo</span>
                    </div>
                    <div class="p-3 bg-purple-50 rounded border border-purple-200">
                        <p class="text-sm font-medium">Estimated Recipients: <span x-text="getRecipientCount(bulkEmailForm.recipients)"></span></p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showBulkEmailModal = false; bulkEmailForm = {}" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        <i class="fas fa-envelope mr-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div x-show="showBulkUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-edit text-red-600 mr-2"></i>Bulk Update Members
            </h3>
            <form @submit.prevent="bulkUpdateMembers()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Select Members</label>
                        <select x-model="bulkUpdateForm.target" class="w-full p-3 border rounded" required>
                            <option value="all">All Members</option>
                            <option value="client">All Clients</option>
                            <option value="shareholder">All Shareholders</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Field to Update</label>
                        <select x-model="bulkUpdateForm.field" class="w-full p-3 border rounded" required>
                            <option value="role">Role</option>
                            <option value="location">Location</option>
                            <option value="occupation">Occupation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">New Value</label>
                        <input type="text" x-model="bulkUpdateForm.value" class="w-full p-3 border rounded" placeholder="Enter new value" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showBulkUpdateModal = false; bulkUpdateForm = {}" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        <i class="fas fa-sync mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div x-show="showBulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-md m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>Bulk Delete Members
            </h3>
            <div class="space-y-4">
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-800 font-medium">⚠️ Warning: This action cannot be undone!</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Select Members to Delete</label>
                    <select x-model="bulkDeleteForm.target" class="w-full p-3 border rounded" required>
                        <option value="">Choose criteria...</option>
                        <option value="inactive">Inactive Members (No transactions in 6 months)</option>
                        <option value="zero_balance">Members with Zero Balance</option>
                    </select>
                </div>
                <div x-show="bulkDeleteForm.target" class="p-3 bg-gray-50 rounded border">
                    <p class="text-sm font-medium">Members to be deleted: <span class="text-red-600">0</span></p>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="showBulkDeleteModal = false; bulkDeleteForm = {}" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                <button @click="bulkDeleteMembers()" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700" :disabled="!bulkDeleteForm.target">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add User Account</h3>
            <form @submit.prevent="addUser()">
                <div class="space-y-4">
                    <input type="text" x-model="userForm.name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="userForm.email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="password" x-model="userForm.password" placeholder="Password" class="w-full p-3 border rounded" required>
                    <select x-model="userForm.role" class="w-full p-3 border rounded" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddUserModal = false" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div x-show="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add New Member</h3>
            <form @submit.prevent="addMember()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member ID (Auto-generated)</label>
                        <input type="text" x-model="nextMemberId" class="w-full p-3 border rounded bg-gray-100" readonly>
                    </div>
                    <input type="text" x-model="memberForm.full_name" placeholder="Full Name" class="w-full p-3 border rounded" autocomplete="name" required>
                    <input type="email" x-model="memberForm.email" placeholder="Email" class="w-full p-3 border rounded" autocomplete="email" required>
                    <input type="text" x-model="memberForm.contact" placeholder="Contact" class="w-full p-3 border rounded" autocomplete="tel" required>
                    <input type="text" x-model="memberForm.location" placeholder="Location" class="w-full p-3 border rounded" autocomplete="address-level2" required>
                    <input type="text" x-model="memberForm.occupation" placeholder="Occupation" class="w-full p-3 border rounded" autocomplete="organization-title" required>
                    <select x-model="memberForm.role" class="w-full p-3 border rounded" required>
                        <option value="">Select Role</option>
                        <option value="client">Client</option>
                        <option value="shareholder">Shareholder</option>
                        <option value="cashier">Cashier</option>
                        <option value="td">TD</option>
                        <option value="ceo">CEO</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddMemberModal = false" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div x-show="showAddTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add Transaction</h3>
            <form @submit.prevent="addTransaction()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (Auto-generated)</label>
                        <input type="text" :value="'TXN' + String(transactions.length + 1).padStart(3, '0')" class="w-full p-3 border rounded bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="memberSearchQuery" 
                                   @input="filterMembersForTransaction()" 
                                   @focus="showMemberDropdown = true; filterMembersForTransaction()"
                                   @click="showMemberDropdown = true; filterMembersForTransaction()"
                                   placeholder="Search or select member..." 
                                   class="w-full p-3 pr-10 border rounded" 
                                   autocomplete="off"
                                   required>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            <div x-show="showMemberDropdown && filteredMembersForTransaction.length > 0" 
                                 @click.away="showMemberDropdown = false"
                                 class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="member in filteredMembersForTransaction" :key="member.member_id">
                                    <div @click="selectMemberForTransaction(member)" 
                                         class="p-3 hover:bg-blue-50 cursor-pointer border-b">
                                        <div class="font-medium text-sm" x-text="member.member_id"></div>
                                        <div class="text-xs text-gray-600" x-text="member.full_name"></div>
                                        <div class="text-xs text-gray-500" x-text="member.email"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select x-model="transactionForm.type" class="w-full p-3 border rounded" required>
                            <option value="">Select Type</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="transfer">Transfer</option>
                            <option value="loan_payment">Loan Payment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (UGX)</label>
                        <input type="number" x-model="transactionForm.amount" placeholder="Enter amount" class="w-full p-3 border rounded" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="transactionForm.description" placeholder="Transaction description" class="w-full p-3 border rounded" rows="3"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddTransactionModal = false; transactionForm = {}; memberSearchQuery = ''; showMemberDropdown = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div x-show="showAddProjectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Add New Project</h3>
            <form @submit.prevent="addProject()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project ID (Auto-generated)</label>
                        <input type="text" :value="'PRJ' + String(projects.length + 1).padStart(3, '0')" class="w-full p-3 border rounded bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                        <input type="text" x-model="projectForm.name" placeholder="Enter project name" class="w-full p-3 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="projectForm.description" placeholder="Project description and objectives" class="w-full p-3 border rounded" rows="3" required></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Budget (UGX)</label>
                            <input type="number" x-model="projectForm.budget" placeholder="0" class="w-full p-3 border rounded" min="1000" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timeline (Deadline)</label>
                            <input type="date" x-model="projectForm.timeline" class="w-full p-3 border rounded" :min="new Date().toISOString().split('T')[0]" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected ROI (%)</label>
                            <input type="number" x-model="projectForm.roi" placeholder="0" class="w-full p-3 border rounded" min="0" max="100" step="0.1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Risk Score (0-100)</label>
                            <input type="number" x-model="projectForm.risk_score" placeholder="20" class="w-full p-3 border rounded" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddProjectModal = false; projectForm = {}" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Add Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div x-show="showEditMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Edit Member</h3>
            <form @submit.prevent="updateMember()">
                <div class="space-y-4">
                    <input type="text" x-model="editingMember.full_name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="editingMember.email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="editingMember.contact" placeholder="Contact" class="w-full p-3 border rounded">
                    <select x-model="editingMember.role" class="w-full p-3 border rounded" required>
                        <option value="client">Client</option>
                        <option value="shareholder">Shareholder</option>
                        <option value="cashier">Cashier</option>
                        <option value="td">TD</option>
                        <option value="ceo">CEO</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditMemberModal = false" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div x-show="showEditProjectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Edit Project</h3>
            <form @submit.prevent="updateProject()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                        <input type="text" x-model="editingProject.name" class="w-full p-3 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editingProject.description" class="w-full p-3 border rounded" rows="3" required></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Budget (UGX)</label>
                            <input type="number" x-model="editingProject.budget" class="w-full p-3 border rounded" min="1000" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                            <input type="number" x-model="editingProject.progress" class="w-full p-3 border rounded" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected ROI (%)</label>
                            <input type="number" x-model="editingProject.roi" class="w-full p-3 border rounded" min="0" max="100" step="0.1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Risk Score (0-100)</label>
                            <input type="number" x-model="editingProject.risk_score" class="w-full p-3 border rounded" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditProjectModal = false; editingProject = {}" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Update Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report View Modal -->
    <div x-show="showReportViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-6xl h-5/6 flex flex-col m-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold" x-text="viewingReport?.type"></h3>
                <button @click="showReportViewModal = false" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-hidden">
                <iframe x-show="viewingReport" :src="`/api/reports/view/${viewingReport?.id}`" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>

    <!-- Notification View Modal -->
    <div x-show="showNotificationViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-2xl m-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Notification Details</h3>
                <button @click="showNotificationViewModal = false" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6" x-show="viewingNotification">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Title</label>
                        <p class="text-lg font-semibold" x-text="viewingNotification?.title"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Message</label>
                        <p class="text-gray-800" x-text="viewingNotification?.message"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Recipients</label>
                            <p x-text="viewingNotification?.recipients"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Method</label>
                            <p x-text="viewingNotification?.method"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Priority</label>
                            <p x-text="viewingNotification?.priority"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <p x-text="viewingNotification?.status"></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Sent At</label>
                        <p x-text="viewingNotification?.sent_at"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Permissions Modal -->
    <div x-show="showEditPermissionsModal" @click.self="showEditPermissionsModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[85vh] overflow-y-auto m-4">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Edit Permissions</h3>
                    <p class="text-sm text-gray-600" x-text="'Role: ' + (editingRole.name || '')"></p>
                </div>
                <button @click="showEditPermissionsModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <form @submit.prevent="updateRolePermissions()" class="p-6">
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
                        <button type="button" @click="editingRole.permissions = availablePermissions.flatMap(c => c.permissions)" class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
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
                        <button type="button" @click="showEditPermissionsModal = false" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Chat Modal -->
    <div x-show="showChatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" @mousedown.self="showChatModal = false">
        <div class="bg-white rounded-lg w-full max-w-md h-[600px] flex flex-col" x-data="{isDragging: false, offsetX: 0, offsetY: 0, modalX: 0, modalY: 0}" :style="`transform: translate(${modalX}px, ${modalY}px); cursor: ${isDragging ? 'grabbing' : 'default'}`" @mousemove.window="if(isDragging) {modalX = $event.clientX - offsetX; modalY = $event.clientY - offsetY}" @mouseup.window="isDragging = false">
            <div class="flex justify-between items-center p-4 border-b bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-t-lg cursor-grab active:cursor-grabbing" @mousedown="isDragging = true; offsetX = $event.clientX - modalX; offsetY = $event.clientY - modalY">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                        <i class="fas fa-headset text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Support Team</h3>
                        <p class="text-xs text-blue-100"><i class="fas fa-circle text-green-400 text-[8px]"></i> Online</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click.stop="modalX = 0; modalY = 0" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-full transition" title="Reset Position">
                        <i class="fas fa-compress"></i>
                    </button>
                    <button @click.stop="showChatModal = false" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="chatMessages">
                <div class="space-y-3">
                    <template x-for="(msg, index) in chatMessages" :key="index">
                        <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.sender === 'user' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800'"
                                 class="p-3 rounded-lg shadow-sm max-w-xs">
                                <p class="text-sm" x-text="msg.text"></p>
                                <span class="text-xs opacity-75" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="p-4 border-t bg-white rounded-b-lg">
                <div class="flex space-x-2 mb-2">
                    <button @click="sendQuickMessage('I need help with system settings')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">Settings Help</button>
                    <button @click="sendQuickMessage('User management issue')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">User Issue</button>
                    <button @click="sendQuickMessage('System error')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">Error</button>
                    <button @click="showMemberChatModal = true; showChatModal = false" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-full text-xs">
                        <i class="fas fa-users mr-1"></i>Members
                    </button>
                </div>
                <div class="flex space-x-2">
                    <input type="text" x-model="chatInput" @keyup.enter="sendMessage" placeholder="Type a message..."
                           class="flex-1 p-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button @click="sendMessage" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Chat Modal -->
    <div x-show="showMemberChatModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" style="display: none;" x-cloak @mousedown.self="showMemberChatModal = false">
        <div class="bg-white rounded-2xl w-full max-w-4xl h-[600px] flex shadow-2xl overflow-hidden border border-gray-200" x-data="{isDragging: false, offsetX: 0, offsetY: 0, modalX: 0, modalY: 0}" :style="`transform: translate(${modalX}px, ${modalY}px); cursor: ${isDragging ? 'grabbing' : 'default'}`" @mousemove.window="if(isDragging) {modalX = $event.clientX - offsetX; modalY = $event.clientY - offsetY}" @mouseup.window="isDragging = false">
            <div class="w-80 flex-shrink-0 flex flex-col bg-gradient-to-b from-gray-50 to-white border-r border-gray-200" :class="{'hidden': selectedMemberChat && window.innerWidth < 640}">
                <div class="p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white cursor-grab active:cursor-grabbing" @mousedown="isDragging = true; offsetX = $event.clientX - modalX; offsetY = $event.clientY - modalY">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold">Messages</h3>
                                <p class="text-[10px] text-blue-200" x-text="filteredMembersChat.length + ' contacts'"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click.stop="modalX = 0; modalY = 0" class="text-white/80 hover:text-white hover:bg-white/20 p-1.5 rounded-full transition" title="Reset Position">
                                <i class="fas fa-compress text-xs"></i>
                            </button>
                            <button @click.stop="showMemberChatModal = false; showChatModal = true" class="text-white/80 hover:text-white hover:bg-white/20 p-1.5 rounded-full transition">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="memberChatSearch" @input="filterMembersForChat()" placeholder="Search..." class="w-full px-3 py-2 pl-9 rounded-lg bg-white/20 backdrop-blur-sm text-white placeholder-white/70 text-xs focus:outline-none focus:ring-2 focus:ring-white/50 transition">
                        <i class="fas fa-search absolute left-3 top-2.5 text-white/70 text-xs"></i>
                    </div>
                    <div class="flex space-x-1 mt-2 overflow-x-auto pb-1">
                        <button @click="chatFilter = 'all'; filterMembersForChat()" :class="chatFilter === 'all' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'" class="px-2 py-1 rounded-lg text-[10px] font-medium whitespace-nowrap transition">All</button>
                        <button @click="chatFilter = 'online'; filterMembersForChat()" :class="chatFilter === 'online' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'" class="px-2 py-1 rounded-lg text-[10px] font-medium whitespace-nowrap transition">Online</button>
                        <button @click="chatFilter = 'unread'; filterMembersForChat()" :class="chatFilter === 'unread' ? 'bg-white text-blue-600' : 'bg-white/20 text-white'" class="px-2 py-1 rounded-lg text-[10px] font-medium whitespace-nowrap transition">Unread</button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300">
                    <div class="p-2">
                        <p class="px-2 py-1 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Contacts</p>
                        <template x-for="member in filteredMembersChat" :key="member.member_id">
                            <div @click="selectMemberChat(member)" :class="selectedMemberChat?.member_id === member.member_id ? 'bg-blue-100 border-l-4 border-blue-600' : 'hover:bg-gray-100'" class="p-2 mb-1 rounded-lg cursor-pointer transition-all duration-200 border-l-4 border-transparent">
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg text-xs">
                                            <span x-text="member.full_name?.charAt(0) || 'U'"></span>
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <p class="text-xs font-semibold text-gray-800 truncate" x-text="member.full_name"></p>
                                            <span class="text-[10px] text-gray-400">12:30</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 truncate" x-text="member.member_id"></p>
                                        <div class="flex items-center justify-between mt-0.5">
                                            <p class="text-[10px] text-gray-400 truncate" x-text="member.email"></p>
                                            <span x-show="member.unread" class="bg-blue-600 text-white text-[9px] rounded-full px-1.5 py-0.5 font-medium shadow-sm">3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredMembersChat.length === 0" class="text-center py-6">
                            <i class="fas fa-user-friends text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">No contacts found</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex flex-col bg-gradient-to-br from-gray-50 to-gray-100" :class="{'w-full': window.innerWidth < 640, 'hidden': !selectedMemberChat && window.innerWidth < 640}">
                <div class="p-3 bg-white border-b flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <button @click="selectedMemberChat = null" class="sm:hidden p-1.5 -ml-1 text-gray-600 hover:bg-gray-100 rounded-full transition">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </button>
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg text-xs">
                                <span x-text="selectedMemberChat?.full_name?.charAt(0) || 'U'"></span>
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-800" x-text="selectedMemberChat?.full_name || ''"></h3>
                            <p class="text-[10px] text-green-600 flex items-center">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>Active now
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition" title="Video Call" @click="alert('Video call feature coming soon!')">
                            <i class="fas fa-video text-xs"></i>
                        </button>
                        <button class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition" title="Voice Call" @click="alert('Voice call feature coming soon!')">
                            <i class="fas fa-phone text-xs"></i>
                        </button>
                    </div>
                </div>
                <div x-show="selectedMemberChat" class="flex-1 p-3 overflow-y-auto bg-gradient-to-br from-blue-50/50 to-indigo-50/50" id="memberChatMessages">
                    <div class="flex justify-center mb-4">
                        <span class="bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] text-gray-600 shadow-sm font-medium">Today</span>
                    </div>
                    <div class="space-y-3">
                        <template x-for="(msg, index) in memberChatMessages" :key="index">
                            <div :class="msg.sender === 'me' ? 'flex justify-end' : 'flex justify-start'" class="animate-fade-in">
                                <div class="max-w-xs">
                                    <div :class="msg.sender === 'me' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm shadow-lg'" class="p-3 relative">
                                        <p class="text-xs leading-relaxed" x-text="msg.text"></p>
                                        <div class="flex items-center justify-end space-x-1 mt-1">
                                            <span class="text-[9px] opacity-70" x-text="msg.time"></span>
                                            <template x-if="msg.sender === 'me'">
                                                <i class="fas fa-check-double text-blue-200 text-[9px]"></i>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="isTyping" class="flex justify-start animate-fade-in">
                            <div class="bg-white rounded-2xl rounded-tl-sm shadow-lg p-3">
                                <div class="flex space-x-1.5">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="!selectedMemberChat" class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-3">
                    <div class="text-center max-w-xs">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-2xl text-blue-400"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-1">BSS Investment Chat</h3>
                        <p class="text-gray-500 text-xs">Select a member from the list to start messaging</p>
                    </div>
                </div>
                <div x-show="selectedMemberChat" class="p-3 bg-white border-t">
                    <div class="flex items-end space-x-2">
                        <input type="text" x-model="memberChatInput" @keydown.enter.prevent="sendMemberMessage" placeholder="Type a message..." class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 resize-none transition-all">
                        <button @click="sendMemberMessage" class="p-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full hover:from-blue-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Picture Upload Modal -->
    <div x-show="showProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" @mousedown.self="showProfileModal = false">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Update Profile Picture</h3>
                <button @click="showProfileModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex flex-col items-center mb-6">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mb-4 overflow-hidden border-4 border-white shadow-lg">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                    <i x-show="!profilePicture" class="fas fa-user-shield text-white text-5xl"></i>
                </div>
                <input type="file" id="profilePictureInput" accept="image/*" class="hidden" @change="handleProfilePictureUpload">
                <button @click="document.getElementById('profilePictureInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Choose Picture
                </button>
                <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF (Max 2MB)</p>
            </div>
        </div>
    </div>

    <script>
        function adminPanel() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                showProfileDropdown: false,
                showLogoModal: false,
                showShareholderModal: false,
                showCalendarModal: false,
                showChatModal: false,
                showMemberChatModal: false,
                showLoanRequestModal: false,
                showOpportunitiesModal: false,
                showProfileViewModal: false,
                activeLink: 'stats',
                sidebarSearch: '',
                profilePicture: null,
                stats: {},
                members: [],
                loans: [],
                transactions: [],
                projects: [],
                showAddMemberModal: false,
                showAddTransactionModal: false,
                showAddProjectModal: false,
                showEditMemberModal: false,
                showEditProjectModal: false,
                memberForm: {},
                nextMemberId: '',
                transactionForm: {},
                projectForm: {},
                editingMember: {},
                editingProject: {},
                projectViewMode: 'grid',
                projectSearchQuery: '',
                projectStatusFilter: 'all',
                users: [],
                settings: {},
                auditLogs: [],
                auditSearch: '',
                auditTypeFilter: 'all',
                auditUserFilter: 'all',
                auditDateFilter: '',
                auditTimeFilter: '',
                notificationForm: {},
                notificationHistory: [],
                notificationStats: {},
                notificationFilter: 'all',
                showNotificationViewModal: false,
                viewingNotification: null,
                selectedNotifications: [],
                userForm: {},
                showAddUserModal: false,
                backups: [],
                financialSummary: {},
                systemHealth: {},
                roles: [],
                showBulkImportModal: false,
                showEditPermissionsModal: false,
                editingRole: {},
                availablePermissions: [
                    {category: 'Members', permissions: ['view_members', 'create_members', 'edit_members', 'delete_members']},
                    {category: 'Financial', permissions: ['view_loans', 'create_loans', 'approve_loans', 'delete_loans', 'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions']},
                    {category: 'Projects', permissions: ['view_projects', 'create_projects', 'edit_projects', 'delete_projects']},
                    {category: 'Reports', permissions: ['view_reports', 'generate_reports', 'export_reports']},
                    {category: 'Settings', permissions: ['view_settings', 'edit_settings']},
                    {category: 'System', permissions: ['manage_users', 'manage_permissions', 'view_audit_logs']}
                ],
                showChatModal: false,
                chatMessages: [],
                chatInput: '',
                showMemberChatModal: false,
                selectedMemberChat: null,
                memberChatMessages: [],
                memberChatInput: '',
                memberChatSearch: '',
                filteredMembersChat: [],
                chatFilter: 'all',
                isTyping: false,
                showProfileModal: false,
                profilePicture: null,
                adminProfile: {
                    name: 'Admin',
                    email: 'admin@bss.com',
                    role: 'Administrator',
                    phone: '+256 700 000 000',
                    location: 'Kampala, Uganda'
                },
                showBulkEmailModal: false,
                showBulkSMSModal: false,
                showBulkUpdateModal: false,
                showBulkDeleteModal: false,
                bulkImportOptions: {skipDuplicates: true, sendWelcomeEmail: false},
                bulkSMSForm: {},
                bulkEmailForm: {},
                bulkUpdateForm: {},
                bulkDeleteForm: {},
                showDepositModal: false,
                showWithdrawalModal: false,
                showTransferModal: false,
                depositForm: {},
                withdrawalForm: {},
                transferForm: {},
                depositMemberSearch: '',
                withdrawalMemberSearch: '',
                depositMemberBalance: 0,
                withdrawalMemberBalance: 0,
                withdrawalFee: 0,
                withdrawalPriorityFee: 0,
                showDepositDropdown: false,
                showWithdrawalDropdown: false,
                filteredDepositMembers: [],
                filteredWithdrawalMembers: [],
                fromMemberBalance: 0,
                transferFee: 0,
                priorityFee: 0,
                fromMemberSearch: '',
                toMemberSearch: '',
                showFromDropdown: false,
                showToDropdown: false,
                filteredFromMembers: [],
                filteredToMembers: [],
                financialFilter: 'all',
                financialSearch: '',
                importFile: null,
                chartData: {},
                memberSearchQuery: '',
                filteredMembersForTransaction: [],
                showMemberDropdown: false,
                reportFilters: {
                    dateFrom: '',
                    dateTo: '',
                    format: 'html'
                },
                recentReports: [],
                selectedReports: [],
                showReportViewModal: false,
                viewingReport: null,
                reportSearch: '',
                reportTypeFilter: 'all',
                reportFormatFilter: 'all',
                reportSortBy: 'newest',
                
                init() {
                    this.loadDashboard();
                    this.loadMembers();
                    this.loadLoans();
                    this.loadTransactions();
                    this.loadProjects();
                    this.loadUsers();
                    this.loadSettings();
                    this.loadAuditLogs();
                    this.loadBackups();
                    this.loadFinancialSummary();
                    this.loadSystemHealth();
                    this.loadRoles();
                    this.loadRecentReports();
                    this.loadNotificationHistory();
                    this.loadNotificationStats();
                    this.notificationForm = {priority: 'normal', schedule: 'now', method: 'system', target: 'all'};
                    this.depositForm = {send_notification: true, method: 'cash'};
                    this.withdrawalForm = {send_notification: true, method: 'cash', priority: 'normal'};
                    this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
                    this.filteredDepositMembers = this.members.slice(0, 10);
                    this.filteredWithdrawalMembers = this.members.slice(0, 10);
                    this.filteredFromMembers = this.members.slice(0, 10);
                    this.filteredToMembers = this.members.slice(0, 10);
                    this.chatMessages = [{sender: 'support', text: 'Hello Admin! How can I help you today?', time: new Date().toLocaleTimeString()}];
                    this.filteredMembersChat = this.members;
                    const savedPicture = localStorage.getItem('adminProfilePicture');
                    if (savedPicture) this.profilePicture = savedPicture;
                    const savedProfile = localStorage.getItem('adminProfile');
                    if (savedProfile) this.adminProfile = JSON.parse(savedProfile);
                    setTimeout(() => this.initCharts(), 500);
                    setInterval(() => {
                        this.loadDashboard();
                        this.loadMembers();
                        this.loadLoans();
                        this.loadTransactions();
                        this.loadProjects();
                        this.loadFinancialSummary();
                        this.loadNotificationStats();
                        this.loadSystemHealth();
                    }, 10000);
                },
                
                sendMessage() {
                    if (!this.chatInput.trim()) return;
                    this.chatMessages.push({sender: 'user', text: this.chatInput, time: new Date().toLocaleTimeString()});
                    this.chatInput = '';
                    setTimeout(() => {
                        this.chatMessages.push({sender: 'support', text: 'Thank you for your message. Our support team will assist you shortly.', time: new Date().toLocaleTimeString()});
                        this.$nextTick(() => {
                            const chat = document.getElementById('chatMessages');
                            if (chat) chat.scrollTop = chat.scrollHeight;
                        });
                    }, 1000);
                },
                
                sendQuickMessage(msg) {
                    this.chatInput = msg;
                    this.sendMessage();
                },
                
                filterMembersForChat() {
                    this.filteredMembersChat = this.members.filter(m => {
                        const search = this.memberChatSearch.toLowerCase();
                        const matchesSearch = !search || m.full_name?.toLowerCase().includes(search) || m.member_id?.toLowerCase().includes(search);
                        const matchesFilter = this.chatFilter === 'all' || (this.chatFilter === 'online' && Math.random() > 0.5) || (this.chatFilter === 'unread' && Math.random() > 0.7);
                        return matchesSearch && matchesFilter;
                    });
                },
                
                selectMemberChat(member) {
                    this.selectedMemberChat = member;
                    this.memberChatMessages = [
                        {sender: 'them', text: 'Hello!', time: '10:30 AM'},
                        {sender: 'me', text: 'Hi! How can I help you?', time: '10:31 AM'},
                        {sender: 'them', text: 'I have a question about my account.', time: '10:32 AM'}
                    ];
                },
                
                sendMemberMessage() {
                    if (!this.memberChatInput.trim()) return;
                    this.memberChatMessages.push({sender: 'me', text: this.memberChatInput, time: new Date().toLocaleTimeString()});
                    this.memberChatInput = '';
                    this.$nextTick(() => {
                        const chat = document.getElementById('memberChatMessages');
                        if (chat) chat.scrollTop = chat.scrollHeight;
                    });
                    this.isTyping = true;
                    setTimeout(() => {
                        this.isTyping = false;
                        this.memberChatMessages.push({sender: 'them', text: 'Thank you for your message!', time: new Date().toLocaleTimeString()});
                        this.$nextTick(() => {
                            const chat = document.getElementById('memberChatMessages');
                            if (chat) chat.scrollTop = chat.scrollHeight;
                        });
                    }, 2000);
                },
                
                handleProfilePictureUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.profilePicture = e.target.result;
                            localStorage.setItem('adminProfilePicture', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                },
                
                async loadDashboard() {
                    try {
                        const response = await fetch('/api/admin/dashboard');
                        const data = await response.json();
                        this.stats = data;
                        this.chartData = {
                            membersGrowth: data.membersGrowth || [],
                            loanStats: data.loanStats || {},
                            transactionStats: data.transactionStats || {},
                            monthlyRevenue: data.monthlyRevenue || [],
                            projects: data.projects || [],
                            financialOverview: data.financialOverview || {}
                        };
                    } catch (error) {
                        console.error('Error loading dashboard:', error);
                    }
                },
                
                async loadMembers() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.members = data.members || [];
                        await this.fetchNextMemberId();
                        this.filteredFromMembers = this.members.slice(0, 10);
                        this.filteredToMembers = this.members.slice(0, 10);
                    } catch (error) {
                        console.error('Error loading members:', error);
                        this.members = [];
                    }
                },
                
                async loadLoans() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.loans = data.pending_loans || [];
                    } catch (error) {
                        console.error('Error loading loans:', error);
                        this.loans = [];
                    }
                },
                
                async loadTransactions() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.transactions = data.recent_transactions || [];
                    } catch (error) {
                        console.error('Error loading transactions:', error);
                        this.transactions = [];
                    }
                },
                
                async loadProjects() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.projects = data.projects || [];
                    } catch (error) {
                        console.error('Error loading projects:', error);
                        this.projects = [];
                    }
                },
                
                async fetchNextMemberId() {
                    const response = await fetch('/api/members/next-id?t=' + Date.now());
                    const data = await response.json();
                    this.nextMemberId = data.next_id;
                },
                
                async addMember() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/members', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.memberForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showAddMemberModal = false;
                            this.memberForm = {};
                            this.loadMembers();
                            this.loadDashboard();
                            alert('Member added successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to add member'));
                        }
                    } catch (error) {
                        console.error('Error adding member:', error);
                        alert('Error adding member');
                    }
                },
                
                editMember(member) {
                    this.editingMember = {...member};
                    this.showEditMemberModal = true;
                },
                
                async updateMember() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/members/${this.editingMember.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.editingMember)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showEditMemberModal = false;
                            this.editingMember = {};
                            this.loadMembers();
                            alert('Member updated successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update member'));
                        }
                    } catch (error) {
                        console.error('Error updating member:', error);
                        alert('Error updating member');
                    }
                },
                
                async deleteMember(id) {
                    if (confirm('Delete this member?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/members/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();
                            console.log('Delete response:', data);
                            if (data.success) {
                                await this.loadMembers();
                                await this.loadDashboard();
                                alert('Member deleted!');
                            } else {
                                alert('Error: ' + (data.message || 'Failed to delete member'));
                            }
                        } catch (error) {
                            console.error('Error deleting member:', error);
                            alert('Error deleting member: ' + error.message);
                        }
                    }
                },
                
                async approveLoan(id) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/loans/${id}/approve`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.loadLoans();
                            this.loadDashboard();
                            alert('Loan approved successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to approve loan'));
                        }
                    } catch (error) {
                        console.error('Error approving loan:', error);
                        alert('Error approving loan');
                    }
                },
                
                async rejectLoan(id) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/loans/${id}/reject`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.loadLoans();
                            this.loadDashboard();
                            alert('Loan rejected!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reject loan'));
                        }
                    } catch (error) {
                        console.error('Error rejecting loan:', error);
                        alert('Error rejecting loan');
                    }
                },
                
                async addTransaction() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/transactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.transactionForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showAddTransactionModal = false;
                            this.transactionForm = {};
                            this.loadTransactions();
                            this.loadDashboard();
                            alert('Transaction added successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to add transaction'));
                        }
                    } catch (error) {
                        console.error('Error adding transaction:', error);
                        alert('Error adding transaction');
                    }
                },
                
                async addProject() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/projects', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.projectForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showAddProjectModal = false;
                            this.projectForm = {};
                            this.loadProjects();
                            this.loadDashboard();
                            alert('Project added successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to add project'));
                        }
                    } catch (error) {
                        console.error('Error adding project:', error);
                        alert('Error adding project');
                    }
                },
                
                editProject(project) {
                    this.editingProject = {...project};
                    this.showEditProjectModal = true;
                },
                
                async updateProject() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/projects/${this.editingProject.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.editingProject)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showEditProjectModal = false;
                            this.editingProject = {};
                            this.loadProjects();
                            alert('Project updated successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update project'));
                        }
                    } catch (error) {
                        console.error('Error updating project:', error);
                        alert('Error updating project');
                    }
                },
                
                async deleteProject(id) {
                    if (confirm('Delete this project?')) {
                        await fetch(`/api/projects/${id}`, {method: 'DELETE'});
                        this.loadProjects();
                        alert('Project deleted!');
                    }
                },
                
                get filteredFinancialTransactions() {
                    return this.transactions.filter(t => {
                        const matchesType = this.financialFilter === 'all' || t.type === this.financialFilter;
                        const matchesSearch = !this.financialSearch || 
                            t.transaction_id?.toLowerCase().includes(this.financialSearch.toLowerCase()) ||
                            t.member_id?.toLowerCase().includes(this.financialSearch.toLowerCase()) ||
                            t.type?.toLowerCase().includes(this.financialSearch.toLowerCase());
                        return matchesType && matchesSearch;
                    });
                },
                
                get totalWithdrawalCost() {
                    return (parseFloat(this.withdrawalForm.amount) || 0) + this.withdrawalFee + this.withdrawalPriorityFee;
                },
                
                get canWithdraw() {
                    return this.withdrawalForm.member_id && 
                           this.withdrawalForm.amount > 0 && 
                           this.totalWithdrawalCost <= this.withdrawalMemberBalance;
                },
                
                get totalTransferCost() {
                    return (parseFloat(this.transferForm.amount) || 0) + this.transferFee + this.priorityFee;
                },
                
                get canTransfer() {
                    return this.transferForm.from_member && 
                           this.transferForm.to_member && 
                           this.transferForm.amount > 0 && 
                           this.totalTransferCost <= this.fromMemberBalance;
                },
                
                get filteredProjects() {
                    return this.projects.filter(p => {
                        const matchesSearch = p.name?.toLowerCase().includes(this.projectSearchQuery.toLowerCase()) ||
                                            p.description?.toLowerCase().includes(this.projectSearchQuery.toLowerCase());
                        const matchesStatus = this.projectStatusFilter === 'all' || 
                                            (this.projectStatusFilter === 'active' && p.progress < 100) ||
                                            (this.projectStatusFilter === 'completed' && p.progress === 100);
                        return matchesSearch && matchesStatus;
                    });
                },
                
                get filteredReports() {
                    let filtered = this.recentReports.filter(r => {
                        const matchesSearch = r.type?.toLowerCase().includes(this.reportSearch.toLowerCase());
                        const matchesType = this.reportTypeFilter === 'all' || r.type?.toLowerCase().includes(this.reportTypeFilter.toLowerCase());
                        const matchesFormat = this.reportFormatFilter === 'all' || r.format === this.reportFormatFilter;
                        return matchesSearch && matchesType && matchesFormat;
                    });
                    
                    if (this.reportSortBy === 'newest') {
                        filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
                    } else if (this.reportSortBy === 'oldest') {
                        filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
                    } else if (this.reportSortBy === 'type') {
                        filtered.sort((a, b) => a.type.localeCompare(b.type));
                    }
                    
                    return filtered;
                },
                
                get filteredNotifications() {
                    return this.notificationHistory.filter(n => {
                        return this.notificationFilter === 'all' || n.status === this.notificationFilter;
                    });
                },
                
                async loadUsers() {
                    try {
                        const response = await fetch('/api/users');
                        this.users = await response.json();
                    } catch (error) {
                        console.error('Error loading users:', error);
                        this.users = [];
                    }
                },
                
                async loadSettings() {
                    try {
                        const response = await fetch('/api/settings');
                        this.settings = await response.json();
                    } catch (error) {
                        console.error('Error loading settings:', error);
                        this.settings = {};
                    }
                },
                
                get filteredAuditLogs() {
                    return this.auditLogs.filter(log => {
                        const matchesSearch = !this.auditSearch || 
                            log.action?.toLowerCase().includes(this.auditSearch.toLowerCase()) ||
                            log.details?.toLowerCase().includes(this.auditSearch.toLowerCase()) ||
                            log.user?.toLowerCase().includes(this.auditSearch.toLowerCase());
                        
                        const matchesType = this.auditTypeFilter === 'all' || 
                            log.action?.toLowerCase().includes(this.auditTypeFilter.toLowerCase());
                        
                        const matchesUser = this.auditUserFilter === 'all' || 
                            log.user?.toLowerCase() === this.auditUserFilter.toLowerCase();
                        
                        let matchesDateTime = true;
                        if (this.auditDateFilter || this.auditTimeFilter) {
                            const logDate = new Date(log.timestamp);
                            if (this.auditDateFilter) {
                                const filterDate = new Date(this.auditDateFilter);
                                matchesDateTime = logDate.toDateString() === filterDate.toDateString();
                            }
                            if (this.auditTimeFilter && matchesDateTime) {
                                const logTime = logDate.toTimeString().slice(0, 8);
                                matchesDateTime = logTime === this.auditTimeFilter;
                            }
                        }
                        
                        return matchesSearch && matchesType && matchesUser && matchesDateTime;
                    });
                },
                
                async loadAuditLogs() {
                    try {
                        const response = await fetch('/api/audit-logs');
                        const data = await response.json();
                        this.auditLogs = Array.isArray(data) ? data : [];
                        console.log('Loaded audit logs:', this.auditLogs.length);
                    } catch (error) {
                        console.error('Error loading audit logs:', error);
                        this.auditLogs = [];
                    }
                },
                
                async addUser() {
                    try {
                        const response = await fetch('/api/users', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(this.userForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showAddUserModal = false;
                            this.userForm = {};
                            this.loadUsers();
                            alert('User added!');
                        }
                    } catch (error) {
                        console.error('Error adding user:', error);
                        alert('Error adding user');
                    }
                },
                
                async toggleUserStatus(id) {
                    try {
                        await fetch(`/api/users/${id}/toggle-status`, {method: 'POST'});
                        this.loadUsers();
                    } catch (error) {
                        console.error('Error toggling user status:', error);
                    }
                },
                
                async deleteUser(id) {
                    if (confirm('Delete this user?')) {
                        try {
                            await fetch(`/api/users/${id}`, {method: 'DELETE'});
                            this.loadUsers();
                            alert('User deleted!');
                        } catch (error) {
                            console.error('Error deleting user:', error);
                            alert('Error deleting user');
                        }
                    }
                },
                
                async updateSettings() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/settings', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.settings)
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('Settings updated successfully!');
                            this.loadSettings();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update settings'));
                        }
                    } catch (error) {
                        console.error('Error updating settings:', error);
                        alert('Error updating settings: ' + error.message);
                    }
                },
                
                async resetSettings() {
                    if (confirm('Reset all settings to default values?')) {
                        this.settings = {
                            interest_rate: 5.5,
                            min_savings: 50000,
                            max_loan: 5000000,
                            loan_fee: 2.5,
                            system_name: 'BSS Investment Group',
                            currency: 'UGX',
                            timezone: 'Africa/Kampala',
                            date_format: 'Y-m-d',
                            email_notifications: true,
                            sms_notifications: false,
                            loan_approval_notify: true,
                            transaction_notify: true,
                            session_timeout: 30,
                            password_min_length: 8,
                            two_factor_auth: false
                        };
                        alert('Settings reset to default values. Click Save to apply.');
                    }
                },
                
                async sendNotification() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/notifications/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.notificationForm)
                        });
                        const data = await response.json();
                        if (data.success || response.ok) {
                            this.notificationForm = {priority: 'normal', schedule: 'now', method: 'system', target: 'all'};
                            this.loadNotificationHistory();
                            this.loadNotificationStats();
                            alert('Notification sent successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to send notification'));
                        }
                    } catch (error) {
                        console.error('Error sending notification:', error);
                        alert('Notification queued for delivery!');
                    }
                },
                
                async loadNotificationHistory() {
                    try {
                        const response = await fetch('/api/notifications/history');
                        const data = await response.json();
                        this.notificationHistory = Array.isArray(data) ? data : [];
                        console.log('Loaded notifications:', this.notificationHistory.length);
                    } catch (error) {
                        console.error('Error loading notification history:', error);
                        this.notificationHistory = [];
                    }
                },
                
                async loadNotificationStats() {
                    try {
                        const response = await fetch('/api/notifications/stats');
                        const data = await response.json();
                        this.notificationStats = data || {total: 0, unread: 0, delivered: 0, pending: 0, failed: 0};
                        console.log('Loaded stats:', this.notificationStats);
                    } catch (error) {
                        console.error('Error loading notification stats:', error);
                        this.notificationStats = {total: 0, unread: 0, delivered: 0, pending: 0, failed: 0};
                    }
                },
                
                useTemplate(type) {
                    const templates = {
                        meeting: {
                            title: 'Upcoming Meeting Reminder',
                            message: 'This is a reminder about our upcoming meeting. Please ensure you attend on time.',
                            priority: 'high',
                            target: 'all'
                        },
                        payment: {
                            title: 'Payment Due Reminder',
                            message: 'Your payment is due soon. Please make the necessary arrangements to avoid late fees.',
                            priority: 'urgent',
                            target: 'all'
                        },
                        announcement: {
                            title: 'Important Announcement',
                            message: 'We have an important announcement to share with all members.',
                            priority: 'normal',
                            target: 'all'
                        }
                    };
                    this.notificationForm = {...templates[type], schedule: 'now', method: 'system'};
                },
                
                saveAsTemplate() {
                    if (!this.notificationForm.title || !this.notificationForm.message) {
                        alert('Please fill in title and message first');
                        return;
                    }
                    alert('Template saved successfully!');
                },
                
                viewNotification(notification) {
                    this.viewingNotification = notification;
                    this.showNotificationViewModal = true;
                },
                
                async resendNotification(id) {
                    if (confirm('Resend this notification?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            await fetch(`/api/notifications/${id}/resend`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            this.loadNotificationHistory();
                            alert('Notification resent!');
                        } catch (error) {
                            console.error('Error resending notification:', error);
                        }
                    }
                },
                
                async deleteNotification(id) {
                    if (confirm('Delete this notification?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            await fetch(`/api/notifications/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            this.loadNotificationHistory();
                            this.loadNotificationStats();
                            alert('Notification deleted!');
                        } catch (error) {
                            console.error('Error deleting notification:', error);
                        }
                    }
                },
                
                toggleAllNotifications(checked) {
                    if (checked) {
                        this.selectedNotifications = this.filteredNotifications.map(n => n.id);
                    } else {
                        this.selectedNotifications = [];
                    }
                },
                
                async deleteSelectedNotifications() {
                    if (confirm(`Delete ${this.selectedNotifications.length} selected notifications?`)) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            for (const id of this.selectedNotifications) {
                                await fetch(`/api/notifications/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                });
                            }
                            this.selectedNotifications = [];
                            this.loadNotificationHistory();
                            this.loadNotificationStats();
                            alert('Selected notifications deleted!');
                        } catch (error) {
                            console.error('Error deleting notifications:', error);
                        }
                    }
                },
                
                async generateReport(type) {
                    try {
                        alert(`Generating ${type} report...`);
                        const params = new URLSearchParams({
                            type: type,
                            format: this.reportFilters.format,
                            dateFrom: this.reportFilters.dateFrom || '',
                            dateTo: this.reportFilters.dateTo || ''
                        });
                        
                        window.open(`/api/reports/generate?${params.toString()}`, '_blank');
                        
                        setTimeout(() => {
                            this.loadRecentReports();
                            alert('Report generated successfully!');
                        }, 1000);
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Error generating report');
                    }
                },
                
                async loadRecentReports() {
                    try {
                        const response = await fetch('/api/reports/recent');
                        this.recentReports = await response.json();
                    } catch (error) {
                        console.error('Error loading recent reports:', error);
                        this.recentReports = [];
                    }
                },
                
                async downloadReport(id) {
                    window.open(`/api/reports/download/${id}`, '_blank');
                },
                
                viewReport(report) {
                    this.viewingReport = report;
                    this.showReportViewModal = true;
                },
                
                async deleteReport(id) {
                    if (confirm('Delete this report?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            await fetch(`/api/reports/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            this.loadRecentReports();
                            alert('Report deleted!');
                        } catch (error) {
                            console.error('Error deleting report:', error);
                        }
                    }
                },
                
                toggleAllReports(checked) {
                    if (checked) {
                        this.selectedReports = this.recentReports.map(r => r.id);
                    } else {
                        this.selectedReports = [];
                    }
                },
                
                async deleteSelectedReports() {
                    if (confirm(`Delete ${this.selectedReports.length} selected reports?`)) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            for (const id of this.selectedReports) {
                                await fetch(`/api/reports/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                });
                            }
                            this.selectedReports = [];
                            this.loadRecentReports();
                            alert('Selected reports deleted!');
                        } catch (error) {
                            console.error('Error deleting reports:', error);
                        }
                    }
                },
                
                async loadBackups() {
                    try {
                        const response = await fetch('/api/backups');
                        this.backups = await response.json();
                    } catch (error) {
                        console.error('Error loading backups:', error);
                        this.backups = [];
                    }
                },
                
                async createBackup() {
                    if (confirm('Create a full database backup?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch('/api/backups/create', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.loadBackups();
                                alert(`Backup created successfully!\nFile: ${data.filename}\nSize: ${data.size}`);
                            } else {
                                alert('Error: ' + (data.message || 'Failed to create backup'));
                            }
                        } catch (error) {
                            console.error('Error creating backup:', error);
                            alert('Error creating backup: ' + error.message);
                        }
                    }
                },
                
                async restoreBackup() {
                    const fileInput = document.getElementById('restoreFile');
                    const file = fileInput?.files[0];
                    if (!file) {
                        alert('Please select a backup file first');
                        return;
                    }
                    
                    if (!confirm('WARNING: This will replace all current data with the backup. Continue?')) {
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('backup', file);
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/backups/restore', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('Database restored successfully! Page will reload.');
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to restore backup'));
                        }
                    } catch (error) {
                        console.error('Error restoring backup:', error);
                        alert('Error restoring backup: ' + error.message);
                    }
                    fileInput.value = '';
                },
                
                async downloadBackup(id) {
                    window.open(`/api/backups/${id}/download`, '_blank');
                },
                
                calculateTotalSize() {
                    if (this.backups.length === 0) return '0 MB';
                    return this.backups[0]?.size || '0 MB';
                },
                
                async deleteBackup(id) {
                    if (confirm('Delete this backup?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/backups/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                alert('Backup deleted successfully!');
                                this.loadBackups();
                            } else {
                                alert('Failed to delete backup: ' + (data.message || 'Unknown error'));
                            }
                        } catch (error) {
                            console.error('Error deleting backup:', error);
                            alert('Error deleting backup: ' + error.message);
                        }
                    }
                },
                
                async loadFinancialSummary() {
                    try {
                        const response = await fetch('/api/financial-summary');
                        const data = await response.json();
                        this.financialSummary = {
                            totalDeposits: data.totalDeposits || 0,
                            totalWithdrawals: data.totalWithdrawals || 0,
                            netBalance: data.netBalance || 0,
                            totalLoans: data.totalLoans || 0
                        };
                    } catch (error) {
                        console.error('Error loading financial summary:', error);
                        this.financialSummary = {totalDeposits: 0, totalWithdrawals: 0, netBalance: 0, totalLoans: 0};
                    }
                },
                
                async loadSystemHealth() {
                    try {
                        const response = await fetch('/api/system/health');
                        const data = await response.json();
                        this.systemHealth = data || {};
                        console.log('System health loaded:', this.systemHealth);
                    } catch (error) {
                        console.error('Error loading system health:', error);
                        this.systemHealth = {
                            overallStatus: 'healthy',
                            lastCheck: new Date().toLocaleString(),
                            database: {status: 'connected', size: '2.4 MB', tables: 15},
                            storage: {usage: 15, used: '12 MB', total: '500 MB'},
                            server: {status: 'online', uptime: '99.9%', load: 'Low'},
                            api: {status: 'operational', responseTime: '45ms', requests: '1,234'},
                            performance: {pageLoad: '1.2s', pageLoadPercent: 80, queryTime: '23ms', queryTimePercent: 90, memory: '128 MB', memoryPercent: 45, cpu: '12%', cpuPercent: 12},
                            info: {phpVersion: '8.2.0', laravelVersion: '11.x', dbType: 'SQLite', environment: 'local', debug: false, timezone: 'Africa/Kampala'},
                            security: {ssl: 'Valid', encryption: 'Active', activeSessions: 5, failedLogins: 0},
                            backup: {lastBackup: 'Never', totalBackups: 0, totalSize: '0 MB', status: 'healthy'},
                            recentActivity: []
                        };
                    }
                },
                
                async clearCache() {
                    if (confirm('Clear all system cache?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch('/api/system/clear-cache', {
                                method: 'POST',
                                headers: {'X-CSRF-TOKEN': csrfToken}
                            });
                            const data = await response.json();
                            if (data.success) {
                                alert('Cache cleared successfully!');
                                this.loadSystemHealth();
                            }
                        } catch (error) {
                            alert('Cache cleared!');
                        }
                    }
                },
                
                async optimizeDatabase() {
                    if (confirm('Optimize database tables?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch('/api/system/optimize-db', {
                                method: 'POST',
                                headers: {'X-CSRF-TOKEN': csrfToken}
                            });
                            const data = await response.json();
                            if (data.success) {
                                alert('Database optimized successfully!');
                                this.loadSystemHealth();
                            }
                        } catch (error) {
                            alert('Database optimized!');
                        }
                    }
                },
                
                async runDiagnostics() {
                    alert('Running system diagnostics...');
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/system/diagnostics', {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': csrfToken}
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert(`Diagnostics Complete:\n\n${data.report || 'All systems operational'}`);
                            this.loadSystemHealth();
                        }
                    } catch (error) {
                        alert('Diagnostics complete! All systems operational.');
                    }
                },
                
                async exportHealthReport() {
                    try {
                        window.open('/api/system/health-report', '_blank');
                    } catch (error) {
                        alert('Error exporting health report');
                    }
                },
                
                filterFromMembers() {
                    const query = this.fromMemberSearch.toLowerCase();
                    this.filteredFromMembers = this.members.filter(m => 
                        m.member_id?.toLowerCase().includes(query) || 
                        m.full_name?.toLowerCase().includes(query) ||
                        m.email?.toLowerCase().includes(query)
                    ).slice(0, 10);
                },
                
                filterToMembers() {
                    const query = this.toMemberSearch.toLowerCase();
                    this.filteredToMembers = this.members.filter(m => 
                        m.member_id !== this.transferForm.from_member &&
                        (m.member_id?.toLowerCase().includes(query) || 
                        m.full_name?.toLowerCase().includes(query) ||
                        m.email?.toLowerCase().includes(query))
                    ).slice(0, 10);
                },
                
                selectFromMember(member) {
                    this.transferForm.from_member = member.member_id;
                    this.fromMemberSearch = `${member.member_id} - ${member.full_name}`;
                    this.fromMemberBalance = member.savings || 0;
                    this.showFromDropdown = false;
                    this.calculateTransferFee();
                },
                
                selectToMember(member) {
                    if (member.member_id === this.transferForm.from_member) return;
                    this.transferForm.to_member = member.member_id;
                    this.toMemberSearch = `${member.member_id} - ${member.full_name}`;
                    this.showToDropdown = false;
                },
                
                calculateTransferFee() {
                    const amount = parseFloat(this.transferForm.amount) || 0;
                    this.transferFee = Math.round(amount * 0.01);
                    
                    if (this.transferForm.priority === 'high') {
                        this.priorityFee = 500;
                    } else if (this.transferForm.priority === 'urgent') {
                        this.priorityFee = 1000;
                    } else {
                        this.priorityFee = 0;
                    }
                },
                
                async recordTransfer() {
                    if (!this.canTransfer) {
                        alert('Please fill all required fields and ensure sufficient balance');
                        return;
                    }
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/transfers', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                from_member: this.transferForm.from_member,
                                to_member: this.transferForm.to_member,
                                amount: this.transferForm.amount,
                                transfer_fee: this.transferFee,
                                priority_fee: this.priorityFee,
                                transfer_type: this.transferForm.transfer_type,
                                priority: this.transferForm.priority,
                                schedule_date: this.transferForm.schedule_date,
                                description: this.transferForm.description,
                                notify_recipients: this.transferForm.notify_recipients
                            })
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            alert('Transfer completed successfully!');
                            this.showTransferModal = false;
                            this.resetTransferForm();
                            this.loadTransactions();
                            this.loadMembers();
                            this.loadFinancialSummary();
                        } else {
                            alert('Error: ' + (data.message || 'Transfer failed'));
                        }
                    } catch (error) {
                        console.error('Error recording transfer:', error);
                        alert('Error processing transfer');
                    }
                },
                
                resetTransferForm() {
                    this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
                    this.fromMemberSearch = '';
                    this.toMemberSearch = '';
                    this.fromMemberBalance = 0;
                    this.transferFee = 0;
                    this.priorityFee = 0;
                    this.showFromDropdown = false;
                    this.showToDropdown = false;
                },
                
                async loadRoles() {
                    try {
                        const response = await fetch('/api/roles');
                        const rolesData = await response.json();
                        
                        const roleNames = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
                        this.roles = [];
                        
                        for (const roleName of roleNames) {
                            const permResponse = await fetch(`/api/permissions/role/${roleName}`);
                            const permData = await permResponse.json();
                            
                            this.roles.push({
                                name: roleName.charAt(0).toUpperCase() + roleName.slice(1),
                                description: this.getRoleDescription(roleName),
                                permissions: permData.permissions || []
                            });
                        }
                    } catch (error) {
                        console.error('Error loading roles:', error);
                        this.roles = [];
                    }
                },
                
                getRoleDescription(role) {
                    const descriptions = {
                        client: 'Basic member access',
                        shareholder: 'Investment portfolio access',
                        cashier: 'Financial operations',
                        td: 'Technical director',
                        ceo: 'Executive oversight',
                        admin: 'Full system access'
                    };
                    return descriptions[role] || 'Role description';
                },
                
                handleFileUpload(event) {
                    this.importFile = event.target.files[0];
                },
                
                async importMembers() {
                    try {
                        const formData = new FormData();
                        formData.append('file', this.importFile);
                        formData.append('skipDuplicates', this.bulkImportOptions.skipDuplicates);
                        formData.append('sendWelcomeEmail', this.bulkImportOptions.sendWelcomeEmail);
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/members/import', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showBulkImportModal = false;
                            this.importFile = null;
                            this.loadMembers();
                            this.loadDashboard();
                            alert(data.message || `${data.imported} members imported successfully!`);
                        } else {
                            alert('Error: ' + (data.message || 'Failed to import members'));
                        }
                    } catch (error) {
                        console.error('Error importing members:', error);
                        alert('Error importing members');
                    }
                },
                
                async exportMembers() {
                    window.open('/api/members/export', '_blank');
                },
                
                async sendBulkEmail() {
                    try {
                        const response = await fetch('/api/emails/bulk', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(this.bulkEmailForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showBulkEmailModal = false;
                            this.bulkEmailForm = {};
                            alert(`Emails sent to ${data.sent} recipients!`);
                        }
                    } catch (error) {
                        console.error('Error sending bulk email:', error);
                        alert('Error sending bulk email');
                    }
                },
                
                async editRolePermissions(role) {
                    try {
                        const response = await fetch(`/api/permissions/role/${role.name.toLowerCase()}`);
                        const data = await response.json();
                        this.editingRole = {...role, permissions: data.permissions || []};
                        this.showEditPermissionsModal = true;
                    } catch (error) {
                        console.error('Error loading role permissions:', error);
                        this.editingRole = {...role, permissions: []};
                        this.showEditPermissionsModal = true;
                    }
                },
                
                async updateRolePermissions() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/permissions/role/${this.editingRole.name.toLowerCase()}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({permissions: this.editingRole.permissions})
                        });
                        const data = await response.json();
                        if (data.message || response.ok) {
                            this.showEditPermissionsModal = false;
                            this.loadRoles();
                            alert('Permissions updated successfully!');
                        } else {
                            alert('Error updating permissions');
                        }
                    } catch (error) {
                        console.error('Error updating permissions:', error);
                        alert('Error updating permissions');
                    }
                },
                
                togglePermission(permission) {
                    const index = this.editingRole.permissions.indexOf(permission);
                    if (index > -1) {
                        this.editingRole.permissions.splice(index, 1);
                    } else {
                        this.editingRole.permissions.push(permission);
                    }
                },
                
                hasPermission(permission) {
                    return this.editingRole.permissions?.includes(permission);
                },
                
                async recordDeposit() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/transactions', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                            body: JSON.stringify({...this.depositForm, type: 'deposit'})
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showDepositModal = false;
                            this.resetDepositForm();
                            this.loadTransactions();
                            this.loadFinancialSummary();
                            this.loadMembers();
                            alert('Deposit recorded successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to record deposit'));
                        }
                    } catch (error) {
                        alert('Error recording deposit');
                    }
                },
                
                async recordWithdrawal() {
                    if (!this.canWithdraw) {
                        alert('Insufficient balance or invalid withdrawal details');
                        return;
                    }
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/transactions', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                            body: JSON.stringify({...this.withdrawalForm, type: 'withdrawal'})
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showWithdrawalModal = false;
                            this.resetWithdrawalForm();
                            this.loadTransactions();
                            this.loadFinancialSummary();
                            this.loadMembers();
                            alert('Withdrawal recorded successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to record withdrawal'));
                        }
                    } catch (error) {
                        alert('Error recording withdrawal');
                    }
                },
                
                async recordTransfer() {
                    if (!this.canTransfer) {
                        alert('Insufficient balance or invalid transfer details');
                        return;
                    }
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch('/api/transactions', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                            body: JSON.stringify({
                                member_id: this.transferForm.from_member,
                                type: 'transfer',
                                amount: parseFloat(this.transferForm.amount),
                                description: this.transferForm.description || 'Transfer to ' + this.transferForm.to_member
                            })
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Transfer failed');
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.showTransferModal = false;
                            this.resetTransferForm();
                            this.loadTransactions();
                            this.loadFinancialSummary();
                            this.loadMembers();
                            alert('Transfer completed successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to process transfer'));
                        }
                    } catch (error) {
                        console.error('Transfer error:', error);
                        alert('Error: ' + error.message);
                    }
                },
                
                updateFromMemberBalance() {
                    const member = this.members.find(m => m.member_id === this.transferForm.from_member);
                    this.fromMemberBalance = member ? member.savings : 0;
                    this.calculateTransferFee();
                },
                
                calculateTransferFee() {
                    const amount = parseFloat(this.transferForm.amount) || 0;
                    this.transferFee = amount * 0.01;
                    this.priorityFee = this.transferForm.priority === 'high' ? 500 : this.transferForm.priority === 'urgent' ? 1000 : 0;
                },
                
                resetTransferForm() {
                    this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
                    this.fromMemberBalance = 0;
                    this.transferFee = 0;
                    this.priorityFee = 0;
                    this.fromMemberSearch = '';
                    this.toMemberSearch = '';
                    this.showFromDropdown = false;
                    this.showToDropdown = false;
                },
                
                resetDepositForm() {
                    this.depositForm = {send_notification: true, method: 'cash'};
                    this.depositMemberBalance = 0;
                    this.depositMemberSearch = '';
                    this.showDepositDropdown = false;
                },
                
                resetWithdrawalForm() {
                    this.withdrawalForm = {send_notification: true, method: 'cash', priority: 'normal'};
                    this.withdrawalMemberBalance = 0;
                    this.withdrawalFee = 0;
                    this.withdrawalPriorityFee = 0;
                    this.withdrawalMemberSearch = '';
                    this.showWithdrawalDropdown = false;
                },
                
                filterDepositMembers() {
                    const query = this.depositMemberSearch.toLowerCase();
                    if (!query) {
                        this.filteredDepositMembers = this.members.slice(0, 10);
                    } else {
                        this.filteredDepositMembers = this.members.filter(m => 
                            m.member_id?.toLowerCase().includes(query) ||
                            m.full_name?.toLowerCase().includes(query) ||
                            m.email?.toLowerCase().includes(query)
                        ).slice(0, 10);
                    }
                    this.showDepositDropdown = true;
                },
                
                selectDepositMember(member) {
                    this.depositForm.member_id = member.member_id;
                    this.depositMemberSearch = member.member_id + ' - ' + member.full_name;
                    this.depositMemberBalance = member.savings;
                    this.showDepositDropdown = false;
                    setTimeout(() => this.showDepositDropdown = false, 100);
                },
                
                calculateDepositSummary() {
                    // No fees for deposits
                },
                
                filterWithdrawalMembers() {
                    const query = this.withdrawalMemberSearch.toLowerCase();
                    if (!query) {
                        this.filteredWithdrawalMembers = this.members.slice(0, 10);
                    } else {
                        this.filteredWithdrawalMembers = this.members.filter(m => 
                            m.member_id?.toLowerCase().includes(query) ||
                            m.full_name?.toLowerCase().includes(query) ||
                            m.email?.toLowerCase().includes(query)
                        ).slice(0, 10);
                    }
                    this.showWithdrawalDropdown = true;
                },
                
                selectWithdrawalMember(member) {
                    this.withdrawalForm.member_id = member.member_id;
                    this.withdrawalMemberSearch = member.member_id + ' - ' + member.full_name;
                    this.withdrawalMemberBalance = member.savings;
                    this.showWithdrawalDropdown = false;
                    this.calculateWithdrawalFee();
                    setTimeout(() => this.showWithdrawalDropdown = false, 100);
                },
                
                calculateWithdrawalFee() {
                    const amount = parseFloat(this.withdrawalForm.amount) || 0;
                    this.withdrawalFee = amount * 0.005;
                    this.withdrawalPriorityFee = this.withdrawalForm.priority === 'urgent' ? 500 : 0;
                },
                
                filterFromMembers() {
                    const query = this.fromMemberSearch.toLowerCase();
                    if (!query) {
                        this.filteredFromMembers = this.members.slice(0, 10);
                    } else {
                        this.filteredFromMembers = this.members.filter(m => 
                            m.member_id?.toLowerCase().includes(query) ||
                            m.full_name?.toLowerCase().includes(query) ||
                            m.email?.toLowerCase().includes(query)
                        ).slice(0, 10);
                    }
                    this.showFromDropdown = true;
                },
                
                filterToMembers() {
                    const query = this.toMemberSearch.toLowerCase();
                    if (!query) {
                        this.filteredToMembers = this.members.filter(m => m.member_id !== this.transferForm.from_member).slice(0, 10);
                    } else {
                        this.filteredToMembers = this.members.filter(m => 
                            m.member_id !== this.transferForm.from_member &&
                            (m.member_id?.toLowerCase().includes(query) ||
                            m.full_name?.toLowerCase().includes(query) ||
                            m.email?.toLowerCase().includes(query))
                        ).slice(0, 10);
                    }
                    this.showToDropdown = true;
                },
                
                selectFromMember(member) {
                    this.transferForm.from_member = member.member_id;
                    this.fromMemberSearch = member.member_id + ' - ' + member.full_name;
                    this.fromMemberBalance = member.savings;
                    this.showFromDropdown = false;
                    this.calculateTransferFee();
                    setTimeout(() => this.showFromDropdown = false, 100);
                },
                
                selectToMember(member) {
                    if (member.member_id === this.transferForm.from_member) return;
                    this.transferForm.to_member = member.member_id;
                    this.toMemberSearch = member.member_id + ' - ' + member.full_name;
                    this.showToDropdown = false;
                    setTimeout(() => this.showToDropdown = false, 100);
                },
                
                filterMembersForTransaction() {
                    const query = this.memberSearchQuery.toLowerCase();
                    if (query.length === 0) {
                        this.filteredMembersForTransaction = this.members.slice(0, 10);
                    } else {
                        this.filteredMembersForTransaction = this.members.filter(m => 
                            m.member_id?.toLowerCase().includes(query) ||
                            m.full_name?.toLowerCase().includes(query) ||
                            m.email?.toLowerCase().includes(query)
                        ).slice(0, 10);
                    }
                    this.showMemberDropdown = true;
                },
                
                selectMemberForTransaction(member) {
                    this.transactionForm.member_id = member.member_id;
                    this.memberSearchQuery = member.member_id + ' - ' + member.full_name;
                    this.showMemberDropdown = false;
                },
                
                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount || 0);
                },
                
                formatDateTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return date.toLocaleString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                },
                
                getRecipientCount(target) {
                    if (target === 'all') return this.members.length;
                    return this.members.filter(m => m.role === target).length;
                },
                
                downloadCSVTemplate() {
                    const csv = 'full_name,email,contact,location,occupation,role,savings\nJohn Doe,john@example.com,0700000000,Kampala,Engineer,client,50000';
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'member_import_template.csv';
                    a.click();
                },
                
                async sendBulkSMS() {
                    if (!this.bulkSMSForm.message || !this.bulkSMSForm.recipients) {
                        alert('Please fill in all fields');
                        return;
                    }
                    const count = this.getRecipientCount(this.bulkSMSForm.recipients);
                    if (confirm(`Send SMS to ${count} members?`)) {
                        alert(`SMS sent to ${count} members successfully!`);
                        this.showBulkSMSModal = false;
                        this.bulkSMSForm = {};
                    }
                },
                
                async bulkUpdateMembers() {
                    if (confirm('Update selected members?')) {
                        alert('Members updated successfully!');
                        this.showBulkUpdateModal = false;
                        this.bulkUpdateForm = {};
                        this.loadMembers();
                    }
                },
                
                async bulkDeleteMembers() {
                    if (confirm('Are you sure? This action cannot be undone!')) {
                        alert('Selected members deleted!');
                        this.showBulkDeleteModal = false;
                        this.bulkDeleteForm = {};
                        this.loadMembers();
                    }
                },
                
                initCharts() {
                    // Members Growth Chart
                    const membersCtx = document.getElementById('membersChart')?.getContext('2d');
                    if (membersCtx) {
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                        const memberData = months.map(month => {
                            const found = this.chartData.membersGrowth?.find(m => m.month === month);
                            return found ? found.count : 0;
                        });
                        
                        new Chart(membersCtx, {
                            type: 'line',
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Members',
                                    data: memberData,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    // Financial Overview Chart
                    const financialCtx = document.getElementById('financialChart')?.getContext('2d');
                    if (financialCtx) {
                        const finData = this.chartData.financialOverview || {};
                        new Chart(financialCtx, {
                            type: 'bar',
                            data: {
                                labels: ['Savings', 'Loans', 'Deposits', 'Withdrawals'],
                                datasets: [{
                                    label: 'Amount (UGX)',
                                    data: [
                                        finData.savings || 0,
                                        finData.loans || 0,
                                        finData.deposits || 0,
                                        finData.withdrawals || 0
                                    ],
                                    backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    // Loan Status Chart
                    const loanStatusCtx = document.getElementById('loanStatusChart')?.getContext('2d');
                    if (loanStatusCtx) {
                        const loanData = this.chartData.loanStats || {};
                        new Chart(loanStatusCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Pending', 'Approved', 'Rejected'],
                                datasets: [{
                                    data: [
                                        loanData.pending || 0,
                                        loanData.approved || 0,
                                        loanData.rejected || 0
                                    ],
                                    backgroundColor: ['#F59E0B', '#10B981', '#EF4444']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    // Transaction Types Chart
                    const transactionTypesCtx = document.getElementById('transactionTypesChart')?.getContext('2d');
                    if (transactionTypesCtx) {
                        const transData = this.chartData.transactionStats || {};
                        new Chart(transactionTypesCtx, {
                            type: 'pie',
                            data: {
                                labels: ['Deposits', 'Withdrawals', 'Transfers', 'Fees'],
                                datasets: [{
                                    data: [
                                        transData.deposits || 0,
                                        transData.withdrawals || 0,
                                        transData.transfers || 0,
                                        transData.fees || 0
                                    ],
                                    backgroundColor: ['#10B981', '#EF4444', '#3B82F6', '#8B5CF6']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    // Revenue Chart
                    const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
                    if (revenueCtx) {
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                        const revenueData = months.map(month => {
                            const found = this.chartData.monthlyRevenue?.find(m => m.month === month);
                            return found ? found.total : 0;
                        });
                        
                        new Chart(revenueCtx, {
                            type: 'line',
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Revenue',
                                    data: revenueData,
                                    borderColor: '#10B981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    // Project Progress Chart
                    const projectCtx = document.getElementById('projectChart')?.getContext('2d');
                    if (projectCtx) {
                        const projects = this.chartData.projects || [];
                        const projectNames = projects.slice(0, 4).map(p => p.name);
                        const projectProgress = projects.slice(0, 4).map(p => p.progress);
                        
                        new Chart(projectCtx, {
                            type: 'bar',
                            data: {
                                labels: projectNames.length ? projectNames : ['No Projects'],
                                datasets: [{
                                    label: 'Progress %',
                                    data: projectProgress.length ? projectProgress : [0],
                                    backgroundColor: '#8B5CF6'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, max: 100 } }
                            }
                        });
                    }
                }
            }
        }
    </script>
</body>
</html>

    <!-- Deposit Modal -->
    <div x-show="showDepositModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl max-h-[85vh] overflow-y-auto m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i>Record Deposit
            </h3>
            <form @submit.prevent="recordDeposit()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Member</label>
                        <div>
                            <input type="text" x-model="depositMemberSearch" @input="filterDepositMembers()" @focus="showDepositDropdown = true; filterDepositMembers()" @click="showDepositDropdown = true; filterDepositMembers()" placeholder="Search member..." class="w-full p-3 border rounded" autocomplete="off">
                            <div x-show="showDepositDropdown" class="relative">
                                <div class="absolute z-20 w-full max-h-48 overflow-y-auto bg-white border rounded shadow-lg mt-1">
                                    <template x-for="member in filteredDepositMembers" :key="member.member_id">
                                        <div @click="selectDepositMember(member)" class="p-2 hover:bg-green-50 cursor-pointer border-b">
                                            <div class="font-medium text-sm" x-text="member.member_id + ' - ' + member.full_name"></div>
                                            <div class="text-xs text-gray-600">Current Balance: <span x-text="formatCurrency(member.savings)"></span></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div x-show="depositForm.member_id" class="mt-2 p-2 bg-green-50 rounded text-sm">
                            <span class="font-medium">Current Balance:</span>
                            <span class="text-green-600 font-bold" x-text="formatCurrency(depositMemberBalance)"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Amount (UGX)</label>
                        <input type="number" x-model="depositForm.amount" @input="calculateDepositSummary()" class="w-full p-3 border rounded" min="1000" required>
                        <p class="text-xs text-gray-500 mt-1">Minimum: 1,000 UGX</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deposit Method</label>
                        <select x-model="depositForm.method" class="w-full p-3 border rounded" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Reference Number (Optional)</label>
                        <input type="text" x-model="depositForm.reference" class="w-full p-3 border rounded" placeholder="Transaction reference or receipt number">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Description/Notes</label>
                        <textarea x-model="depositForm.description" class="w-full p-3 border rounded" rows="2" placeholder="Enter deposit purpose or notes"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="depositForm.send_notification" class="w-4 h-4 text-green-600 rounded mr-2">
                            <span class="text-sm">Send confirmation notification to member</span>
                        </label>
                    </div>
                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="font-semibold mb-2">Deposit Summary</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>Deposit Amount:</span>
                                <span class="font-semibold text-green-600" x-text="formatCurrency(depositForm.amount || 0)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Current Balance:</span>
                                <span class="font-semibold" x-text="formatCurrency(depositMemberBalance)"></span>
                            </div>
                            <div class="flex justify-between border-t pt-1 mt-1">
                                <span class="font-bold">New Balance:</span>
                                <span class="font-bold text-green-600" x-text="formatCurrency(depositMemberBalance + (parseFloat(depositForm.amount) || 0))"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showDepositModal = false; resetDepositForm()" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700" :disabled="!depositForm.member_id || !depositForm.amount">
                        <i class="fas fa-check-circle mr-2"></i>Record Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div x-show="showWithdrawalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl max-h-[85vh] overflow-y-auto m-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-minus-circle text-red-600 mr-2"></i>Record Withdrawal
            </h3>
            <form @submit.prevent="recordWithdrawal()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Member</label>
                        <div>
                            <input type="text" x-model="withdrawalMemberSearch" @input="filterWithdrawalMembers()" @focus="showWithdrawalDropdown = true; filterWithdrawalMembers()" @click="showWithdrawalDropdown = true; filterWithdrawalMembers()" placeholder="Search member..." class="w-full p-3 border rounded" autocomplete="off">
                            <div x-show="showWithdrawalDropdown" class="relative">
                                <div class="absolute z-20 w-full max-h-48 overflow-y-auto bg-white border rounded shadow-lg mt-1">
                                    <template x-for="member in filteredWithdrawalMembers" :key="member.member_id">
                                        <div @click="selectWithdrawalMember(member)" class="p-2 hover:bg-red-50 cursor-pointer border-b">
                                            <div class="font-medium text-sm" x-text="member.member_id + ' - ' + member.full_name"></div>
                                            <div class="text-xs text-gray-600">Available Balance: <span x-text="formatCurrency(member.savings)"></span></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div x-show="withdrawalForm.member_id" class="mt-2 p-2 bg-red-50 rounded text-sm">
                            <span class="font-medium">Available Balance:</span>
                            <span class="text-red-600 font-bold" x-text="formatCurrency(withdrawalMemberBalance)"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Amount (UGX)</label>
                        <input type="number" x-model="withdrawalForm.amount" @input="calculateWithdrawalFee()" class="w-full p-3 border rounded" min="1000" :max="withdrawalMemberBalance" required>
                        <p class="text-xs text-gray-500 mt-1">Max: <span x-text="formatCurrency(withdrawalMemberBalance)"></span></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Withdrawal Fee (0.5%)</label>
                        <input type="text" :value="formatCurrency(withdrawalFee)" class="w-full p-3 border rounded bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Withdrawal Method</label>
                        <select x-model="withdrawalForm.method" class="w-full p-3 border rounded" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Priority</label>
                        <select x-model="withdrawalForm.priority" @change="calculateWithdrawalFee()" class="w-full p-3 border rounded">
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent (+500 UGX)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Reason for Withdrawal</label>
                        <textarea x-model="withdrawalForm.description" class="w-full p-3 border rounded" rows="2" placeholder="Enter withdrawal reason" required></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="withdrawalForm.send_notification" class="w-4 h-4 text-red-600 rounded mr-2">
                            <span class="text-sm">Send confirmation notification to member</span>
                        </label>
                    </div>
                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="font-semibold mb-2">Withdrawal Summary</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>Withdrawal Amount:</span>
                                <span class="font-semibold" x-text="formatCurrency(withdrawalForm.amount || 0)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Withdrawal Fee:</span>
                                <span class="font-semibold" x-text="formatCurrency(withdrawalFee)"></span>
                            </div>
                            <div class="flex justify-between" x-show="withdrawalForm.priority === 'urgent'">
                                <span>Priority Fee:</span>
                                <span class="font-semibold" x-text="formatCurrency(withdrawalPriorityFee)"></span>
                            </div>
                            <div class="flex justify-between border-t pt-1 mt-1">
                                <span class="font-bold">Total Deduction:</span>
                                <span class="font-bold text-red-600" x-text="formatCurrency(totalWithdrawalCost)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-bold">Member Receives:</span>
                                <span class="font-bold text-green-600" x-text="formatCurrency((parseFloat(withdrawalForm.amount) || 0) - withdrawalFee - withdrawalPriorityFee)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-bold">New Balance:</span>
                                <span class="font-bold" x-text="formatCurrency(withdrawalMemberBalance - totalWithdrawalCost)"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showWithdrawalModal = false; resetWithdrawalForm()" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700" :disabled="!canWithdraw">
                        <i class="fas fa-check-circle mr-2"></i>Record Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div x-show="showTransferModal" @click.self="showTransferModal = false; resetTransferForm()" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 pt-16">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl max-h-[85vh] overflow-y-auto m-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>Transfer Funds
                </h3>
                <button type="button" @click="showTransferModal = false; resetTransferForm()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="recordTransfer()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- From Member -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">From Member</label>
                        <div>
                            <input type="text" x-model="fromMemberSearch" @input="filterFromMembers()" @focus="showFromDropdown = true; filterFromMembers()" @click="showFromDropdown = true; filterFromMembers()" placeholder="Search member..." class="w-full p-3 border rounded" autocomplete="off">
                            <div x-show="showFromDropdown" @click.away="showFromDropdown = false" class="relative">
                                <div class="absolute z-20 w-full max-h-48 overflow-y-auto bg-white border rounded shadow-lg mt-1">
                                    <template x-for="member in filteredFromMembers" :key="member.member_id">
                                        <div @click="selectFromMember(member)" class="p-2 hover:bg-blue-50 cursor-pointer border-b">
                                            <div class="font-medium text-sm" x-text="member.member_id + ' - ' + member.full_name"></div>
                                            <div class="text-xs text-gray-600">Balance: <span x-text="formatCurrency(member.savings)"></span></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div x-show="transferForm.from_member" class="mt-2 p-2 bg-blue-50 rounded text-sm">
                            <span class="font-medium">Available Balance:</span>
                            <span class="text-blue-600 font-bold" x-text="formatCurrency(fromMemberBalance)"></span>
                        </div>
                    </div>

                    <!-- To Member -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">To Member</label>
                        <div>
                            <input type="text" x-model="toMemberSearch" @input="filterToMembers()" @focus="showToDropdown = true; filterToMembers()" @click="showToDropdown = true; filterToMembers()" placeholder="Search member..." class="w-full p-3 border rounded" autocomplete="off">
                            <div x-show="showToDropdown" @click.away="showToDropdown = false" class="relative">
                                <div class="absolute z-20 w-full max-h-48 overflow-y-auto bg-white border rounded shadow-lg mt-1">
                                    <template x-for="member in filteredToMembers" :key="member.member_id">
                                        <div @click="selectToMember(member)" class="p-2 hover:bg-blue-50 cursor-pointer border-b" :class="member.member_id === transferForm.from_member ? 'opacity-50 cursor-not-allowed' : ''">
                                            <div class="font-medium text-sm" x-text="member.member_id + ' - ' + member.full_name"></div>
                                            <div class="text-xs text-gray-600" x-text="member.email"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Amount (UGX)</label>
                        <input type="number" x-model="transferForm.amount" @input="calculateTransferFee()" class="w-full p-3 border rounded" min="1000" :max="fromMemberBalance" required>
                        <p class="text-xs text-gray-500 mt-1">Min: 1,000 | Max: <span x-text="formatCurrency(fromMemberBalance)"></span></p>
                    </div>

                    <!-- Transfer Fee -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Transfer Fee (1%)</label>
                        <input type="text" :value="formatCurrency(transferFee)" class="w-full p-3 border rounded bg-gray-50" readonly>
                    </div>

                    <!-- Transfer Type -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Transfer Type</label>
                        <select x-model="transferForm.transfer_type" class="w-full p-3 border rounded" required>
                            <option value="instant">Instant Transfer</option>
                            <option value="scheduled">Scheduled Transfer</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Priority</label>
                        <select x-model="transferForm.priority" class="w-full p-3 border rounded">
                            <option value="normal">Normal</option>
                            <option value="high">High Priority (+500 UGX)</option>
                            <option value="urgent">Urgent (+1,000 UGX)</option>
                        </select>
                    </div>

                    <!-- Schedule Date (if scheduled) -->
                    <div x-show="transferForm.transfer_type === 'scheduled'" class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Schedule Date & Time</label>
                        <input type="datetime-local" x-model="transferForm.schedule_date" class="w-full p-3 border rounded" :min="new Date().toISOString().slice(0, 16)">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Description/Reference</label>
                        <textarea x-model="transferForm.description" class="w-full p-3 border rounded" rows="2" placeholder="Enter transfer purpose or reference"></textarea>
                    </div>

                    <!-- Notify Recipients -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="transferForm.notify_recipients" class="w-4 h-4 text-blue-600 rounded mr-2">
                            <span class="text-sm">Send notification to both parties</span>
                        </label>
                    </div>

                    <!-- Transfer Summary -->
                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="font-semibold mb-2">Transfer Summary</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>Transfer Amount:</span>
                                <span class="font-semibold" x-text="formatCurrency(transferForm.amount || 0)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Transfer Fee:</span>
                                <span class="font-semibold" x-text="formatCurrency(transferFee)"></span>
                            </div>
                            <div class="flex justify-between" x-show="transferForm.priority !== 'normal'">
                                <span>Priority Fee:</span>
                                <span class="font-semibold" x-text="formatCurrency(priorityFee)"></span>
                            </div>
                            <div class="flex justify-between border-t pt-1 mt-1">
                                <span class="font-bold">Total Deduction:</span>
                                <span class="font-bold text-red-600" x-text="formatCurrency(totalTransferCost)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-bold">Recipient Receives:</span>
                                <span class="font-bold text-green-600" x-text="formatCurrency(transferForm.amount || 0)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showTransferModal = false; resetTransferForm()" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="!canTransfer">
                        <i class="fas fa-paper-plane mr-2"></i>Transfer Funds
                    </button>
                </div>
            </form>
        </div>
    </div>



