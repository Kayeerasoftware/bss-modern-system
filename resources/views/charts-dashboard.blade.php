<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSS Investment Group - Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="chartsSystem()">
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-chart-line text-2xl"></i>
                    <h1 class="text-xl font-bold">BSS Analytics Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="refreshCharts()" class="bg-blue-700 px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <a href="/complete" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Savings</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(analytics.totalSavings)"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-hand-holding-usd text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Active Loans</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(analytics.totalLoans)"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="analytics.totalMembers"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-project-diagram text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Active Projects</p>
                        <p class="text-2xl font-bold text-orange-600" x-text="analytics.activeProjects"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Savings vs Loans Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Savings vs Loans Overview</h3>
                <canvas id="savingsLoansChart" width="400" height="200"></canvas>
            </div>

            <!-- Member Distribution by Role -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Member Distribution by Role</h3>
                <canvas id="memberRoleChart" width="400" height="200"></canvas>
            </div>

            <!-- Monthly Transaction Trends -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Monthly Transaction Trends</h3>
                <canvas id="transactionTrendsChart" width="400" height="200"></canvas>
            </div>

            <!-- Loan Status Distribution -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Loan Status Distribution</h3>
                <canvas id="loanStatusChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Full Width Charts -->
        <div class="grid grid-cols-1 gap-6 mb-8">
            <!-- Project Progress Overview -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Project Progress Overview</h3>
                <canvas id="projectProgressChart" width="800" height="300"></canvas>
            </div>

            <!-- Financial Performance Over Time -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Financial Performance Over Time</h3>
                <canvas id="financialPerformanceChart" width="800" height="300"></canvas>
            </div>
        </div>

        <!-- Additional Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Savers -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Top Savers</h3>
                <div class="space-y-3">
                    <template x-for="(member, index) in analytics.topSavers" :key="member.member_id">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div class="flex items-center">
                                <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm mr-3" x-text="index + 1"></span>
                                <div>
                                    <p class="font-medium" x-text="member.full_name"></p>
                                    <p class="text-sm text-gray-600" x-text="member.member_id"></p>
                                </div>
                            </div>
                            <p class="font-bold text-green-600" x-text="formatCurrency(member.savings)"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Loan Repayment Status -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Loan Repayment Status</h3>
                <canvas id="repaymentStatusChart" width="300" height="200"></canvas>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <template x-for="activity in analytics.recentActivity" :key="activity.id">
                        <div class="flex items-center p-3 bg-gray-50 rounded">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3" :class="getActivityColor(activity.type)">
                                <i :class="getActivityIcon(activity.type)" class="text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium" x-text="activity.description"></p>
                                <p class="text-xs text-gray-500" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function chartsSystem() {
            return {
                analytics: {
                    totalSavings: 0,
                    totalLoans: 0,
                    totalMembers: 0,
                    activeProjects: 0,
                    topSavers: [],
                    recentActivity: []
                },
                charts: {},

                init() {
                    this.loadAnalyticsData();
                },

                async loadAnalyticsData() {
                    try {
                        // Load real data from API
                        const response = await fetch('/api/analytics/dashboard');
                        if (response.ok) {
                            const data = await response.json();
                            this.analytics = {
                                totalSavings: data.totalSavings || 0,
                                totalLoans: data.totalLoans || 0,
                                totalMembers: data.totalMembers || 0,
                                activeProjects: data.activeProjects || 0,
                                topSavers: data.topSavers || [],
                                recentActivity: data.recentActivity || [],
                                membersByRole: data.membersByRole || [],
                                loansByStatus: data.loansByStatus || [],
                                monthlyTransactions: data.monthlyTransactions || [],
                                projects: data.projects || [],
                                quarterlyPerformance: data.quarterlyPerformance || [],
                                repaymentAnalysis: data.repaymentAnalysis || {}
                            };
                        } else {
                            throw new Error('API not available');
                        }
                    } catch (error) {
                        console.log('Using fallback data');
                        // Fallback data for demonstration
                        this.analytics = {
                            totalSavings: 8450000,
                            totalLoans: 2000000,
                            totalMembers: 8,
                            activeProjects: 3,
                            topSavers: [
                                {member_id: 'BSS005', full_name: 'David Brown', savings: 3000000},
                                {member_id: 'BSS004', full_name: 'Mary Wilson', savings: 2000000},
                                {member_id: 'BSS008', full_name: 'Lisa Anderson', savings: 1500000},
                                {member_id: 'BSS003', full_name: 'Robert Johnson', savings: 1200000},
                                {member_id: 'BSS006', full_name: 'Sarah Connor', savings: 800000}
                            ],
                            recentActivity: [
                                {id: 1, type: 'deposit', description: 'John Doe made a deposit of UGX 100,000', time: '2 hours ago'},
                                {id: 2, type: 'loan', description: 'New loan application from Jane Smith', time: '4 hours ago'},
                                {id: 3, type: 'project', description: 'Water Project progress updated to 65%', time: '6 hours ago'},
                                {id: 4, type: 'member', description: 'New member Sarah Connor registered', time: '1 day ago'}
                            ],
                            membersByRole: [
                                {role: 'client', count: 3},
                                {role: 'shareholder', count: 2},
                                {role: 'cashier', count: 1},
                                {role: 'td', count: 1},
                                {role: 'ceo', count: 1}
                            ],
                            loansByStatus: [
                                {status: 'active', count: 3},
                                {status: 'pending', count: 1},
                                {status: 'completed', count: 5},
                                {status: 'defaulted', count: 0}
                            ],
                            monthlyTransactions: [
                                {month: 'Jan 2024', deposits: 1200000, withdrawals: 800000},
                                {month: 'Feb 2024', deposits: 1500000, withdrawals: 900000},
                                {month: 'Mar 2024', deposits: 1800000, withdrawals: 700000},
                                {month: 'Apr 2024', deposits: 1600000, withdrawals: 1100000},
                                {month: 'May 2024', deposits: 2000000, withdrawals: 950000},
                                {month: 'Jun 2024', deposits: 2200000, withdrawals: 1200000}
                            ],
                            projects: [
                                {name: 'Community Water Project', progress: 65, budget: 5000000},
                                {name: 'Education Support Program', progress: 100, budget: 3000000},
                                {name: 'Healthcare Initiative', progress: 15, budget: 8000000},
                                {name: 'Agricultural Development', progress: 40, budget: 4500000},
                                {name: 'Youth Skills Training', progress: 55, budget: 2500000}
                            ],
                            quarterlyPerformance: [
                                {period: 'Q1 2023', total_assets: 5000000, member_savings: 3500000, loan_portfolio: 1200000},
                                {period: 'Q2 2023', total_assets: 5800000, member_savings: 4200000, loan_portfolio: 1400000},
                                {period: 'Q3 2023', total_assets: 6500000, member_savings: 4800000, loan_portfolio: 1600000},
                                {period: 'Q4 2023', total_assets: 7200000, member_savings: 5500000, loan_portfolio: 1800000},
                                {period: 'Q1 2024', total_assets: 8000000, member_savings: 6200000, loan_portfolio: 1900000},
                                {period: 'Q2 2024', total_assets: 8450000, member_savings: 6800000, loan_portfolio: 2000000}
                            ],
                            repaymentAnalysis: {
                                on_time: 75,
                                late: 20,
                                defaulted: 5
                            }
                        };
                    }
                    
                    this.$nextTick(() => {
                        this.initializeCharts();
                    });
                },

                initializeCharts() {
                    this.createSavingsLoansChart();
                    this.createMemberRoleChart();
                    this.createTransactionTrendsChart();
                    this.createLoanStatusChart();
                    this.createProjectProgressChart();
                    this.createFinancialPerformanceChart();
                    this.createRepaymentStatusChart();
                },

                createSavingsLoansChart() {
                    const ctx = document.getElementById('savingsLoansChart').getContext('2d');
                    this.charts.savingsLoans = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Total Savings', 'Active Loans', 'Available Funds'],
                            datasets: [{
                                data: [
                                    this.analytics.totalSavings,
                                    this.analytics.totalLoans,
                                    this.analytics.totalSavings - this.analytics.totalLoans
                                ],
                                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },

                createMemberRoleChart() {
                    const ctx = document.getElementById('memberRoleChart').getContext('2d');
                    const roleData = this.analytics.membersByRole || [];
                    const labels = roleData.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1));
                    const data = roleData.map(item => item.count);
                    
                    this.charts.memberRole = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.length > 0 ? labels : ['Clients', 'Shareholders', 'Cashiers', 'TDs', 'CEOs'],
                            datasets: [{
                                data: data.length > 0 ? data : [3, 2, 1, 1, 1],
                                backgroundColor: ['#8B5CF6', '#EC4899', '#F59E0B', '#EF4444', '#06B6D4'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },

                createTransactionTrendsChart() {
                    const ctx = document.getElementById('transactionTrendsChart').getContext('2d');
                    const transactionData = this.analytics.monthlyTransactions || [];
                    const labels = transactionData.map(item => item.month);
                    const deposits = transactionData.map(item => item.deposits);
                    const withdrawals = transactionData.map(item => item.withdrawals);
                    
                    this.charts.transactionTrends = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels.length > 0 ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Deposits',
                                data: deposits.length > 0 ? deposits : [1200000, 1500000, 1800000, 1600000, 2000000, 2200000],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Withdrawals',
                                data: withdrawals.length > 0 ? withdrawals : [800000, 900000, 700000, 1100000, 950000, 1200000],
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'UGX ' + (value / 1000000).toFixed(1) + 'M';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                },

                createLoanStatusChart() {
                    const ctx = document.getElementById('loanStatusChart').getContext('2d');
                    const loanData = this.analytics.loansByStatus || [];
                    const labels = loanData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
                    const data = loanData.map(item => item.count);
                    
                    this.charts.loanStatus = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.length > 0 ? labels : ['Active', 'Pending', 'Completed', 'Defaulted'],
                            datasets: [{
                                label: 'Number of Loans',
                                data: data.length > 0 ? data : [3, 1, 5, 0],
                                backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                },

                createProjectProgressChart() {
                    const ctx = document.getElementById('projectProgressChart').getContext('2d');
                    const projectData = this.analytics.projects || [];
                    const labels = projectData.map(item => item.name);
                    const progress = projectData.map(item => item.progress);
                    
                    this.charts.projectProgress = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.length > 0 ? labels : ['Community Water Project', 'Education Support Program', 'Healthcare Initiative', 'Agricultural Development', 'Youth Skills Training'],
                            datasets: [{
                                label: 'Progress (%)',
                                data: progress.length > 0 ? progress : [65, 100, 15, 40, 55],
                                backgroundColor: function(context) {
                                    const value = context.parsed.y;
                                    if (value >= 80) return '#10B981';
                                    if (value >= 50) return '#F59E0B';
                                    return '#EF4444';
                                },
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                },

                createFinancialPerformanceChart() {
                    const ctx = document.getElementById('financialPerformanceChart').getContext('2d');
                    const performanceData = this.analytics.quarterlyPerformance || [];
                    const labels = performanceData.map(item => item.period);
                    const totalAssets = performanceData.map(item => item.total_assets);
                    const memberSavings = performanceData.map(item => item.member_savings);
                    const loanPortfolio = performanceData.map(item => item.loan_portfolio);
                    
                    this.charts.financialPerformance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels.length > 0 ? labels : ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024', 'Q2 2024'],
                            datasets: [{
                                label: 'Total Assets',
                                data: totalAssets.length > 0 ? totalAssets : [5000000, 5800000, 6500000, 7200000, 8000000, 8450000],
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Member Savings',
                                data: memberSavings.length > 0 ? memberSavings : [3500000, 4200000, 4800000, 5500000, 6200000, 6800000],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Loan Portfolio',
                                data: loanPortfolio.length > 0 ? loanPortfolio : [1200000, 1400000, 1600000, 1800000, 1900000, 2000000],
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'UGX ' + (value / 1000000).toFixed(1) + 'M';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                },

                createRepaymentStatusChart() {
                    const ctx = document.getElementById('repaymentStatusChart').getContext('2d');
                    const repaymentData = this.analytics.repaymentAnalysis || {};
                    
                    this.charts.repaymentStatus = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['On Time', 'Late', 'Defaulted'],
                            datasets: [{
                                data: [
                                    repaymentData.on_time || 75,
                                    repaymentData.late || 20,
                                    repaymentData.defaulted || 5
                                ],
                                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },

                refreshCharts() {
                    // Destroy existing charts
                    Object.values(this.charts).forEach(chart => {
                        if (chart) chart.destroy();
                    });
                    this.charts = {};
                    
                    // Reload data and recreate charts
                    this.loadAnalyticsData();
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount || 0);
                },

                getActivityColor(type) {
                    const colors = {
                        deposit: 'bg-green-100 text-green-600',
                        loan: 'bg-blue-100 text-blue-600',
                        project: 'bg-purple-100 text-purple-600',
                        member: 'bg-orange-100 text-orange-600'
                    };
                    return colors[type] || 'bg-gray-100 text-gray-600';
                },

                getActivityIcon(type) {
                    const icons = {
                        deposit: 'fas fa-arrow-up',
                        loan: 'fas fa-hand-holding-usd',
                        project: 'fas fa-project-diagram',
                        member: 'fas fa-user-plus'
                    };
                    return icons[type] || 'fas fa-info';
                }
            }
        }
    </script>
</body>
</html>