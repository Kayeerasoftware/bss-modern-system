@extends('layouts.app')

@section('title', 'BSS Admin Panel')

@section('content')
    <div x-data="adminPanel()" class="flex h-screen bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-xl">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">BSS Admin</h1>
                        <p class="text-xs text-gray-400 mt-1">Management System</p>
                    </div>
                </div>
            </div>
            <nav class="p-4 space-y-1">
                <button @click="activeTab = 'dashboard'"
                        :class="activeTab === 'dashboard' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-500/10 group-hover:bg-blue-500/20 mr-3">
                        <i class="fas fa-tachometer-alt text-blue-400"></i>
                    </div>
                    <span class="font-medium">Dashboard</span>
                </button>

                <button @click="activeTab = 'members'"
                        :class="activeTab === 'members' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-500/10 group-hover:bg-green-500/20 mr-3">
                        <i class="fas fa-users text-green-400"></i>
                    </div>
                    <span class="font-medium">Members</span>
                </button>

                <button @click="activeTab = 'loans'"
                        :class="activeTab === 'loans' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-500/10 group-hover:bg-yellow-500/20 mr-3">
                        <i class="fas fa-money-bill-wave text-yellow-400"></i>
                    </div>
                    <span class="font-medium">Loans</span>
                </button>

                <button @click="activeTab = 'transactions'"
                        :class="activeTab === 'transactions' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-purple-500/10 group-hover:bg-purple-500/20 mr-3">
                        <i class="fas fa-exchange-alt text-purple-400"></i>
                    </div>
                    <span class="font-medium">Transactions</span>
                </button>

                <button @click="activeTab = 'projects'"
                        :class="activeTab === 'projects' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-500/10 group-hover:bg-indigo-500/20 mr-3">
                        <i class="fas fa-project-diagram text-indigo-400"></i>
                    </div>
                    <span class="font-medium">Projects</span>
                </button>

                <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 group-hover:bg-red-500/20 mr-3">
                        <i class="fas fa-bell text-red-400"></i>
                    </div>
                    <span class="font-medium">Notifications</span>
                </button>

                <button @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-3 flex items-center rounded-xl transition-all duration-300 group">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-600/10 group-hover:bg-gray-600/20 mr-3">
                        <i class="fas fa-cog text-gray-400"></i>
                    </div>
                    <span class="font-medium">Settings</span>
                </button>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700 bg-gray-800/50 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-900 shadow-sm border-b dark:border-gray-800 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent"
                            x-text="getTabTitle()"></h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="getTabDescription()"></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Search..."
                                   class="pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" class="fade-in space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Total Members</p>
                                    <p class="text-3xl font-bold mt-2" x-text="stats.totalMembers">0</p>
                                    <div class="flex items-center mt-2 text-blue-200 text-sm">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        <span>20% from last month</span>
                                    </div>
                                </div>
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-sm font-medium">Total Savings</p>
                                    <p class="text-2xl font-bold mt-2" x-text="formatCurrency(stats.totalSavings)">500000</p>
                                    <div class="flex items-center mt-2 text-emerald-200 text-sm">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        <span>8.5% growth</span>
                                    </div>
                                </div>
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-piggy-bank text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-100 text-sm font-medium">Active Loans for you</p>
                                    <p class="text-3xl font-bold mt-2" x-text="stats.activeLoans">0</p>
                                    <div class="flex items-center mt-2 text-amber-200 text-sm">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        <span>24 active loans</span>
                                    </div>
                                </div>
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-violet-500 to-violet-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-violet-100 text-sm font-medium">Total Projects</p>
                                    <p class="text-3xl font-bold mt-2" x-text="stats.totalProjects">0</p>
                                    <div class="flex items-center mt-2 text-violet-200 text-sm">
                                        <i class="fas fa-tasks mr-1"></i>
                                        <span>5 ongoing</span>
                                    </div>
                                </div>
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-project-diagram text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <button @click="activeTab = 'members'; showAddMemberModal = true"
                                    class="flex flex-col items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Member</span>
                            </button>

                            <button @click="activeTab = 'loans'"
                                    class="flex flex-col items-center justify-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors duration-200">
                                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-800 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-hand-holding-usd text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Process Loans</span>
                            </button>

                            <button @click="activeTab = 'transactions'; showAddTransactionModal = true"
                                    class="flex flex-col items-center justify-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors duration-200">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-exchange-alt text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Transaction</span>
                            </button>

                            <button @click="activeTab = 'notifications'"
                                    class="flex flex-col items-center justify-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors duration-200">
                                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-800 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-bullhorn text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Send Alert</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Members Tab -->
                <div x-show="activeTab === 'members'" class="fade-in">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Member Management</h3>
                            <p class="text-gray-600 dark:text-gray-400">Manage all member accounts and profiles</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <button @click="showAddMemberModal = true"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-plus mr-2"></i>Add Member
                            </button>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Member</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Savings</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="member in members" :key="member.id">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                        <span x-text="member.full_name.charAt(0)"></span>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900 dark:text-white" x-text="member.full_name"></div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="member.member_id"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100" x-text="member.email"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="member.contact || 'N/A'"></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span :class="getRoleBadgeClass(member.role)"
                                                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                                      x-text="member.role"></span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="formatCurrency(member.savings)"></td>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-2">
                                                    <button @click="editMember(member)"
                                                            class="w-8 h-8 flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors duration-200">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </button>
                                                    <button @click="viewMember(member)"
                                                            class="w-8 h-8 flex items-center justify-center bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </button>
                                                    <button @click="deleteMember(member.id)"
                                                            class="w-8 h-8 flex items-center justify-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors duration-200">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Loans Tab (Redesigned) -->
                <div x-show="activeTab === 'loans'" class="fade-in">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Loan Management</h3>
                            <p class="text-gray-600 dark:text-gray-400">Review and manage loan applications</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Loan Stats -->
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-100 text-sm font-medium">Pending Loans</p>
                                    <p class="text-3xl font-bold mt-2" x-text="stats.pendingLoans || 0">0</p>
                                </div>
                                <i class="fas fa-clock text-3xl opacity-80"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-sm font-medium">Approved Loans</p>
                                    <p class="text-3xl font-bold mt-2" x-text="stats.approvedLoans || 0">0</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl opacity-80"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-rose-500 to-rose-600 rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-rose-100 text-sm font-medium">Total Loan Amount</p>
                                    <p class="text-2xl font-bold mt-2" x-text="formatCurrency(stats.totalLoanAmount || 0)">0</p>
                                </div>
                                <i class="fas fa-money-bill-wave text-3xl opacity-80"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Loan Details</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="loan in loans" :key="loan.id">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white" x-text="loan.loan_id"></div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400" x-text="loan.member?.full_name"></div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1" x-text="loan.purpose"></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-lg font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(loan.amount)"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="loan.duration + ' months'"></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span :class="getStatusClass(loan.status)"
                                                      class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium"
                                                      x-text="loan.status.charAt(0).toUpperCase() + loan.status.slice(1)"></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-2">
                                                    <template x-if="loan.status === 'pending'">
                                                        <button @click="approveLoan(loan.id)"
                                                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-200">
                                                            <i class="fas fa-check mr-2"></i>Approve
                                                        </button>
                                                    </template>
                                                    <template x-if="loan.status === 'pending'">
                                                        <button @click="rejectLoan(loan.id)"
                                                                class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-200">
                                                            <i class="fas fa-times mr-2"></i>Reject
                                                        </button>
                                                    </template>
                                                    <button @click="viewLoan(loan)"
                                                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200">
                                                        <i class="fas fa-eye mr-2"></i>View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Other Tabs (Transactions, Projects, Notifications, Settings) -->
                <!-- Note: These sections follow similar redesign patterns -->
                <!-- Add your redesigned content for other tabs here -->

            </main>
        </div>

        <!-- Modals (Redesigned) -->
        <!-- Add Member Modal -->
        <div x-show="showAddMemberModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div @click.away="showAddMemberModal = false"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Add New Member</h3>
                        <button @click="showAddMemberModal = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <form @submit.prevent="addMember()" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Form fields with improved styling -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Member ID</label>
                            <input x-model="memberForm.member_id" type="text"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter member ID" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                            <input x-model="memberForm.full_name" type="text"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter full name" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                            <input x-model="memberForm.email" type="email"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter email" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                            <input x-model="memberForm.contact" type="tel"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter phone number" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select x-model="memberForm.role"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   required>
                                <option value="">Select Role</option>
                                <option value="client">Client</option>
                                <option value="shareholder">Shareholder</option>
                                <option value="cashier">Cashier</option>
                                <option value="td">TD</option>
                                <option value="ceo">CEO</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Initial Savings</label>
                            <input x-model="memberForm.savings" type="number"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter amount">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <input x-model="memberForm.location" type="text"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Enter address">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t dark:border-gray-700">
                        <button type="button" @click="showAddMemberModal = false"
                                class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg">
                            Add Member
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add similar redesigned modals for Transactions, Projects, etc. -->

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

                getTabDescription() {
                    const descriptions = {
                        dashboard: 'Overview of your organization\'s performance',
                        members: 'Manage member accounts and profiles',
                        loans: 'Review and process loan applications',
                        transactions: 'Track all financial transactions',
                        projects: 'Monitor and manage ongoing projects',
                        notifications: 'Send alerts and updates to members',
                        settings: 'Configure system preferences and rules'
                    };
                    return descriptions[this.activeTab];
                },

                async loadDashboard() {
                    const response = await fetch('/api/admin/dashboard');
                    this.stats = await response.json();
                },

                async loadMembers() {
                    const response = await fetch('/api/members');
                    this.members = await response.json();
                },

                async loadLoans() {
                    const response = await fetch('/api/loans');
                    this.loans = await response.json();
                },

                async loadTransactions() {
                    const response = await fetch('/api/transactions');
                    this.transactions = await response.json();
                },

                async loadProjects() {
                    const response = await fetch('/api/projects');
                    this.projects = await response.json();
                },

                async loadSettings() {
                    const response = await fetch('/api/settings');
                    this.settings = await response.json();
                },

                async addMember() {
                    const response = await fetch('/api/members', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.memberForm)
                    });

                    if (response.ok) {
                        this.loadMembers();
                        this.loadDashboard();
                        this.showAddMemberModal = false;
                        this.memberForm = {};
                        this.showToast('Member added successfully!', 'success');
                    }
                },

                async deleteMember(id) {
                    if (confirm('Are you sure you want to delete this member?')) {
                        await fetch(`/api/members/${id}`, { method: 'DELETE' });
                        this.loadMembers();
                        this.loadDashboard();
                        this.showToast('Member deleted successfully!', 'info');
                    }
                },

                async approveLoan(id) {
                    await fetch(`/api/loans/${id}/approve`, { method: 'POST' });
                    this.loadLoans();
                    this.loadDashboard();
                    this.showToast('Loan approved successfully!', 'success');
                },

                async rejectLoan(id) {
                    await fetch(`/api/loans/${id}/reject`, { method: 'POST' });
                    this.loadLoans();
                    this.showToast('Loan rejected!', 'info');
                },

                async addTransaction() {
                    const response = await fetch('/api/transactions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.transactionForm)
                    });

                    if (response.ok) {
                        this.loadTransactions();
                        this.loadMembers();
                        this.showAddTransactionModal = false;
                        this.transactionForm = {};
                        this.showToast('Transaction added successfully!', 'success');
                    }
                },

                async addProject() {
                    const response = await fetch('/api/projects', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.projectForm)
                    });

                    if (response.ok) {
                        this.loadProjects();
                        this.showAddProjectModal = false;
                        this.projectForm = {};
                        this.showToast('Project added successfully!', 'success');
                    }
                },

                async deleteProject(id) {
                    if (confirm('Are you sure you want to delete this project?')) {
                        await fetch(`/api/projects/${id}`, { method: 'DELETE' });
                        this.loadProjects();
                        this.showToast('Project deleted successfully!', 'info');
                    }
                },

                async sendNotification() {
                    const response = await fetch('/api/notifications', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.notificationForm)
                    });

                    if (response.ok) {
                        this.notificationForm = { roles: [] };
                        this.showToast('Notification sent successfully!', 'success');
                    }
                },

                async updateSettings() {
                    const response = await fetch('/api/settings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.settings)
                    });

                    if (response.ok) {
                        this.showToast('Settings updated successfully!', 'success');
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount || 0);
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                getStatusClass(status) {
                    const classes = {
                        pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        approved: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                        rejected: 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                        active: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        completed: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
                },

                getRoleBadgeClass(role) {
                    const classes = {
                        client: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        shareholder: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                        cashier: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                        td: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        ceo: 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'
                    };
                    return classes[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
                },

                showToast(message, type = 'info') {
                    // Create toast notification
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${this.getToastClass(type)}`;
                    toast.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas ${this.getToastIcon(type)} mr-3"></i>
                            <span class="font-medium">${message}</span>
                        </div>
                    `;

                    document.body.appendChild(toast);

                    // Animate in
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full');
                    }, 10);

                    // Remove after 3 seconds
                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => {
                            document.body.removeChild(toast);
                        }, 300);
                    }, 3000);
                },

                getToastClass(type) {
                    const classes = {
                        success: 'bg-emerald-500 text-white',
                        error: 'bg-rose-500 text-white',
                        warning: 'bg-amber-500 text-white',
                        info: 'bg-blue-500 text-white'
                    };
                    return classes[type] || 'bg-blue-500 text-white';
                },

                getToastIcon(type) {
                    const icons = {
                        success: 'fa-check-circle',
                        error: 'fa-exclamation-circle',
                        warning: 'fa-exclamation-triangle',
                        info: 'fa-info-circle'
                    };
                    return icons[type] || 'fa-info-circle';
                },

                viewMember(member) {
                    // Implement view member details
                    console.log('View member:', member);
                },

                editMember(member) {
                    // Implement edit member
                    console.log('Edit member:', member);
                },

                viewLoan(loan) {
                    // Implement view loan details
                    console.log('View loan:', loan);
                }
            }
        }
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .dark ::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    </style>
@endsection
