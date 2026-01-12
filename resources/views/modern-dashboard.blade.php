@extends('layouts.app')

@section('title', 'BSS Investment Group Dashboard')

@section('content')
    <div x-data="dashboardApp()" class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white dark:bg-gray-800 shadow-lg p-4 flex items-center justify-between">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 dark:text-gray-300">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">BSS Dashboard</h1>
            <button @click="logout()" class="text-red-600">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </button>
        </div>

        <div class="flex">
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>
            
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="lg:translate-x-0 fixed lg:static inset-y-0 left-0 z-50 w-64 sidebar-gradient text-white min-h-screen shadow-2xl transition-transform duration-300">
                <div class="p-4 lg:p-6">
                    <div class="flex items-center space-x-3 mb-6 lg:mb-8">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-circle text-xl lg:text-2xl"></i>
                        </div>
                        <div class="hidden lg:block">
                            <h3 class="font-semibold" x-text="currentUser?.full_name"></h3>
                            <p class="text-sm opacity-75" x-text="currentUser?.role?.toUpperCase()"></p>
                        </div>
                    </div>
                    <nav class="space-y-1 lg:space-y-2">
                        <template x-for="tab in dashboardTabs" :key="tab.id">
                            <button @click="activeTab = tab.id; sidebarOpen = false"
                                    :class="activeTab === tab.id ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10'"
                                    class="w-full text-left px-3 py-2 lg:px-4 lg:py-3 rounded-xl transition-all duration-200 flex items-center space-x-3">
                                <i :class="tab.icon" class="w-4 lg:w-5"></i>
                                <span class="text-sm lg:text-base" x-text="tab.name"></span>
                            </button>
                        </template>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 lg:ml-0 p-4 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Dashboard Header -->
                    <div class="mb-6 lg:mb-8 hidden lg:block">
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2" x-text="currentTabName + ' Dashboard'"></h1>
                        <p class="text-gray-600 dark:text-gray-400">Welcome back, <span x-text="currentUser?.full_name"></span></p>
                    </div>

                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                currentUser: null,
                currentRole: '',
                dashboardData: {},
                activeTab: 'overview',
                sidebarOpen: false,
                loanEstimate: 'Enter amount to estimate interest',

                get currentTabName() {
                    const tab = this.dashboardTabs.find(t => t.id === this.activeTab);
                    return tab ? tab.name : 'Dashboard';
                },

                get dashboardTabs() {
                    const tabs = {
                        client: [
                            { id: 'overview', name: 'Overview', icon: 'fas fa-home' },
                            { id: 'loans', name: 'My Loans', icon: 'fas fa-money-bill-wave' },
                            { id: 'savings', name: 'Savings', icon: 'fas fa-piggy-bank' },
                            { id: 'documents', name: 'Documents', icon: 'fas fa-file-alt' }
                        ],
                        shareholder: [
                            { id: 'overview', name: 'Overview', icon: 'fas fa-chart-line' },
                            { id: 'members', name: 'Members', icon: 'fas fa-users' },
                            { id: 'portfolio', name: 'Portfolio', icon: 'fas fa-briefcase' },
                            { id: 'analytics', name: 'Analytics', icon: 'fas fa-chart-pie' }
                        ],
                        cashier: [
                            { id: 'overview', name: 'Financial Summary', icon: 'fas fa-calculator' },
                            { id: 'loans', name: 'Pending Loans', icon: 'fas fa-clock' },
                            { id: 'transactions', name: 'Transactions', icon: 'fas fa-exchange-alt' },
                            { id: 'analytics', name: 'Analytics', icon: 'fas fa-chart-line' }
                        ],
                        td: [
                            { id: 'overview', name: 'Projects', icon: 'fas fa-project-diagram' },
                            { id: 'progress', name: 'Progress', icon: 'fas fa-chart-bar' },
                            { id: 'team', name: 'Team', icon: 'fas fa-users-cog' },
                            { id: 'meetings', name: 'Meetings', icon: 'fas fa-calendar' },
                            { id: 'documents', name: 'Documents', icon: 'fas fa-folder' }
                        ],
                        ceo: [
                            { id: 'overview', name: 'Executive Summary', icon: 'fas fa-crown' },
                            { id: 'members', name: 'Members', icon: 'fas fa-users' },
                            { id: 'analytics', name: 'Analytics', icon: 'fas fa-chart-pie' },
                            { id: 'reports', name: 'Reports', icon: 'fas fa-file-alt' },
                            { id: 'meetings', name: 'Meetings', icon: 'fas fa-calendar' },
                            { id: 'documents', name: 'Documents', icon: 'fas fa-folder' }
                        ]
                    };
                    return tabs[this.currentRole] || [];
                },

                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                async apiCall(url, method = 'GET', data = null) {
                    const options = {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    };

                    if (data) options.body = JSON.stringify(data);

                    const response = await fetch(url, options);
                    return await response.json();
                },

                showToast(message, type = 'success') {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message, type }
                    }));
                },


                async logout() {
                    // Using route helper for logout
                    const response = await fetch('{{ route('logout') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    if (response.ok) {
                        this.currentUser = null;
                        this.currentRole = '';
                        this.showToast('Logged out successfully');
                        window.location.replace('/'); // Redirect to home or login page
                    } else {
                        this.showToast('Failed to logout.', 'error');
                    }
                },

                async loadDashboard() {
                    // Skip auth check for now
                    this.currentUser = { name: 'Test User', role: 'admin' };
                    this.currentRole = 'admin';

                    // Load dashboard data
                    try {
                        const response = await fetch('/api/debug-data');
                        const debugData = await response.json();
                        
                        this.dashboardData = {
                            members: debugData.members_data || [],
                            totalSavings: debugData.total_savings || 0,
                            total_savings: debugData.total_savings || 0,
                            loans: debugData.loans_data || [],
                            projects: [],
                            pending_loans: [],
                            condolenceFund: 50000000
                        };
                        
                        this.renderDashboard();
                    } catch (error) {
                        console.error('Failed to load dashboard data:', error);
                        this.showToast('Failed to load dashboard data', 'error');
                    }
                },

                renderDashboard() {
                    const content = document.getElementById('dashboardContent');
                    
                    // Use unified dashboard style for all roles
                    content.innerHTML = this.renderOverview();
                },

                renderOverview() {
                    return `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Members</p>
                                        <p class="text-2xl font-bold text-gray-900">${this.dashboardData.members?.length || 0}</p>
                                    </div>
                                    <i class="fas fa-users text-blue-500 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Savings</p>
                                        <p class="text-2xl font-bold text-gray-900">UGX ${(this.dashboardData.totalSavings || 0).toLocaleString()}</p>
                                    </div>
                                    <i class="fas fa-piggy-bank text-green-500 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Active Loans</p>
                                        <p class="text-2xl font-bold text-gray-900">${this.dashboardData.active_loans || 0}</p>
                                    </div>
                                    <i class="fas fa-money-bill-wave text-yellow-500 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Condolence Fund</p>
                                        <p class="text-2xl font-bold text-gray-900">UGX ${(this.dashboardData.condolenceFund || 0).toLocaleString()}</p>
                                    </div>
                                    <i class="fas fa-hand-holding-heart text-purple-500 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <h3 class="text-lg font-semibold mb-4">Members Overview</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-2 px-2 font-semibold text-gray-900">Name</th>
                                                <th class="text-left py-2 px-2 font-semibold text-gray-900">ID</th>
                                                <th class="text-left py-2 px-2 font-semibold text-gray-900">Savings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${this.dashboardData.members?.slice(0, 5).map(m => `
                                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                    <td class="py-2 px-2 text-gray-900">${m.full_name}</td>
                                                    <td class="py-2 px-2 text-gray-600">${m.member_id}</td>
                                                    <td class="py-2 px-2 text-green-600 font-medium">UGX ${m.savings?.toLocaleString()}</td>
                                                </tr>
                                            `).join('') || '<tr><td colspan="3" class="py-4 text-center text-gray-500">No members found</td></tr>'}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <h3 class="text-lg font-semibold mb-4">Pending Loans</h3>
                                <div class="space-y-3">
                                    ${this.dashboardData.pending_loans?.slice(0, 3).map(loan => `
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h4 class="font-medium">${loan.member_id}</h4>
                                                    <p class="text-sm text-gray-600">${loan.purpose}</p>
                                                </div>
                                                <span class="font-semibold text-gray-900">UGX ${loan.amount?.toLocaleString()}</span>
                                            </div>
                                        </div>
                                    `).join('') || '<p class="text-gray-500 text-center py-4">No pending loans</p>'}
                                </div>
                            </div>
                        </div>
                    `;
                },

                renderShareholderDashboard() {
                    return `
                        <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                            <h3 class="text-base lg:text-xl font-semibold text-gray-900 mb-4 lg:mb-6">Members Overview</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Name</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">ID</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Savings</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Loan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${this.dashboardData.members?.map(m => `
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-gray-900">${m.full_name}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-gray-600">${m.member_id}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-green-600 font-medium">₦${m.savings?.toLocaleString()}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-red-600 font-medium">₦${m.loan?.toLocaleString()}</td>
                                            </tr>
                                        `).join('') || ''}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                },

                renderCashierDashboard() {
                    return `
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Savings</p>
                                        <p class="text-lg lg:text-2xl font-bold text-green-600">₦${this.dashboardData.totalSavings?.toLocaleString() || '0'}</p>
                                    </div>
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-wallet text-green-600 text-sm lg:text-base"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Loans</p>
                                        <p class="text-lg lg:text-2xl font-bold text-red-600">₦${this.dashboardData.totalLoans?.toLocaleString() || '0'}</p>
                                    </div>
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-hand-holding-usd text-red-600 text-sm lg:text-base"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs lg:text-sm font-medium text-gray-600">Available Funds</p>
                                        <p class="text-lg lg:text-2xl font-bold text-blue-600">₦${((this.dashboardData.totalSavings || 0) - (this.dashboardData.totalLoans || 0)).toLocaleString()}</p>
                                    </div>
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-coins text-blue-600 text-sm lg:text-base"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                            <h3 class="text-base lg:text-xl font-semibold text-gray-900 mb-4 lg:mb-6">Pending Loans</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Member</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Amount</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900 hidden sm:table-cell">Purpose</th>
                                            <th class="text-left py-2 lg:py-3 px-2 lg:px-4 font-semibold text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${this.dashboardData.loans?.filter(l => l.status === 'pending').map(l => `
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-gray-900">${l.member_id}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-gray-900 font-medium">₦${l.amount?.toLocaleString()}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4 text-gray-600 hidden sm:table-cell">${l.purpose}</td>
                                                <td class="py-2 lg:py-3 px-2 lg:px-4">
                                                    <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                                        <button onclick="approveLoan('${l.loan_id}')" class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                                            Approve
                                                        </button>
                                                        <button onclick="rejectLoan('${l.loan_id}')" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                                            Reject
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        `).join('') || '<tr><td colspan="4" class="py-8 text-center text-gray-500">No pending loans</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                },

                renderTDDashboard() {
                    return `
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Active Projects</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                ${this.dashboardData.projects?.map(p => `
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-lg transition-shadow">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">${p.name}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Budget: UGX ${p.budget?.toLocaleString()}</p>
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600 dark:text-gray-400">Progress</span>
                                                <span class="text-gray-900 dark:text-white">${p.progress}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${p.progress}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-green-600 dark:text-green-400">ROI: ${p.roi}%</span>
                                            <span class="text-orange-600 dark:text-orange-400">Risk: ${p.risk_score}</span>
                                        </div>
                                    </div>
                                `).join('') || '<p class="text-gray-500 text-center py-8">No active projects</p>'}
                            </div>
                        </div>
                    `;
                },

                renderCEODashboard() {
                    return `
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="stats-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Members</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${this.dashboardData.members?.length || 0}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="stats-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Savings</p>
                                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">UGX ${this.dashboardData.totalSavings?.toLocaleString() || '0'}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-piggy-bank text-green-600 dark:text-green-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="stats-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Projects</p>
                                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">${this.dashboardData.projects?.length || 0}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-project-diagram text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="stats-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Condolence Fund</p>
                                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">UGX ${this.dashboardData.condolenceFund?.toLocaleString() || '0'}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-hand-holding-heart text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-900 dark:text-white font-medium">New member registered</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">2 hours ago</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-900 dark:text-white font-medium">Loan approved</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">5 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                },

                // This function is still needed for loan calculations within the dashboard views.
                async loadAnalyticsCharts() {
                    try {
                        const response = await this.apiCall('/api/analytics/dashboard?role=' + this.currentRole);
                        this.renderAnalyticsCharts(response);
                    } catch (error) {
                        console.error('Error loading analytics:', error);
                    }
                },

                renderAnalyticsCharts(data) {
                    const content = document.getElementById('dashboardContent');
                    content.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Members</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">${data.overview?.total_members || 0}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Savings</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX ${this.formatCurrency(data.overview?.total_savings || 0)}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-piggy-bank text-green-600 dark:text-green-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Loans</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX ${this.formatCurrency(data.overview?.total_loans || 0)}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-hand-holding-usd text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Available Funds</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX ${this.formatCurrency(data.overview?.available_funds || 0)}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-coins text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Savings Distribution</h3>
                                <canvas id="savingsChart" width="400" height="200"></canvas>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Monthly Transactions</h3>
                                <canvas id="transactionsChart" width="400" height="200"></canvas>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Loan Status</h3>
                                <canvas id="loanChart" width="400" height="200"></canvas>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Member Growth</h3>
                                <canvas id="growthChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <div class="text-center mt-8">
                            <a href="/analytics" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 font-medium inline-flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>View Full Analytics Dashboard
                            </a>
                        </div>
                    `;

                    setTimeout(() => this.createMiniCharts(data), 100);
                },

                createMiniCharts(data) {
                    const savingsCtx = document.getElementById('savingsChart')?.getContext('2d');
                    if (savingsCtx) {
                        new Chart(savingsCtx, {
                            type: 'doughnut',
                            data: {
                                labels: data.charts?.savings_distribution?.map(item => item.label) || [],
                                datasets: [{
                                    data: data.charts?.savings_distribution?.map(item => item.value) || [],
                                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });
                    }

                    const transactionsCtx = document.getElementById('transactionsChart')?.getContext('2d');
                    if (transactionsCtx) {
                        new Chart(transactionsCtx, {
                            type: 'line',
                            data: {
                                labels: data.charts?.monthly_transactions?.map(item => item.month) || [],
                                datasets: [{
                                    data: data.charts?.monthly_transactions?.map(item => item.count) || [],
                                    borderColor: '#8b5cf6',
                                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                        });
                    }

                    const loanCtx = document.getElementById('loanChart')?.getContext('2d');
                    if (loanCtx) {
                        new Chart(loanCtx, {
                            type: 'pie',
                            data: {
                                labels: data.charts?.loan_status_pie?.map(item => item.label) || [],
                                datasets: [{ data: data.charts?.loan_status_pie?.map(item => item.value) || [], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'] }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });
                    }

                    const growthCtx = document.getElementById('growthChart')?.getContext('2d');
                    if (growthCtx) {
                        new Chart(growthCtx, {
                            type: 'bar',
                            data: {
                                labels: data.charts?.member_growth?.map(item => item.month) || [],
                                datasets: [{ data: data.charts?.member_growth?.map(item => item.new_members) || [], backgroundColor: '#f97316' }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                        });
                    }
                },

                formatCurrency(amount) {
                    if (amount >= 1000000) return (amount / 1000000).toFixed(1) + 'M';
                    if (amount >= 1000) return (amount / 1000).toFixed(1) + 'K';
                    return amount?.toLocaleString() || '0';
                }
            }));
        });

    </script>
@endsection