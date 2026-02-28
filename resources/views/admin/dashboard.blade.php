@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-3 md:p-4">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl overflow-hidden bg-white shadow-lg flex-shrink-0">
                    <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0">
                <h1 class="text-sm sm:text-lg md:text-2xl font-bold text-white truncate">Welcome, <span class="text-black text-base sm:text-xl md:text-3xl">{{ auth()->user()->name }}</span> <span class="text-blue-200 font-normal text-xs sm:text-sm md:text-lg">({{ ucfirst(auth()->user()->role) }})</span> 👋</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-0.5 md:mt-1">Please monitor the organization overall progress...</p>
                </div>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 sm:px-3 sm:py-1.5 md:px-4 md:py-2 text-right flex-shrink-0">
                <p class="text-white text-xs sm:text-sm font-semibold whitespace-nowrap">{{ now()->format('l, F d, Y') }}</p>
                <p class="text-blue-100 text-xs mt-0.5 whitespace-nowrap">{{ now()->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Dashboard data...</span>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3">
        <!-- Total Members -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-blue-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-users text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Members</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['totalMembers']) }} <span class="text-xs text-green-600 font-medium">+{{ $stats['newMembersThisMonth'] }}</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Assets -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-green-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-wallet text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Assets</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['totalAssets']/1000000, 1) }}M <span class="text-xs text-gray-500 font-medium">UGX</span></h3>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-purple-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-money-bill-wave text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Active Loans</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['approvedLoans']) }} <span class="text-xs text-yellow-600 font-medium">{{ $stats['pendingApplications'] }} pending</span></h3>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-orange-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-exchange-alt text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Transactions</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['todayTransactions']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Projects -->
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-indigo-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-project-diagram text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Active Projects</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['activeProjects'] }} <span class="text-xs text-gray-500 font-medium">/{{ $stats['totalProjects'] }}</span></h3>
                </div>
            </div>
        </div>

        <!-- Fundraising -->
        <div class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-pink-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-hand-holding-heart text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Campaigns</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['activeFundraisings'] }} <span class="text-xs text-green-600 font-medium">{{ number_format(($stats['totalFundraisingRaised'] / max($stats['totalFundraisingTarget'], 1)) * 100, 1) }}%</span></h3>
                </div>
            </div>
        </div>

        <!-- System Users -->
        <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-teal-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-user-shield text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">System Users</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['activeUsers'] }} <span class="text-xs text-gray-500 font-medium">/{{ $stats['totalUsers'] }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section with Tabs -->
    <div class="bg-white rounded-xl shadow-xl p-6">
        <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center justify-between gap-2 mb-6 border-b pb-4">
            <div class="flex flex-wrap gap-2">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-chart-line mr-2"></i>Overview
                </button>
                <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-money-bill-wave mr-2"></i>Loans
                </button>
                <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-users mr-2"></i>Members
                </button>
                <button @click="activeTab = 'financial'" :class="activeTab === 'financial' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-chart-pie mr-2"></i>Financial
                </button>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <span id="analyticsLabel" class="text-xs sm:text-sm font-semibold text-gray-700 whitespace-nowrap">Analytics for <span id="yearText">{{ now()->year }}</span></span>
                <select id="yearFilter" class="px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg font-semibold text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm">
                    @for($year = 2023; $year <= 2033; $year++)
                        <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                    <option value="all">All Years</option>
                </select>
            </div>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Growth Trends</h3>
                <div id="growthEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="growthYear"></span>'</p>
                    </div>
                </div>
                <canvas id="growthChart"></canvas>
            </div>
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Distribution</h3>
                <div id="distributionEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="distributionYear"></span>'</p>
                    </div>
                </div>
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- Loans Tab -->
        <div x-show="activeTab === 'loans'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Loan Status</h3>
                <div id="loanStatusEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-hand-holding-usd text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="loanStatusYear"></span>'</p>
                    </div>
                </div>
                <canvas id="loanStatusChart"></canvas>
            </div>
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Loan Amounts</h3>
                <div id="loanAmountEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="loanAmountYear"></span>'</p>
                    </div>
                </div>
                <canvas id="loanAmountChart"></canvas>
            </div>
        </div>

        <!-- Members Tab -->
        <div x-show="activeTab === 'members'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Member Growth</h3>
                <div id="memberGrowthEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="memberGrowthYear"></span>'</p>
                    </div>
                </div>
                <canvas id="memberGrowthChart"></canvas>
            </div>
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Member Activity</h3>
                <div id="memberActivityEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-radar text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="memberActivityYear"></span>'</p>
                    </div>
                </div>
                <canvas id="memberActivityChart"></canvas>
            </div>
        </div>

        <!-- Financial Tab -->
        <div x-show="activeTab === 'financial'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Revenue vs Expenses</h3>
                <div id="revenueEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-dollar-sign text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="revenueYear"></span>'</p>
                    </div>
                </div>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="h-80 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Asset Breakdown</h3>
                <div id="assetEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-wallet text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="assetYear"></span>'</p>
                    </div>
                </div>
                <canvas id="assetChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Members -->
        <div class="bg-white rounded-xl shadow-xl p-6 hover:shadow-2xl transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i>Recent Members
                </h3>
                <a href="{{ route('admin.members.index') }}" class="text-blue-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentMembers as $member)
                <a href="{{ route('admin.members.show', $member->id) }}" class="flex items-center space-x-3 p-3 hover:bg-blue-50 rounded-lg transition cursor-pointer">
                    @if($member->profile_picture_url)
                        <img src="{{ $member->profile_picture_url }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2" alt="{{ $member->full_name }}">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg ring-2 ring-blue-500 ring-offset-2">
                            <span class="text-white font-bold text-lg">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $member->member_id }} • {{ $member->created_at->diffForHumans() }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                @empty
                <p class="text-gray-500 text-sm text-center py-8">No recent members</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Loans -->
        <div class="bg-white rounded-xl shadow-xl p-6 hover:shadow-2xl transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-money-bill-wave text-purple-600 mr-2"></i>Recent Loans
                </h3>
                <a href="{{ route('admin.loans.index') }}" class="text-blue-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentLoans as $loan)
                <div class="flex items-center justify-between p-3 hover:bg-purple-50 rounded-lg transition cursor-pointer">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $loan->member->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">UGX {{ number_format($loan->amount, 0) }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $loan->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($loan->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-8">No recent loans</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-xl p-6 hover:shadow-2xl transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-exchange-alt text-green-600 mr-2"></i>Recent Transactions
                </h3>
                <a href="{{ route('admin.financial.transactions') }}" class="text-blue-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between p-3 hover:bg-green-50 rounded-lg transition cursor-pointer">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $transaction->type ?? 'Transaction' }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, h:i A') }}</p>
                    </div>
                    <p class="text-sm font-bold text-green-600">+{{ number_format($transaction->amount ?? 0, 0) }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-8">No recent transactions</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function closePanel() {}

function closePanelLoans() {}

function closePanelMembers() {}

function closePanelFinancial() {}

let monthlyData = @json($monthlyData);
let stats = @json($stats);
let allCharts = {};

window.monthlyData = monthlyData;
window.stats = stats;
window.allCharts = allCharts;

const commonOptions = {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2,
    interaction: { mode: 'index', intersect: true },
    plugins: {
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.9)',
            padding: 16,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            cornerRadius: 8,
            displayColors: true,
            callbacks: {
                title: (items) => items[0].label,
                label: (context) => {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    label += context.parsed.y !== undefined ? Math.round(context.parsed.y * 100) / 100 : context.parsed;
                    return label;
                }
            }
        },
        legend: {
            position: 'bottom',
            labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true },
            onClick: (e, legendItem, legend) => {
                const index = legendItem.datasetIndex;
                const chart = legend.chart;
                const meta = chart.getDatasetMeta(index);
                meta.hidden = !meta.hidden;
                chart.update();
            }
        }
    },
    animation: { duration: 1000, easing: 'easeInOutQuart' },
    onHover: (event, activeElements) => {
        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
    }
};

