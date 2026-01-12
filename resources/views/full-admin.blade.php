@extends('layouts.app')

@section('title', 'BSS Full Admin Panel')

@section('content')
    <div x-data="adminPanel()" class="flex h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 shadow-2xl text-white">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold">BSS Admin Panel</h1>
                <p class="text-gray-400 text-sm">Full Control</p>
            </div>
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </button>
                    <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-users mr-3"></i> Members
                    </button>
                    <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-money-bill-wave mr-3"></i> Loans
                    </button>
                    <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-exchange-alt mr-3"></i> Transactions
                    </button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-project-diagram mr-3"></i> Projects
                    </button>
                    <button @click="activeTab = 'savings'" :class="activeTab === 'savings' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-piggy-bank mr-3"></i> Savings
                    </button>
                    <button @click="activeTab = 'meetings'" :class="activeTab === 'meetings' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-calendar mr-3"></i> Meetings
                    </button>
                    <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-file-alt mr-3"></i> Documents
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full text-left px-4 py-3 flex items-center transition-colors">
                        <i class="fas fa-bell mr-3"></i> Notifications
                    </button>
                    <button @click="activeTab = 'analytics'" :class="activeTab === 'analytics' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-chart-bar mr-3"></i> Analytics
                    </button>
                    <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </button>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <header class="bg-white/10 backdrop-blur-md border-b border-white/20 p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-white" x-text="getTabTitle()"></h2>
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="text-white">
                                <i class="fas fa-user-circle mr-2"></i>
                                <span x-text="'{{ Auth::user()->name }}'"></span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        @else
                            <div class="text-white">
                                <i class="fas fa-user-circle mr-2"></i>
                                <span>Guest User</span>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="p-6 h-full overflow-y-auto">
                <!-- Dashboard -->
                <div x-show="activeTab === 'dashboard'" class="space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-blue-500 to-blue-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100">Total Members</p>
                                    <p class="text-3xl font-bold" x-text="members.length">0</p>
                                </div>
                                <i class="fas fa-users text-4xl text-blue-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-green-500 to-green-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100">Total Savings</p>
                                    <p class="text-3xl font-bold" x-text="formatCurrency(members.reduce((sum, m) => sum + (m.savings || 0), 0))">0</p>
                                </div>
                                <i class="fas fa-piggy-bank text-4xl text-green-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100">Active Loans</p>
                                    <p class="text-3xl font-bold" x-text="loans.filter(l => l.status === 'approved').length">0</p>
                                </div>
                                <i class="fas fa-money-bill-wave text-4xl text-yellow-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-purple-500 to-purple-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100">Total Projects</p>
                                    <p class="text-3xl font-bold" x-text="projects.length">0</p>
                                </div>
                                <i class="fas fa-project-diagram text-4xl text-purple-200"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="glass-card rounded-xl p-6 border border-white/20">
                            <h3 class="text-xl font-bold text-white mb-4">Monthly Savings</h3>
                            <canvas id="savingsChart" width="400" height="200"></canvas>
                        </div>
                        <div class="glass-card rounded-xl p-6 border border-white/20">
                            <h3 class="text-xl font-bold text-white mb-4">Loan Status</h3>
                            <canvas id="loanChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Members -->
                <div x-show="activeTab === 'members'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">Member Management</h3>
                        <button @click="showAddMember = true" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Member
                        </button>
                    </div>
                    
                    <div class="glass-card rounded-xl border border-white/20 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Member ID</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Name</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Email</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Role</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Savings</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="member in members" :key="member.member_id">
                                        <tr class="border-b border-white/10 hover:bg-white/5">
                                            <td class="px-6 py-4 text-white" x-text="member.member_id"></td>
                                            <td class="px-6 py-4 text-white" x-text="member.full_name"></td>
                                            <td class="px-6 py-4 text-white" x-text="member.email"></td>
                                            <td class="px-6 py-4 text-white" x-text="member.role"></td>
                                            <td class="px-6 py-4 text-white" x-text="formatCurrency(member.savings)"></td>
                                            <td class="px-6 py-4">
                                                <button class="text-blue-400 hover:text-blue-300 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-400 hover:text-red-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Loans -->
                <div x-show="activeTab === 'loans'" class="space-y-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Loan Management</h3>
                        <button @click="showAddLoan = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>Add Loan
                        </button>
                    </div>
                    
                    <div class="glass-card rounded-xl border border-white/20 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Loan ID</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Member</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Amount</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Purpose</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Status</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="loan in loans" :key="loan.loan_id">
                                        <tr class="border-b border-white/10 hover:bg-white/5">
                                            <td class="px-6 py-4 text-white" x-text="loan.loan_id"></td>
                                            <td class="px-6 py-4 text-white" x-text="getMemberName(loan.member_id)"></td>
                                            <td class="px-6 py-4 text-white" x-text="formatCurrency(loan.amount)"></td>
                                            <td class="px-6 py-4 text-white" x-text="loan.purpose"></td>
                                            <td class="px-6 py-4 text-white">
                                                <span :class="getStatusClass(loan.status)" class="px-2 py-1 rounded-full text-xs" x-text="loan.status"></span>
                                            </td>
                                            <td class="px-6 py-4 text-white space-x-2">
                                                <button x-show="loan.status === 'pending'" @click="approveLoan(loan)" class="text-green-400 hover:text-green-300">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button x-show="loan.status === 'pending'" @click="rejectLoan(loan)" class="text-red-400 hover:text-red-300">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div x-show="activeTab === 'transactions'" class="space-y-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Transaction Management</h3>
                        <button @click="showAddTransaction = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>Add Transaction
                        </button>
                    </div>
                    
                    <div class="glass-card rounded-xl border border-white/20 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Member</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Amount</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Type</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="transaction in transactions" :key="transaction.transaction_id">
                                        <tr class="border-b border-white/10 hover:bg-white/5">
                                            <td class="px-6 py-4 text-white" x-text="transaction.transaction_id"></td>
                                            <td class="px-6 py-4 text-white" x-text="getMemberName(transaction.member_id)"></td>
                                            <td class="px-6 py-4 text-white" x-text="formatCurrency(transaction.amount)"></td>
                                            <td class="px-6 py-4 text-white" x-text="transaction.type"></td>
                                            <td class="px-6 py-4 text-white">Today</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Projects -->
                <div x-show="activeTab === 'projects'" class="space-y-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Project Management</h3>
                        <button @click="showAddProject = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>Add Project
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="project in projects" :key="project.project_id">
                            <div class="glass-card rounded-xl shadow p-6">
                                <h4 class="text-lg font-bold mb-2 text-white" x-text="project.name"></h4>
                                <p class="text-gray-300 mb-4" x-text="project.description"></p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-white">
                                        <span>Budget:</span>
                                        <span x-text="formatCurrency(project.budget)"></span>
                                    </div>
                                    <div class="flex justify-between text-white">
                                        <span>Progress:</span>
                                        <span x-text="project.progress + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                    </div>
                                </div>
                                <div class="mt-4 flex space-x-2">
                                    <button @click="editProject(project)" class="text-blue-400 hover:text-blue-300">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteProject(project)" class="text-red-400 hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Other Tabs -->
                <div x-show="activeTab === 'savings'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-piggy-bank text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Savings Management</h3>
                        <p class="text-gray-300 mb-4">Advanced savings tracking and management features</p>
                        <div class="bg-blue-900/50 border border-blue-700 rounded-lg p-4">
                            <p class="text-blue-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-blue-300 text-sm mt-2">Individual savings accounts, interest calculations, automated plans</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'meetings'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-calendar text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Meeting Management</h3>
                        <p class="text-gray-300 mb-4">Schedule and manage group meetings</p>
                        <div class="bg-green-900/50 border border-green-700 rounded-lg p-4">
                            <p class="text-green-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-green-300 text-sm mt-2">Meeting scheduling, attendance tracking, minutes recording</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'documents'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-file-alt text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Document Management</h3>
                        <p class="text-gray-300 mb-4">Upload and manage important documents</p>
                        <div class="bg-purple-900/50 border border-purple-700 rounded-lg p-4">
                            <p class="text-purple-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-purple-300 text-sm mt-2">File upload, categorization, version control, access permissions</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'notifications'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-bell text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Notification System</h3>
                        <p class="text-gray-300 mb-4">Send notifications to members</p>
                        <div class="bg-yellow-900/50 border border-yellow-700 rounded-lg p-4">
                            <p class="text-yellow-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-yellow-300 text-sm mt-2">Broadcast messages, role-based targeting, email notifications</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'analytics'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-chart-bar text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Advanced Analytics</h3>
                        <p class="text-gray-300 mb-4">Comprehensive reports and insights</p>
                        <div class="bg-indigo-900/50 border border-indigo-700 rounded-lg p-4">
                            <p class="text-indigo-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-indigo-300 text-sm mt-2">Financial reports, member analytics, performance metrics</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'settings'" class="text-white">
                    <div class="glass-card rounded-lg shadow p-8 text-center">
                        <i class="fas fa-cog text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">System Settings</h3>
                        <p class="text-gray-300 mb-4">Configure system parameters</p>
                        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-4">
                            <p class="text-gray-200 font-semibold">🚀 Features coming soon...</p>
                            <p class="text-gray-300 text-sm mt-2">Interest rates, fees, limits, company settings</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modals -->
        <div x-show="showAddMember" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddMember = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add New Member</h3>
                <form @submit.prevent="addMember()">
                    <div class="space-y-4">
                        <input x-model="newMember.member_id" type="text" placeholder="Member ID" class="form-input" required>
                        <input x-model="newMember.full_name" type="text" placeholder="Full Name" class="form-input" required>
                        <input x-model="newMember.email" type="email" placeholder="Email" class="form-input" required>
                        <input x-model="newMember.location" type="text" placeholder="Location" class="form-input" required>
                        <input x-model="newMember.occupation" type="text" placeholder="Occupation" class="form-input" required>
                        <input x-model="newMember.contact" type="text" placeholder="Contact" class="form-input" required>
                        <select x-model="newMember.role" class="form-input" required>
                            <option value="">Select Role</option>
                            <option value="client">Client</option>
                            <option value="shareholder">Shareholder</option>
                            <option value="cashier">Cashier</option>
                            <option value="td">TD</option>
                            <option value="ceo">CEO</option>
                        </select>
                        <input x-model="newMember.savings" type="number" placeholder="Initial Savings" class="form-input">
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddMember = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Member</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showAddLoan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddLoan = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add New Loan</h3>
                <form @submit.prevent="addLoan()">
                    <div class="space-y-4">
                        <input x-model="newLoan.loan_id" type="text" placeholder="Loan ID" class="form-input" required>
                        <select x-model="newLoan.member_id" class="form-input" required>
                            <option value="">Select Member</option>
                            <template x-for="member in members" :key="member.member_id">
                                <option :value="member.member_id" x-text="member.full_name"></option>
                            </template>
                        </select>
                        <input x-model="newLoan.amount" type="number" placeholder="Amount" class="form-input" required>
                        <input x-model="newLoan.purpose" type="text" placeholder="Purpose" class="form-input" required>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddLoan = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Loan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showAddTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddTransaction = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add Transaction</h3>
                <form @submit.prevent="addTransaction()">
                    <div class="space-y-4">
                        <input x-model="newTransaction.transaction_id" type="text" placeholder="Transaction ID" class="form-input" required>
                        <select x-model="newTransaction.member_id" class="form-input" required>
                            <option value="">Select Member</option>
                            <template x-for="member in members" :key="member.member_id">
                                <option :value="member.member_id" x-text="member.full_name"></option>
                            </template>
                        </select>
                        <input x-model="newTransaction.amount" type="number" placeholder="Amount" class="form-input" required>
                        <select x-model="newTransaction.type" class="form-input" required>
                            <option value="">Select Type</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddTransaction = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Transaction</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showAddProject" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="showAddProject = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Add Project</h3>
                <form @submit.prevent="addProject()">
                    <div class="space-y-4">
                        <input x-model="newProject.project_id" type="text" placeholder="Project ID" class="form-input" required>
                        <input x-model="newProject.name" type="text" placeholder="Project Name" class="form-input" required>
                        <textarea x-model="newProject.description" placeholder="Description" rows="3" class="form-input" required></textarea>
                        <input x-model="newProject.budget" type="number" placeholder="Budget" class="form-input" required>
                        <input x-model="newProject.progress" type="number" placeholder="Progress %" min="0" max="100" class="form-input">
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showAddProject = false" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200">Add Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-input,
        .form-textarea,
        .form-select {
            @apply w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400;
            transition: all 0.2s ease-in-out;
        }
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            @apply border-blue-500 ring-2 ring-blue-500 ring-opacity-50 outline-none;
        }
    </style>

    <script>
        function adminPanel() {
            return {
                activeTab: 'dashboard',
                showAddMember: false,
                showAddLoan: false,
                showAddTransaction: false,
                showAddProject: false,
                members: [
                    {member_id: 'BSS001', full_name: 'MUSUBIKA LYDIA', email: 'lydia@bssgroup.com', location: 'Mayuge', occupation: 'Accountant', contact: '+256759159154', role: 'cashier', savings: 5000000},
                    {member_id: 'BSS002', full_name: 'MBULAKYALO MATHIAS', email: 'mathias@bssgroup.com', location: 'Kampala', occupation: 'Engineer', contact: '+256700123456', role: 'td', savings: 3000000},
                    {member_id: 'BSS003', full_name: 'NKULEGA RAYMON', email: 'raymon@bssgroup.com', location: 'Jinja', occupation: 'Business Owner', contact: '+256701234567', role: 'ceo', savings: 8000000},
                    {member_id: 'BSS004', full_name: 'NAKATO SARAH', email: 'sarah@bssgroup.com', location: 'Mukono', occupation: 'Teacher', contact: '+256702345678', role: 'shareholder', savings: 2500000},
                    {member_id: 'BSS005', full_name: 'MUSOKE JOHN', email: 'john@bssgroup.com', location: 'Entebbe', occupation: 'Farmer', contact: '+256703456789', role: 'client', savings: 1800000}
                ],
                loans: [
                    {loan_id: 'LOAN001', member_id: 'BSS004', amount: 1000000, purpose: 'Business expansion', status: 'approved'},
                    {loan_id: 'LOAN002', member_id: 'BSS005', amount: 500000, purpose: 'Agricultural equipment', status: 'pending'}
                ],
                transactions: [
                    {transaction_id: 'TXN001', member_id: 'BSS001', amount: 500000, type: 'deposit'},
                    {transaction_id: 'TXN002', member_id: 'BSS004', amount: 1000000, type: 'loanDisbursement'}
                ],
                projects: [
                    {project_id: 'PRJ001', name: 'Agriculture Project', description: 'Investment in modern farming techniques', budget: 10000000, progress: 75},
                    {project_id: 'PRJ002', name: 'Real Estate Development', description: 'Commercial property development', budget: 25000000, progress: 45},
                    {project_id: 'PRJ003', name: 'Education Initiative', description: 'School infrastructure improvement', budget: 8000000, progress: 90}
                ],
                newMember: {},
                newLoan: {},
                newTransaction: {},
                newProject: {},

                init() {
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                },

                getTabTitle() {
                    const titles = {
                        dashboard: 'Dashboard Overview',
                        members: 'Member Management',
                        loans: 'Loan Management',
                        transactions: 'Transaction Management',
                        projects: 'Project Management',
                        savings: 'Savings Management',
                        meetings: 'Meeting Management',
                        documents: 'Document Management',
                        notifications: 'Notification System',
                        analytics: 'Analytics & Reports',
                        settings: 'System Settings'
                    };
                    return titles[this.activeTab];
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX'
                    }).format(amount || 0);
                },

                getMemberName(memberId) {
                    const member = this.members.find(m => m.member_id === memberId);
                    return member ? member.full_name : 'Unknown';
                },

                getStatusClass(status) {
                    const classes = {
                        pending: 'bg-yellow-100 text-yellow-800',
                        approved: 'bg-green-100 text-green-800',
                        rejected: 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },

                logout() {
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = '{{ route('logout') }}';
                    }
                },

                addMember() {
                    this.members.push({...this.newMember});
                    this.newMember = {};
                    this.showAddMember = false;
                    alert('Member added successfully!');
                },

                editMember(member) {
                    alert('Edit member: ' + member.full_name);
                },

                deleteMember(member) {
                    if (confirm('Delete member: ' + member.full_name + '?')) {
                        const index = this.members.findIndex(m => m.member_id === member.member_id);
                        this.members.splice(index, 1);
                        alert('Member deleted!');
                    }
                },

                addLoan() {
                    this.newLoan.status = 'pending';
                    this.loans.push({...this.newLoan});
                    this.newLoan = {};
                    this.showAddLoan = false;
                    alert('Loan added successfully!');
                },

                approveLoan(loan) {
                    loan.status = 'approved';
                    alert('Loan approved!');
                },

                rejectLoan(loan) {
                    loan.status = 'rejected';
                    alert('Loan rejected!');
                },

                addTransaction() {
                    this.transactions.push({...this.newTransaction});
                    this.newTransaction = {};
                    this.showAddTransaction = false;
                    alert('Transaction added successfully!');
                },

                addProject() {
                    this.newProject.progress = this.newProject.progress || 0;
                    this.projects.push({...this.newProject});
                    this.newProject = {};
                    this.showAddProject = false;
                    alert('Project added successfully!');
                },

                editProject(project) {
                    alert('Edit project: ' + project.name);
                },

                deleteProject(project) {
                    if (confirm('Delete project: ' + project.name + '?')) {
                        const index = this.projects.findIndex(p => p.project_id === project.project_id);
                        this.projects.splice(index, 1);
                        alert('Project deleted!');
                    }
                },

                initCharts() {
                    // Savings Chart
                    const savingsCtx = document.getElementById('savingsChart');
                    if (savingsCtx) {
                        new Chart(savingsCtx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                                datasets: [{
                                    label: 'Monthly Savings',
                                    data: [12000000, 19000000, 15000000, 25000000, 22000000, 30000000],
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Loan Chart
                    const loanCtx = document.getElementById('loanChart');
                    if (loanCtx) {
                        new Chart(loanCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Approved', 'Pending', 'Rejected'],
                                datasets: [{
                                    data: [1, 1, 0],
                                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { position: 'bottom' }
                                }
                            }
                        });
                    }
                }
            }
        }
    </script>
@endsection