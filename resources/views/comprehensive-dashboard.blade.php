@extends('layouts.app')

@section('title', 'BSS Comprehensive Dashboard')

@section('content')
    <div x-data="dashboardApp()" class="flex h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 shadow-2xl text-white">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold">BSS System</h1>
                <p class="text-gray-400 text-sm">Investment Group</p>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </button>
                    <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-users mr-3"></i> Members
                    </button>
                    <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-money-bill-wave mr-3"></i> Loans
                    </button>
                    <button @click="activeTab = 'savings'" :class="activeTab === 'savings' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-piggy-bank mr-3"></i> Savings
                    </button>
                    <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-exchange-alt mr-3"></i> Transactions
                    </button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-project-diagram mr-3"></i> Projects
                    </button>
                    <button @click="activeTab = 'meetings'" :class="activeTab === 'meetings' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-calendar mr-3"></i> Meetings
                    </button>
                    <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'" class="w-full flex items-center px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-file-alt mr-3"></i> Documents
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
            <!-- Header -->
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

            <!-- Content Area -->
            <main class="p-6 h-full overflow-y-auto">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" class="space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-blue-500 to-blue-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100">Total Members</p>
                                    <p class="text-3xl font-bold" x-text="stats.totalMembers">0</p>
                                </div>
                                <i class="fas fa-users text-4xl text-blue-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-green-500 to-green-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100">Total Savings</p>
                                    <p class="text-3xl font-bold" x-text="formatCurrency(stats.totalSavings)">0</p>
                                </div>
                                <i class="fas fa-piggy-bank text-4xl text-green-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100">Active Loans</p>
                                    <p class="text-3xl font-bold" x-text="stats.activeLoans">0</p>
                                </div>
                                <i class="fas fa-money-bill-wave text-4xl text-yellow-200"></i>
                            </div>
                        </div>
                        <div class="glass-card p-6 rounded-xl text-white shadow-lg bg-gradient-to-r from-purple-500 to-purple-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100">Total Projects</p>
                                    <p class="text-3xl font-bold" x-text="stats.totalProjects">0</p>
                                </div>
                                <i class="fas fa-project-diagram text-4xl text-purple-200"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="glass-card rounded-xl p-6 border border-white/20">
                            <h3 class="text-xl font-bold text-white mb-4">Monthly Savings Trend</h3>
                            <canvas id="savingsChart" width="400" height="200"></canvas>
                        </div>
                        <div class="glass-card rounded-xl p-6 border border-white/20">
                            <h3 class="text-xl font-bold text-white mb-4">Loan Status Distribution</h3>
                            <canvas id="loanChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Members Tab -->
                <div x-show="activeTab === 'members'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">Member Management</h3>
                        <button @click="showAddMemberModal = true" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
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
                                        <th class="px-6 py-3 text-left text-white font-semibold">Balance</th>
                                        <th class="px-6 py-3 text-left text-white font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="member in members" :key="member.id">
                                        <tr class="border-b border-white/10 hover:bg-white/5">
                                            <td class="px-6 py-4 text-white" x-text="member.member_id"></td>
                                            <td class="px-6 py-4 text-white" x-text="member.full_name"></td>
                                            <td class="px-6 py-4 text-white" x-text="member.email"></td>
                                            <td class="px-6 py-4 text-white" x-text="formatCurrency(member.balance)"></td>
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

                <!-- Loans Tab -->
                <div x-show="activeTab === 'loans'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Loan Management</h3>
                    <p>Loan management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'savings'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Savings Management</h3>
                    <p>Savings management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'transactions'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Transaction History</h3>
                    <p>Transaction management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'projects'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Project Management</h3>
                    <p>Project management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'meetings'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Meeting Schedule</h3>
                    <p>Meeting management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'documents'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Document Management</h3>
                    <p>Document management features coming soon...</p>
                </div>

                <div x-show="activeTab === 'analytics'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">Analytics & Reports</h3>
                    <p>Advanced analytics features coming soon...</p>
                </div>

                <div x-show="activeTab === 'settings'" class="text-white">
                    <h3 class="text-2xl font-bold mb-4">System Settings</h3>
                    <p>System configuration features coming soon...</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        function dashboardApp() {
            return {
                activeTab: 'dashboard',
                stats: {
                    totalMembers: 0,
                    totalSavings: 0,
                    activeLoans: 0,
                    totalProjects: 0
                },
                members: [],
                showAddMemberModal: false,

                init() {
                    this.loadDashboardData();
                    this.loadMembers();
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                },

                getTabTitle() {
                    const titles = {
                        dashboard: 'Dashboard Overview',
                        members: 'Member Management',
                        loans: 'Loan Management',
                        savings: 'Savings Management',
                        transactions: 'Transaction History',
                        projects: 'Project Management',
                        meetings: 'Meeting Schedule',
                        documents: 'Document Management',
                        analytics: 'Analytics & Reports',
                        settings: 'System Settings'
                    };
                    return titles[this.activeTab] || 'Dashboard';
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX'
                    }).format(amount || 0);
                },

                async loadDashboardData() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.stats = data;
                    } catch (error) {
                        console.error('Error loading dashboard data:', error);
                    }
                },

                async loadMembers() {
                    try {
                        const response = await fetch('/api/members');
                        this.members = await response.json();
                    } catch (error) {
                        console.error('Error loading members:', error);
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
                                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { labels: { color: 'white' } }
                                },
                                scales: {
                                    x: { ticks: { color: 'white' } },
                                    y: { ticks: { color: 'white' } }
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
                                labels: ['Active', 'Completed', 'Pending'],
                                datasets: [{
                                    data: [12, 8, 3],
                                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { labels: { color: 'white' } }
                                }
                            }
                        });
                    }
                }
            }
        }
    </script>
@endsection