// Growth Chart with Predictions
allCharts.growth = new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: [...monthlyData.months, 'Predicted'],
        datasets: [{
            label: 'Members',
            data: [...monthlyData.members, monthlyData.predictions.members],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.members.length ? [5, 5] : undefined }
        }, {
            label: 'Loans',
            data: [...monthlyData.loans, monthlyData.predictions.loans],
            borderColor: '#a855f7',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.loans.length ? [5, 5] : undefined }
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { font: { size: 11 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// Distribution Chart
allCharts.distribution = new Chart(document.getElementById('distributionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Members', 'Loans', 'Projects', 'Fundraising'],
        datasets: [{
            data: [stats.totalMembers, stats.totalLoans, stats.totalProjects, stats.activeFundraisings],
            backgroundColor: ['#3b82f6', '#a855f7', '#6366f1', '#ec4899'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 20,
            hoverBorderWidth: 4
        }]
    },
    options: {
        ...commonOptions,
        cutout: '70%',
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            },
            legend: {
                ...commonOptions.plugins.legend,
                onClick: null
            }
        }
    }
});

// Loan Status Chart
allCharts.loanStatus = new Chart(document.getElementById('loanStatusChart'), {
    type: 'pie',
    data: {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
            data: [stats.approvedLoans, stats.pendingLoans, stats.totalLoans - stats.approvedLoans - stats.pendingLoans],
            backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 20
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                callbacks: {
                    afterLabel: function(context) {
                        return 'Avg Repayment: ' + monthlyData.analytics.avgRepaymentRate + '%';
                    }
                }
            }
        }
    }
});

