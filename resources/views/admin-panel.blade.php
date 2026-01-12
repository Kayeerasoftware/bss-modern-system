@extends('layouts.app')

@section('title', 'BSS Admin Panel')

@section('content')
    <div x-data="adminPanel()" class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white shadow-lg">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">BSS Admin Panel</h1>
            </div>
            <nav class="mt-4">
                <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </button>
                <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-users mr-3"></i> Members
                </button>
                <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-money-bill-wave mr-3"></i> Loans
                </button>
                <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-exchange-alt mr-3"></i> Transactions
                </button>
                <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-project-diagram mr-3"></i> Projects
                </button>
                <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-bell mr-3"></i> Notifications
                </button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-blue-600' : 'hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors duration-200">
                    <i class="fas fa-cog mr-3"></i> Settings
                </button>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <header class="bg-white dark:bg-gray-800 shadow-sm p-4 border-b dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="getTabTitle()"></h2>
                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors duration-200">Logout</button>
                            </form>
                        @else
                            <span class="text-gray-600 dark:text-gray-300">Guest</span>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="p-6 overflow-y-auto h-full bg-gray-100 dark:bg-gray-900">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" class="fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100">Total Members</p>
                                    <p class="text-3xl font-bold" x-text="stats.totalMembers">0</p>
                                </div>
                                <i class="fas fa-users text-4xl text-blue-200"></i>
                            </div>
                        </div>
                        <div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100">Total Savings</p>
                                    <p class="text-2xl font-bold" x-text="formatCurrency(stats.totalSavings)">0</p>
                                </div>
                                <i class="fas fa-piggy-bank text-4xl text-green-200"></i>
                            </div>
                        </div>
                        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100">Active Loans</p>
                                    <p class="text-3xl font-bold" x-text="stats.activeLoans">0</p>
                                </div>
                                <i class="fas fa-money-bill-wave text-4xl text-yellow-200"></i>
                            </div>
                        </div>
                        <div class="bg-purple-500 text-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100">Total Projects</p>
                                    <p class="text-3xl font-bold" x-text="stats.totalProjects">0</p>
                                </div>
                                <i class="fas fa-project-diagram text-4xl text-purple-200"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Members Tab -->
                <div x-show="activeTab === 'members'" class="fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Member Management panel</h3>
                        <button @click="showAddMemberModal = true; fetchNextMemberId()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Add Member
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Savings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="member in members" :key="member.id">
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="member.member_id"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="member.full_name"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="member.email"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="member.role"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="formatCurrency(member.savings)"></td>
                                        <td class="px-6 py-4 text-sm space-x-2">
                                            <button @click="editMember(member)" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click="deleteMember(member.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Loans Tab -->
                <div x-show="activeTab === 'loans'" class="fade-in">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Loan Management</h3>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Loan ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Purpose</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="loan in loans" :key="loan.id">
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="loan.loan_id"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="loan.member?.full_name"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="formatCurrency(loan.amount)"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="loan.purpose"></td>
                                        <td class="px-6 py-4 text-sm">
                                            <span :class="getStatusClass(loan.status)" class="px-2 py-1 rounded-full text-xs" x-text="loan.status"></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm space-x-2">
                                            <button x-show="loan.status === 'pending'" @click="approveLoan(loan.id)" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button x-show="loan.status === 'pending'" @click="rejectLoan(loan.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transactions Tab -->
                <div x-show="activeTab === 'transactions'" class="fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Transaction Management</h3>
                        <button @click="showAddTransactionModal = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Add Transaction
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Transaction ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="transaction in transactions" :key="transaction.id">
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="transaction.transaction_id"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="transaction.member?.full_name"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="formatCurrency(transaction.amount)"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="transaction.type"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(transaction.created_at)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Projects Tab -->
                <div x-show="activeTab === 'projects'" class="fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Project Management</h3>
                        <button @click="showAddProjectModal = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Add Project
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="project in projects" :key="project.id">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2" x-text="project.name"></h4>
                                <p class="text-gray-600 dark:text-gray-300 mb-4" x-text="project.description"></p>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Budget:</span>
                                        <span x-text="formatCurrency(project.budget)" class="text-gray-800 dark:text-gray-100"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Progress:</span>
                                        <span x-text="project.progress + '%'" class="text-gray-800 dark:text-gray-100"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                    </div>
                                </div>
                                <div class="mt-4 flex space-x-2">
                                    <button @click="editProject(project)" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteProject(project.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div x-show="activeTab === 'notifications'" class="fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Send Notification</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <form @submit.prevent="sendNotification()">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                                    <input x-model="notificationForm.title" type="text" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                                    <select x-model="notificationForm.type" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                                        <option value="info">Info</option>
                                        <option value="warning">Warning</option>
                                        <option value="success">Success</option>
                                        <option value="error">Error</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                <textarea x-model="notificationForm.message" rows="4" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required></textarea>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target Roles</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" x-model="notificationForm.roles" value="client" class="mr-2">
                                        Client
                                    </label>
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" x-model="notificationForm.roles" value="shareholder" class="mr-2">
                                        Shareholder
                                    </label>
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" x-model="notificationForm.roles" value="cashier" class="mr-2">
                                        Cashier
                                    </label>
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" x-model="notificationForm.roles" value="td" class="mr-2">
                                        TD
                                    </label>
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" x-model="notificationForm.roles" value="ceo" class="mr-2">
                                        CEO
                                    </label>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition-colors duration-200">
                                    Send Notification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" class="fade-in">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">System Settings</h3>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <form @submit.prevent="updateSettings()">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interest Rate (%)</label>
                                    <input x-model="settings.interest_rate" type="number" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loan Processing Fee (%)</label>
                                    <input x-model="settings.loan_processing_fee" type="number" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Savings</label>
                                    <input x-model="settings.minimum_savings" type="number" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Loan</label>
                                    <input x-model="settings.maximum_loan" type="number" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition-colors duration-200">
                                    Update Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <!-- Add Member Modal -->
        <div x-show="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddMemberModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add New Member</h3>
                <form @submit.prevent="addMember()">
                    <div class="space-y-4">
                        <input x-model="nextMemberId" type="text" placeholder="Member ID" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" readonly>
                        <input x-model="memberForm.full_name" type="text" placeholder="Full Name" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="memberForm.email" type="email" placeholder="Email" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="memberForm.location" type="text" placeholder="Location" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="memberForm.occupation" type="text" placeholder="Occupation" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="memberForm.contact" type="text" placeholder="Contact" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <select x-model="memberForm.role" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                            <option value="">Select Role</option>
                            <option value="client">Client</option>
                            <option value="shareholder">Shareholder</option>
                            <option value="cashier">Cashier</option>
                            <option value="td">TD</option>
                            <option value="ceo">CEO</option>
                        </select>
                        <input x-model="memberForm.savings" type="number" placeholder="Initial Savings" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddMemberModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Member</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Transaction Modal -->
        <div x-show="showAddTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddTransactionModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add Transaction</h3>
                <form @submit.prevent="addTransaction()">
                    <div class="space-y-4">
                        <select x-model="transactionForm.member_id" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                            <option value="">Select Member</option>
                            <template x-for="member in members" :key="member.id">
                                <option :value="member.member_id" x-text="member.full_name"></option>
                            </template>
                        </select>
                        <input x-model="transactionForm.amount" type="number" placeholder="Amount" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <select x-model="transactionForm.type" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                            <option value="">Select Type</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                        </select>
                        <input x-model="transactionForm.description" type="text" placeholder="Description" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddTransactionModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Transaction</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Project Modal -->
        <div x-show="showAddProjectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddProjectModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add Project</h3>
                <form @submit.prevent="addProject()">
                    <div class="space-y-4">
                        <input x-model="projectForm.name" type="text" placeholder="Project Name" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <textarea x-model="projectForm.description" placeholder="Description" rows="3" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required></textarea>
                        <input x-model="projectForm.budget" type="number" placeholder="Budget" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="projectForm.timeline" type="date" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2" required>
                        <input x-model="projectForm.progress" type="number" placeholder="Progress %" min="0" max="100" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md px-3 py-2">
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddProjectModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function adminPanel() {
            return {
                activeTab: 'dashboard',
                stats: {},
                members: [],
                loans: [],
                transactions: [],
                projects: [],
                settings: {},
                showAddMemberModal: false,
                showAddTransactionModal: false,
                showAddProjectModal: false,
                memberForm: {},
                nextMemberId: '',
                transactionForm: {},
                projectForm: {},
                notificationForm: { roles: [] },

                init() {
                    this.loadDashboard();
                    this.loadMembers();
                    this.loadLoans();
                    this.loadTransactions();
                    this.loadProjects();
                    this.loadSettings();
                },

                getTabTitle() {
                    const titles = {
                        dashboard: 'Dashboard',
                        members: 'Member Management',
                        loans: 'Loan Management',
                        transactions: 'Transaction Management',
                        projects: 'Project Management',
                        notifications: 'Send Notifications',
                        settings: 'System Settings'
                    };
                    return titles[this.activeTab];
                },

                async loadDashboard() {
                    const response = await fetch('/api/admin/dashboard');
                    this.stats = await response.json();
                },

                async loadMembers() {
                    const response = await fetch('/api/members');
                    this.members = await response.json();
                    await this.fetchNextMemberId();
                },

                async fetchNextMemberId() {
                    const response = await fetch('/api/members/next-id?t=' + Date.now());
                    const data = await response.json();
                    console.log('Fetched next member ID:', data.next_id);
                    this.nextMemberId = data.next_id;
                },

                async loadLoans() {
                    const response = await fetch('/api/loans'); // Corrected API endpoint
                    this.loans = await response.json();
                },

                async loadTransactions() {
                    const response = await fetch('/api/transactions'); // Corrected API endpoint
                    this.transactions = await response.json();
                },

                async loadProjects() {
                    const response = await fetch('/api/projects'); // Corrected API endpoint
                    this.projects = await response.json();
                },

                async loadSettings() {
                    const response = await fetch('/api/settings'); // Corrected API endpoint
                    this.settings = await response.json();
                },

                async addMember() {
                    const response = await fetch('/api/members', { // Corrected API endpoint
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.memberForm)
                    });

                    if (response.ok) {
                        this.loadMembers();
                        this.showAddMemberModal = false;
                        this.memberForm = {};
                        alert('Member added successfully!');
                    }
                },

                async deleteMember(id) {
                    if (confirm('Are you sure you want to delete this member?')) {
                        await fetch(`/api/members/${id}`, { method: 'DELETE' }); // Corrected API endpoint
                        this.loadMembers();
                    }
                },

                async approveLoan(id) {
                    await fetch(`/api/loans/${id}/approve`, { method: 'POST' }); // Corrected API endpoint
                    this.loadLoans();
                    alert('Loan approved successfully!');
                },

                async rejectLoan(id) {
                    await fetch(`/api/loans/${id}/reject`, { method: 'POST' }); // Corrected API endpoint
                    this.loadLoans();
                    alert('Loan rejected!');
                },

                async addTransaction() {
                    const response = await fetch('/api/transactions', { // Corrected API endpoint
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.transactionForm)
                    });

                    if (response.ok) {
                        this.loadTransactions();
                        this.loadMembers();
                        this.showAddTransactionModal = false;
                        this.transactionForm = {};
                        alert('Transaction added successfully!');
                    }
                },

                async addProject() {
                    const response = await fetch('/api/projects', { // Corrected API endpoint
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.projectForm)
                    });

                    if (response.ok) {
                        this.loadProjects();
                        this.showAddProjectModal = false;
                        this.projectForm = {};
                        alert('Project added successfully!');
                    }
                },

                async deleteProject(id) {
                    if (confirm('Are you sure you want to delete this project?')) {
                        await fetch(`/api/projects/${id}`, { method: 'DELETE' }); // Corrected API endpoint
                        this.loadProjects();
                    }
                },

                async sendNotification() {
                    const response = await fetch('/api/notifications', { // Corrected API endpoint
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.notificationForm)
                    });

                    if (response.ok) {
                        this.notificationForm = { roles: [] };
                        alert('Notification sent successfully!');
                    }
                },

                async updateSettings() {
                    const response = await fetch('/api/settings', { // Corrected API endpoint
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.settings)
                    });

                    if (response.ok) {
                        alert('Settings updated successfully!');
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX'
                    }).format(amount || 0);
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString();
                },

                getStatusClass(status) {
                    const classes = {
                        pending: 'bg-yellow-100 text-yellow-800',
                        approved: 'bg-green-100 text-green-800',
                        rejected: 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                }
            }
        }
    </script>
@endsection
