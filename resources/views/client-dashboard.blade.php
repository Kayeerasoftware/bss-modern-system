<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/nav.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="clientDashboard()">
    @include('navs.client-topnav')
    @include('navs.client-sidenav')

    <!-- Main Content -->
    <div class="main-content ml-0 lg:ml-36 mt-12 transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-36'">
        <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Savings</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(personalData.savings)">UGX 500,000</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+12% from last month</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active Loans</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(personalData.activeLoan)">UGX 0</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-hand-holding-usd text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span>No active loans</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Monthly Deposits</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="formatCurrency(personalData.monthlyDeposits)">UGX 100,000</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-purple-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>On track</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Account Balance</p>
                        <p class="text-2xl font-bold text-orange-600" x-text="formatCurrency(personalData.balance)">UGX 500,000</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-wallet text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>Healthy balance</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Financial Health Score -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Financial Health</h3>
                    <div class="text-2xl" x-text="getHealthIcon()">💚</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2" :class="getHealthColor()" x-text="analytics.financial_health?.score || 85">85</div>
                    <div class="text-sm text-gray-600 mb-3" x-text="analytics.financial_health?.rating || 'Excellent'">Excellent</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" :style="`width: ${analytics.financial_health?.score || 85}%`"></div>
                    </div>
                </div>
            </div>

            <!-- Credit Score -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Credit Score</h3>
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2" x-text="analytics.credit_score || 720">720</div>
                    <div class="text-sm text-gray-600 mb-3">Good Credit</div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>300</span>
                        <span>850</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-blue-500 h-2 rounded-full" :style="`width: ${((analytics.credit_score || 720) - 300) / 550 * 100}%`"></div>
                    </div>
                </div>
            </div>

            <!-- Savings Growth Rate -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Growth Rate</h3>
                    <i class="fas fa-trending-up text-green-600 text-xl"></i>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2" x-text="(analytics.savings_growth_rate || 12.5).toFixed(1) + '%'">12.5%</div>
                    <div class="text-sm text-gray-600 mb-3">Monthly Growth</div>
                    <div class="flex items-center justify-center text-sm">
                        <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                        <span class="text-green-600">Above average</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Savings Growth Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Savings Growth</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>Last 6 months</option>
                        <option>Last year</option>
                    </select>
                </div>
                <div style="height: 250px;">
                    <canvas id="savingsChart"></canvas>
                </div>
            </div>

            <!-- Transaction Distribution -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Transaction Distribution</h3>
                    <span class="text-sm text-gray-500">This month</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>

            <!-- Spending Categories -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Spending Analysis</h3>
                    <span class="text-sm text-gray-500">This month</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="spendingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Action Cards & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button @click="makeDeposit()" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Make Deposit
                    </button>
                    <button class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                        <i class="fas fa-hand-holding-usd mr-2"></i>
                        Apply for Loan
                    </button>
                    <button class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 flex items-center justify-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Reports
                    </button>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
                    <button class="text-blue-600 text-sm hover:underline">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Date</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Type</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Amount</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="transaction in recentTransactions" :key="transaction.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 text-sm" x-text="transaction.date">2024-01-15</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full"
                                              :class="transaction.type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                              x-text="transaction.type">Deposit</span>
                                    </td>
                                    <td class="py-3 text-sm font-medium"
                                        :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'"
                                        x-text="formatCurrency(transaction.amount)">UGX 100,000</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Enhanced Savings Goal Progress -->
        <div class="mt-6 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Savings Goals & Predictions</h3>
                <button class="text-blue-600 text-sm hover:underline">Set New Goal</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Current Goals</h4>
                    <template x-for="goal in savingsGoals" :key="goal.name">
                        <div class="p-4 border rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="font-medium" x-text="goal.name">Emergency Fund</h5>
                                <span class="text-sm text-gray-500" x-text="goal.progress + '%'">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-500" :style="`width: ${goal.progress}%`"></div>
                            </div>
                            <p class="text-sm text-gray-600" x-text="`${formatCurrency(goal.current)} of ${formatCurrency(goal.target)}`">UGX 375,000 of UGX 500,000</p>
                            <p class="text-xs text-gray-500 mt-1" x-text="`Target: ${goal.deadline}`">Target: 2024-06-30</p>
                        </div>
                    </template>
                </div>

                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Savings Predictions</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm font-medium">3 Months</span>
                            <span class="text-sm font-bold text-blue-600" x-text="formatCurrency(analytics.predicted_savings?.['3_months'] || 650000)">UGX 650,000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium">6 Months</span>
                            <span class="text-sm font-bold text-green-600" x-text="formatCurrency(analytics.predicted_savings?.['6_months'] || 800000)">UGX 800,000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm font-medium">12 Months</span>
                            <span class="text-sm font-bold text-purple-600" x-text="formatCurrency(analytics.predicted_savings?.['12_months'] || 1100000)">UGX 1,100,000</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Monthly Comparison</h4>
                    <div class="space-y-3">
                        <div class="p-3 border rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">This Month</span>
                                <span class="font-medium" x-text="formatCurrency(monthlyComparison.this_month || 100000)">UGX 100,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Last Month</span>
                                <span class="font-medium" x-text="formatCurrency(monthlyComparison.last_month || 85000)">UGX 85,000</span>
                            </div>
                            <div class="mt-2 pt-2 border-t">
                                <div class="flex items-center" :class="monthlyComparison.change_percent >= 0 ? 'text-green-600' : 'text-red-600'">
                                    <i :class="monthlyComparison.change_percent >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="mr-1"></i>
                                    <span class="text-sm font-medium" x-text="Math.abs(monthlyComparison.change_percent || 17.6).toFixed(1) + '%'">17.6%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeClientCharts, 500);
        });

        function initializeClientCharts() {
            // Savings Growth Chart
            const savingsCtx = document.getElementById('savingsChart')?.getContext('2d');
            if (savingsCtx) {
                new Chart(savingsCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Savings',
                            data: [300000, 350000, 400000, 420000, 480000, 500000],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return 'UGX ' + (value/1000).toFixed(0) + 'K'; } }
                            }
                        }
                    }
                });
            }

            // Transaction Distribution Chart
            const transactionCtx = document.getElementById('transactionChart')?.getContext('2d');
            if (transactionCtx) {
                new Chart(transactionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Deposits', 'Withdrawals', 'Transfers'],
                        datasets: [{
                            data: [70, 20, 10],
                            backgroundColor: ['#10B981', '#EF4444', '#8B5CF6'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            // Spending Categories Chart
            const spendingCtx = document.getElementById('spendingChart')?.getContext('2d');
            if (spendingCtx) {
                new Chart(spendingCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Savings', 'Loans', 'Transfers', 'Fees'],
                        datasets: [{
                            label: 'Percentage',
                            data: [60, 20, 15, 5],
                            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { callback: function(value) { return value + '%'; } }
                            }
                        }
                    }
                });
            }
        }
        function clientDashboard() {
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
                activeLink: 'overview',
                sidebarSearch: '',
                profilePicture: null,
                userData: {
                    name: 'John Doe',
                    memberId: 'BSS001'
                },
                personalData: {
                    savings: 500000,
                    activeLoan: 0,
                    monthlyDeposits: 100000,
                    balance: 500000
                },
                analytics: {
                    savings_growth_rate: 12.5,
                    credit_score: 720,
                    financial_health: {
                        score: 85,
                        rating: 'Excellent',
                        factors: ['Strong savings balance', 'Low debt ratio', 'Regular deposits']
                    },
                    predicted_savings: {
                        '3_months': 650000,
                        '6_months': 800000,
                        '12_months': 1100000
                    }
                },
                savingsGoals: [
                    {
                        name: 'Emergency Fund',
                        target: 500000,
                        current: 375000,
                        progress: 75,
                        deadline: '2024-06-30'
                    },
                    {
                        name: 'Investment Fund',
                        target: 1000000,
                        current: 450000,
                        progress: 45,
                        deadline: '2024-12-31'
                    }
                ],
                monthlyComparison: {
                    this_month: 100000,
                    last_month: 85000,
                    change_percent: 17.6
                },
                spendingCategories: {
                    savings: 60,
                    loans: 20,
                    transfers: 15,
                    fees: 5
                },
                recentTransactions: [
                    {id: 1, date: '2024-01-15', type: 'deposit', amount: 100000},
                    {id: 2, date: '2024-01-10', type: 'deposit', amount: 50000},
                    {id: 3, date: '2024-01-05', type: 'withdrawal', amount: 25000}
                ],

                init() {
                    // Charts are initialized separately via DOM ready event
                },

                getHealthIcon() {
                    const score = this.analytics.financial_health?.score || 85;
                    if (score >= 80) return '💚';
                    if (score >= 60) return '💛';
                    return '❤️';
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount);
                },

                async makeDeposit() {
                    const amount = prompt('Enter deposit amount:');
                    if (amount && !isNaN(amount) && amount > 0) {
                        try {
                            const response = await fetch('/api/transactions', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify({
                                    member_id: 'BSS001',
                                    amount: parseFloat(amount),
                                    type: 'deposit',
                                    description: 'Manual deposit'
                                })
                            });

                            if (response.ok) {
                                this.loadClientData();
                                alert('Deposit successful!');
                            } else {
                                alert('Error processing deposit');
                            }
                        } catch (error) {
                            console.error('Error making deposit:', error);
                            alert('Error processing deposit. Please try again.');
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