// Loan Amount Chart
allCharts.loanAmount = new Chart(document.getElementById('loanAmountChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Loan Amount (UGX M)',
            data: monthlyData.loanAmounts,
            backgroundColor: monthlyData.loanAmounts.map((v, i) =>
                i === monthlyData.loanAmounts.length - 1 ? '#a855f7' : 'rgba(168, 85, 247, 0.7)'
            ),
            borderColor: '#a855f7',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (context) => 'Amount: UGX ' + context.parsed.y.toFixed(2) + 'M'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    callback: (value) => value + 'M',
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// Member Growth Chart
allCharts.memberGrowth = new Chart(document.getElementById('memberGrowthChart'), {
    type: 'line',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'New Members',
            data: monthlyData.members,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            yAxisID: 'y'
        }, {
            label: 'Retention Rate (%)',
            data: monthlyData.memberRetention,
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { font: { size: 11 } }
            },
            y1: {
                type: 'linear',
                position: 'right',
                beginAtZero: true,
                max: 100,
                grid: { display: false },
                ticks: {
                    callback: (value) => value + '%',
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// Member Activity Chart
allCharts.memberActivity = new Chart(document.getElementById('memberActivityChart'), {
    type: 'radar',
    data: {
        labels: ['Loans', 'Savings (M)', 'Transactions', 'Projects', 'Fundraising'],
        datasets: [{
            label: 'Current',
            data: [stats.totalLoans, stats.totalSavings/1000000, stats.totalTransactions, stats.totalProjects, stats.activeFundraisings],
            backgroundColor: 'rgba(59, 130, 246, 0.3)',
            borderColor: '#3b82f6',
            borderWidth: 3,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6
        }, {
            label: 'Target',
            data: [stats.totalLoans * 1.2, (stats.totalSavings/1000000) * 1.3, stats.totalTransactions * 1.15, stats.totalProjects * 1.1, stats.activeFundraisings * 1.25],
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
            borderColor: '#22c55e',
            borderWidth: 2,
            borderDash: [5, 5],
            pointBackgroundColor: '#22c55e',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        }]
    },
    options: {
        ...commonOptions,
        scales: { r: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.1)' }, angleLines: { color: 'rgba(0, 0, 0, 0.1)' }, ticks: { backdropColor: 'transparent' } } }
    }
});

// Revenue Chart
allCharts.revenue = new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Revenue (UGX M)',
            data: monthlyData.revenue,
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: '#22c55e',
            borderWidth: 2,
            borderRadius: 8
        }, {
            label: 'Expenses (UGX M)',
            data: monthlyData.expenses,
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: '#ef4444',
            borderWidth: 2,
            borderRadius: 8
        }, {
            label: 'Profit (UGX M)',
            data: monthlyData.revenue.map((r, i) => r - monthlyData.expenses[i]),
            type: 'line',
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                callbacks: {
                    footer: function(items) {
                        return 'Profit Margin: ' + monthlyData.analytics.profitMargin + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    callback: (value) => value + 'M',
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// Asset Chart
allCharts.asset = new Chart(document.getElementById('assetChart'), {
    type: 'polarArea',
    data: {
        labels: ['Savings', 'Loans', 'Investments', 'Cash'],
        datasets: [{
            data: [stats.totalSavings/1000000, stats.totalLoanAmount/1000000, stats.totalAssets/2000000, stats.totalAssets/3000000],
            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(245, 158, 11, 0.8)'],
            borderColor: ['#3b82f6', '#a855f7', '#22c55e', '#f59e0b'],
            borderWidth: 2
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': UGX ' + context.parsed.r.toFixed(2) + 'M';
                    },
                    afterLabel: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed.r / total) * 100).toFixed(1);
                        return 'Share: ' + percentage + '%';
                    }
                }
            }
        },
        scales: { r: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.1)' }, ticks: { backdropColor: 'transparent' } } }
    }
});

console.log('📊 Analytics:', monthlyData.analytics, '🔮 Predictions:', monthlyData.predictions);

// Year filter functionality
document.getElementById('yearFilter').addEventListener('change', function(e) {
    const year = e.target.value;
    const yearText = document.getElementById('yearText');

    if (year === 'all') {
        yearText.textContent = '2023 to ' + new Date().getFullYear();
    } else {
        yearText.textContent = year;
    }

    fetch(`{{ route('admin.dashboard.data') }}?year=${year}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            window.monthlyData = data;
            if (data.stats) window.stats = data.stats;
            updateAllCharts();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            alert('Failed to load chart data: ' + error.message);
        });
});

function updateAllCharts() {
    const year = document.getElementById('yearFilter').value;
    const isEmpty = window.monthlyData.members.every(v => v === 0) && window.monthlyData.loans.every(v => v === 0);

    ['growth', 'distribution', 'loanStatus', 'loanAmount', 'memberGrowth', 'memberActivity', 'revenue', 'asset'].forEach(chart => {
        const emptyDiv = document.getElementById(chart + 'Empty');
        const yearSpan = document.getElementById(chart + 'Year');
        if (isEmpty) {
            emptyDiv.classList.remove('hidden');
            yearSpan.textContent = year === 'all' ? 'All Years' : year;
            window.allCharts[chart].canvas.style.display = 'none';
        } else {
            emptyDiv.classList.add('hidden');
            window.allCharts[chart].canvas.style.display = 'block';
        }
    });

    window.allCharts.growth.data.labels = [...window.monthlyData.months, 'Predicted'];
    window.allCharts.growth.data.datasets[0].data = [...window.monthlyData.members, window.monthlyData.predictions.members];
    window.allCharts.growth.data.datasets[1].data = [...window.monthlyData.loans, window.monthlyData.predictions.loans];
    window.allCharts.growth.update();

    window.allCharts.distribution.data.datasets[0].data = [window.stats.totalMembers, window.stats.totalLoans, window.stats.totalProjects, window.stats.activeFundraisings];
    window.allCharts.distribution.update();

    window.allCharts.loanStatus.data.datasets[0].data = [window.stats.approvedLoans, window.stats.pendingLoans, window.stats.totalLoans - window.stats.approvedLoans - window.stats.pendingLoans];
    window.allCharts.loanStatus.update();

    window.allCharts.loanAmount.data.labels = window.monthlyData.months;
    window.allCharts.loanAmount.data.datasets[0].data = window.monthlyData.loanAmounts;
    window.allCharts.loanAmount.update();

    window.allCharts.memberGrowth.data.labels = window.monthlyData.months;
    window.allCharts.memberGrowth.data.datasets[0].data = window.monthlyData.members;
    window.allCharts.memberGrowth.data.datasets[1].data = window.monthlyData.memberRetention;
    window.allCharts.memberGrowth.update();

    window.allCharts.memberActivity.data.datasets[0].data = [window.stats.totalLoans, window.stats.totalSavings/1000000, window.stats.totalTransactions, window.stats.totalProjects, window.stats.activeFundraisings];
    window.allCharts.memberActivity.data.datasets[1].data = [window.stats.totalLoans * 1.2, (window.stats.totalSavings/1000000) * 1.3, window.stats.totalTransactions * 1.15, window.stats.totalProjects * 1.1, window.stats.activeFundraisings * 1.25];
    window.allCharts.memberActivity.update();

    window.allCharts.revenue.data.labels = window.monthlyData.months;
    window.allCharts.revenue.data.datasets[0].data = window.monthlyData.revenue;
    window.allCharts.revenue.data.datasets[1].data = window.monthlyData.expenses;
    window.allCharts.revenue.data.datasets[2].data = window.monthlyData.revenue.map((r, i) => r - window.monthlyData.expenses[i]);
    window.allCharts.revenue.update();

    window.allCharts.asset.data.datasets[0].data = [window.stats.totalSavings/1000000, window.stats.totalLoanAmount/1000000, window.stats.totalAssets/2000000, window.stats.totalAssets/3000000];
    window.allCharts.asset.update();
}

// Make functions globally accessible
window.updateAllCharts = updateAllCharts;
</script>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endpush

