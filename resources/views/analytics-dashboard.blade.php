@extends('layouts.app')

@section('title', 'BSS Analytics Dashboard')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="analyticsApp()">
        <!-- Header -->
        <header class="gradient-bg text-white relative overflow-hidden pt-16">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="relative z-10 container mx-auto px-6 py-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">Analytics Dashboard</h1>
                        <p class="text-lg opacity-90">BSS Investment Group Intelligence</p>
                    </div>
                    <div class="flex space-x-4">
                        <button @click="refreshData()" class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition-all duration-300">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition-all duration-300">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>
    
        <div class="container mx-auto px-6 py-8">
            <!-- Overview Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-card rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Members</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" x-text="analytics.overview?.total_members || 0"></p>
                            <p class="text-sm text-green-600 dark:text-green-400">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span x-text="analytics.overview?.monthly_growth?.toFixed(1) || 0"></span>% growth
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>
    
                <div class="glass-card rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Savings</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX <span x-text="formatNumber(analytics.overview?.total_savings || 0)"></span></p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                <i class="fas fa-piggy-bank mr-1"></i>
                                Avg: UGX <span x-text="formatNumber(analytics.overview?.avg_savings_per_member || 0)"></span>
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-wallet text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                </div>
    
                <div class="glass-card rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Active Loans</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX <span x-text="formatNumber(analytics.overview?.total_loans || 0)"></span></p>
                            <p class="text-sm text-orange-600 dark:text-orange-400">
                                <i class="fas fa-percentage mr-1"></i>
                                Default: <span x-text="analytics.overview?.loan_default_rate?.toFixed(1) || 0"></span>%
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-hand-holding-usd text-2xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                </div>
    
                <div class="glass-card rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Available Funds</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX <span x-text="formatNumber(analytics.overview?.available_funds || 0)"></span></p>
                            <p class="text-sm text-purple-600 dark:text-purple-400">
                                <i class="fas fa-chart-line mr-1"></i>
                                <span x-text="analytics.overview?.active_projects || 0"></span> Projects
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-coins text-2xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Savings Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-blue-500 mr-3"></i>Savings Distribution
                    </h3>
                    <div class="chart-container">
                        <canvas id="savingsDistributionChart"></canvas>
                    </div>
                </div>
    
                <!-- Loan Status -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-donut text-green-500 mr-3"></i>Loan Status
                    </h3>
                    <div class="chart-container">
                        <canvas id="loanStatusChart"></canvas>
                    </div>
                </div>
    
                <!-- Monthly Transactions -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-line text-purple-500 mr-3"></i>Monthly Transactions
                    </h3>
                    <div class="chart-container">
                        <canvas id="monthlyTransactionsChart"></canvas>
                    </div>
                </div>
    
                <!-- Member Growth -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-area text-orange-500 mr-3"></i>Member Growth
                    </h3>
                    <div class="chart-container">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                </div>
    
                <!-- Project Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-tasks text-red-500 mr-3"></i>Project Progress
                    </h3>
                    <div class="chart-container">
                        <canvas id="projectProgressChart"></canvas>
                    </div>
                </div>
    
                <!-- Transaction Types -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-exchange-alt text-indigo-500 mr-3"></i>Transaction Types
                    </h3>
                    <div class="chart-container">
                        <canvas id="transactionTypesChart"></canvas>
                    </div>
                </div>
            </div>
    
            <!-- Advanced Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Age Demographics -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-users text-pink-500 mr-3"></i>Age Demographics
                    </h3>
                    <div class="chart-container">
                        <canvas id="ageDemographicsChart"></canvas>
                    </div>
                </div>
    
                <!-- Location Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt text-teal-500 mr-3"></i>Location Distribution
                    </h3>
                    <div class="chart-container">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
    
                <!-- Occupation Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-briefcase text-cyan-500 mr-3"></i>Occupation Breakdown
                    </h3>
                    <div class="chart-container">
                        <canvas id="occupationChart"></canvas>
                    </div>
                </div>
            </div>
    
            <!-- Performance Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- ROI by Project -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-yellow-500 mr-3"></i>ROI by Project
                    </h3>
                    <div class="chart-container">
                        <canvas id="roiChart"></canvas>
                    </div>
                </div>
    
                <!-- Member Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-trophy text-amber-500 mr-3"></i>Top Performers
                    </h3>
                    <div class="space-y-3 max-h-72 overflow-y-auto">
                        <template x-for="(member, index) in analytics.performance?.member_performance?.slice(0, 10) || []" :key="member.member_id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm" x-text="index + 1"></div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="member.full_name"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="member.member_id"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600 dark:text-green-400">UGX <span x-text="formatNumber(member.net_worth)"></span></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><span x-text="member.transaction_count"></span> transactions</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
    
            <!-- Predictions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-crystal-ball text-violet-500 mr-3"></i>Predictions & Forecasts
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-xl">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Projected Savings</h4>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">UGX <span x-text="formatNumber(analytics.predictions?.projected_savings?.next_year || 0)"></span></p>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Next Year</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl">
                        <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">Loan Demand</h4>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100" x-text="analytics.predictions?.loan_demand_forecast?.next_year || 0"></p>
                        <p class="text-sm text-green-700 dark:text-green-300">Applications/Year</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-xl">
                        <h4 class="font-semibold text-purple-800 dark:text-purple-200 mb-2">Member Growth</h4>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100" x-text="analytics.predictions?.member_growth_projection?.next_year || 0"></p>
                        <p class="text-sm text-purple-700 dark:text-purple-300">Members by Next Year</p>
                    </div>
                </div>
            </div>
        </div>
    
        <script>
            function analyticsApp() {
                return {
                    analytics: {},
                    charts: {},
    
                    async init() {
                        await this.loadAnalytics();
                        this.initializeCharts();
                    },
    
                    async loadAnalytics() {
                        try {
                            // Corrected API endpoint for analytics
                            const response = await fetch('/api/analytics/dashboard');
                            this.analytics = await response.json();
                        } catch (error) {
                            console.error('Error loading analytics:', error);
                        }
                    },
    
                    async refreshData() {
                        await this.loadAnalytics();
                        this.updateCharts();
                    },
    
                    formatNumber(num) {
                        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
                        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
                        return num?.toLocaleString() || '0';
                    },
    
                    initializeCharts() {
                        this.createChart('savingsDistributionChart', 'doughnut', this.analytics.charts?.savings_distribution, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'], 'label', 'value', { legend: 'bottom' });
                        this.createChart('loanStatusChart', 'pie', this.analytics.charts?.loan_status_pie, ['#10b981', '#f59e0b', '#ef4444'], 'label', 'value', { legend: 'bottom' });
                        this.createChart('monthlyTransactionsChart', 'line', this.analytics.charts?.monthly_transactions, '#8b5cf6', 'month', 'count', { label: 'Transaction Count', fill: true, tension: 0.4, legend: false, yBeginAtZero: true });
                        this.createChart('memberGrowthChart', 'bar', this.analytics.charts?.member_growth, '#f97316', 'month', 'new_members', { label: 'New Members', borderRadius: 8, legend: false, yBeginAtZero: true });
                        this.createChart('projectProgressChart', 'horizontalBar', this.analytics.charts?.project_progress, '#ef4444', 'name', 'progress', { label: 'Progress %', borderRadius: 4, indexAxis: 'y', legend: false, xBeginAtZero: true, xMax: 100 });
                        this.createChart('transactionTypesChart', 'polarArea', this.analytics.charts?.transaction_types, ['#6366f1', '#8b5cf6', '#a855f7', '#c084fc'], 'type', 'count', { legend: 'bottom' });
                        this.createChart('ageDemographicsChart', 'radar', this.analytics.charts?.age_demographics, '#ec4899', 'label', 'value', { label: 'Members', backgroundColor: 'rgba(236, 72, 153, 0.2)', borderColor: '#ec4899', pointBackgroundColor: '#ec4899', legend: false });
                        this.createChart('locationChart', 'bar', this.analytics.charts?.location_distribution, '#14b8a6', 'location', 'count', { label: 'Members', borderRadius: 6, legend: false, yBeginAtZero: true });
                        this.createChart('occupationChart', 'doughnut', this.analytics.charts?.occupation_breakdown, ['#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1', '#8b5cf6'], 'occupation', 'count', { legend: 'bottom' });
                        this.createChart('roiChart', 'scatter', this.analytics.performance?.roi_by_project, '#eab308', 'budget', 'roi', { label: 'Projects', borderColor: '#ca8a04', xTitle: 'Budget (UGX)', yTitle: 'ROI (%)', xBeginAtZero: true, yBeginAtZero: true });
                    },
    
                    createChart(elementId, type, data, colors, labelKey, valueKey, options = {}) {
                        const ctx = document.getElementById(elementId)?.getContext('2d');
                        if (!ctx) return;
    
                        const chartData = {
                            labels: data?.map(item => item[labelKey]) || [],
                            datasets: [{
                                label: options.label || '',
                                data: data?.map(item => item[valueKey]) || [],
                                backgroundColor: Array.isArray(colors) ? colors : colors || '',
                                borderColor: options.borderColor || colors || '',
                                borderWidth: options.borderWidth !== undefined ? options.borderWidth : 0,
                                fill: options.fill !== undefined ? options.fill : false,
                                tension: options.tension !== undefined ? options.tension : 0,
                                borderRadius: options.borderRadius !== undefined ? options.borderRadius : 0,
                                pointBackgroundColor: options.pointBackgroundColor || colors || '',
                            }]
                        };
    
                        const chartOptions = {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: options.legend !== undefined ? options.legend : true,
                                    position: options.legendPosition || 'top'
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: options.xBeginAtZero !== undefined ? options.xBeginAtZero : false,
                                    title: {
                                        display: options.xTitle !== undefined,
                                        text: options.xTitle || '',
                                    },
                                    max: options.xMax || undefined,
                                },
                                y: {
                                    beginAtZero: options.yBeginAtZero !== undefined ? options.yBeginAtZero : false,
                                    title: {
                                        display: options.yTitle !== undefined,
                                        text: options.yTitle || '',
                                    }
                                }
                            },
                            indexAxis: options.indexAxis || 'x', // For horizontal bar charts
                        };
    
                        // Special handling for scatter chart
                        if (type === 'scatter') {
                            chartData.datasets[0].data = data?.map(item => ({ x: item[labelKey], y: item[valueKey] })) || [];
                            // For scatter, labelKey and valueKey are used for x and y in data mapping
                            // The actual labels for the legend will be handled by options.label
                            chartOptions.scales.x.title.text = options.xTitle || '';
                            chartOptions.scales.y.title.text = options.yTitle || '';
                        }
    
                        this.charts[elementId] = new Chart(ctx, { type: type, data: chartData, options: chartOptions });
                    },
    
                    updateCharts() {
                        Object.values(this.charts).forEach(chart => {
                            if (chart) chart.destroy();
                        });
                        this.initializeCharts();
                    }
                }
            }
        </script>
    </div>
@endsection