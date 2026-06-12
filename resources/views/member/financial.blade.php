@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-6" x-data="{ activeTab: '' }">
    <div class="mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-chart-pie text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Financial Overview</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">{{ $member->full_name }} - Personal Financial Dashboard</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Financial data...</span>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Revenue</p>
            <h3 class="text-xl font-bold">UGX {{ number_format($totalRevenue/1000, 1) }}K</h3>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-red-100 text-[10px] font-medium mb-0.5">Expenses</p>
            <h3 class="text-xl font-bold">UGX {{ number_format($totalExpenses/1000, 1) }}K</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Net Profit</p>
            <h3 class="text-xl font-bold">UGX {{ number_format($netProfit/1000, 1) }}K</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Assets</p>
            <h3 class="text-xl font-bold">UGX {{ number_format($totalAssets/1000, 1) }}K</h3>
        </div>
    </div>

    <!-- Balance Sheet -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-wallet text-green-600"></i> Assets
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Savings</span>
                    <span class="font-semibold">UGX {{ number_format($totalAssets) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Shares</span>
                    <span class="font-semibold">UGX {{ number_format($totalShares) }}</span>
                </div>
                <div class="flex justify-between text-xs pt-2 border-t">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-green-600">UGX {{ number_format($totalAssets + $totalShares) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-file-invoice text-red-600"></i> Liabilities
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Loans</span>
                    <span class="font-semibold">UGX {{ number_format($totalLiabilities) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Pending Dividends</span>
                    <span class="font-semibold">UGX {{ number_format($totalDividendsPending) }}</span>
                </div>
                <div class="flex justify-between text-xs pt-2 border-t">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-red-600">UGX {{ number_format($totalLiabilities + $totalDividendsPending) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-balance-scale text-blue-600"></i> Equity
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Net Worth</span>
                    <span class="font-semibold">UGX {{ number_format($totalEquity) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Share Capital</span>
                    <span class="font-semibold">UGX {{ number_format($totalShares) }}</span>
                </div>
                <div class="flex justify-between text-xs pt-2 border-t">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-blue-600">UGX {{ number_format($totalEquity + $totalShares) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Monthly Trend</h3>
            <div style="height: 200px; position: relative;">
                <canvas id="monthlyChart"></canvas>
                <div id="monthlyEmpty" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                        <i class="fas fa-chart-line" style="color: white; font-size: 32px;"></i>
                    </div>
                    <p style="color: #6b7280; font-weight: 600; font-size: 14px; margin: 0;">No Data Available</p>
                    <p style="color: #9ca3af; font-size: 11px; margin-top: 4px;">Start adding transactions</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Revenue Sources</h3>
            <div style="height: 200px; position: relative;">
                <canvas id="revenueChart"></canvas>
                <div id="revenueEmpty" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);">
                        <i class="fas fa-chart-pie" style="color: white; font-size: 32px;"></i>
                    </div>
                    <p style="color: #6b7280; font-weight: 600; font-size: 14px; margin: 0;">No Data Available</p>
                    <p style="color: #9ca3af; font-size: 11px; margin-top: 4px;">Revenue will appear here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Portfolio & Financial Ratios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-green-600"></i> Loan Portfolio
            </h3>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <p class="text-[10px] text-gray-600 mb-1">Active Loans</p>
                    <p class="text-base font-bold text-purple-600">{{ $activeLoans }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <p class="text-[10px] text-gray-600 mb-1">Total Amount</p>
                    <p class="text-base font-bold text-purple-600">{{ number_format($totalLoanAmount/1000, 1) }}K</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <p class="text-[10px] text-gray-600 mb-1">Interest Earned</p>
                    <p class="text-base font-bold text-green-600">{{ number_format($totalInterestEarned/1000, 1) }}K</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <p class="text-[10px] text-gray-600 mb-1">Avg Rate</p>
                    <p class="text-base font-bold text-blue-600">{{ number_format($avgInterestRate, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-600"></i> Key Ratios
            </h3>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                    <p class="text-[10px] text-gray-600 mb-1">Profit Margin</p>
                    <p class="text-base font-bold text-blue-600">{{ number_format($profitMargin, 1) }}%</p>
                </div>
                <div class="bg-green-50 rounded-lg p-2 border border-green-100">
                    <p class="text-[10px] text-gray-600 mb-1">ROA</p>
                    <p class="text-base font-bold text-green-600">{{ number_format($roi, 1) }}%</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <p class="text-[10px] text-gray-600 mb-1">Debt/Equity</p>
                    <p class="text-base font-bold text-purple-600">{{ number_format($debtToEquity, 2) }}</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-2 border border-orange-100">
                    <p class="text-[10px] text-gray-600 mb-1">Current Ratio</p>
                    <p class="text-base font-bold text-orange-600">{{ number_format($currentRatio, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Data Tables - Click buttons to view -->
    <div id="recordsSection" class="mb-6 bg-gradient-to-r from-purple-50 via-pink-50 to-purple-50 rounded-xl p-4 border-l-4 border-purple-600 shadow-md">
        <h3 class="text-lg md:text-xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1 flex items-center gap-3">
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 p-2 rounded-lg shadow-lg">
                <i class="fas fa-table text-white text-base"></i>
            </div>
            Comprehensive Financial Records & Analytics
        </h3>
        <p class="text-xs text-gray-600 ml-11">Select a category below to view detailed financial data and insights</p>
    </div>
    <div class="flex flex-wrap gap-3 mb-6">
        <button @click="activeTab = activeTab === 'transactions' ? '' : 'transactions'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'transactions' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md' : 'bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-exchange-alt mr-1"></i> Transactions ({{ count($transactions) }})
        </button>
        <button @click="activeTab = activeTab === 'dividends' ? '' : 'dividends'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'dividends' ? 'bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-md' : 'bg-white text-yellow-600 border border-yellow-200 hover:bg-yellow-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-coins mr-1"></i> Dividends ({{ count($dividends) }})
        </button>
        <button @click="activeTab = activeTab === 'shares' ? '' : 'shares'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'shares' ? 'bg-gradient-to-r from-teal-600 to-teal-700 text-white shadow-md' : 'bg-white text-teal-600 border border-teal-200 hover:bg-teal-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-certificate mr-1"></i> Shares ({{ count($shares) }})
        </button>
        <button @click="activeTab = activeTab === 'loans' ? '' : 'loans'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'loans' ? 'bg-gradient-to-r from-pink-600 to-pink-700 text-white shadow-md' : 'bg-white text-pink-600 border border-pink-200 hover:bg-pink-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-hand-holding-usd mr-1"></i> Loans ({{ count($loans) }})
        </button>
        <button @click="activeTab = activeTab === 'savings' ? '' : 'savings'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'savings' ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' : 'bg-white text-green-600 border border-green-200 hover:bg-green-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-piggy-bank mr-1"></i> Savings (1)
        </button>
        <button @click="activeTab = activeTab === 'expenses' ? '' : 'expenses'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'expenses' ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-md' : 'bg-white text-red-600 border border-red-200 hover:bg-red-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-receipt mr-1"></i> Expenses (1)
        </button>
        <button @click="activeTab = activeTab === 'revenue' ? '' : 'revenue'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'revenue' ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md' : 'bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-chart-line mr-1"></i> Revenue (1)
        </button>
        <button @click="activeTab = activeTab === 'assets' ? '' : 'assets'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'assets' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md' : 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-wallet mr-1"></i> Assets (2)
        </button>
        <button @click="activeTab = activeTab === 'liabilities' ? '' : 'liabilities'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'liabilities' ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-md' : 'bg-white text-orange-600 border border-orange-200 hover:bg-orange-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-file-invoice mr-1"></i> Liabilities (2)
        </button>
    </div>

    <!-- Placeholder Table -->
    <div x-show="activeTab === ''" class="mb-6">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase ">Column 1</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase ">Column 2</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase ">Column 3</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Column 4</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-table text-gray-300 text-5xl"></i>
                                    <p class="text-gray-500 font-semibold text-lg">Data will appear here</p>
                                    <p class="text-gray-400 text-sm">Click on any button above to view detailed financial records</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyRevenue = {{ json_encode(array_values($monthlyRevenue ?? [])) }};
    const monthlyExpenses = {{ json_encode(array_values($monthlyExpenses ?? [])) }};
    const hasMonthlyData = monthlyRevenue.length > 0 && (monthlyRevenue.some(v => v > 0) || monthlyExpenses.some(v => v > 0));
    
    if (hasMonthlyData) {
        document.getElementById('monthlyEmpty').style.display = 'none';
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: monthlyRevenue,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Expenses',
                    data: monthlyExpenses,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 } } } },
                scales: { y: { beginAtZero: true, ticks: { font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } }
            }
        });
    } else {
        document.getElementById('monthlyChart').style.display = 'none';
    }

    const deposits = {{ $deposits ?? 0 }};
    const loanPayments = {{ $loanPayments ?? 0 }};
    const interestEarned = {{ $totalInterestEarned ?? 0 }};
    const totalRevenue = deposits + loanPayments + interestEarned;
    
    if (totalRevenue > 0) {
        document.getElementById('revenueEmpty').style.display = 'none';
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: ['Deposits', 'Loan Payments', 'Interest'],
                datasets: [{
                    data: [deposits, loanPayments, interestEarned],
                    backgroundColor: ['rgb(34, 197, 94)', 'rgb(59, 130, 246)', 'rgb(168, 85, 247)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
            }
        });
    } else {
        document.getElementById('revenueChart').style.display = 'none';
    }
});
</script>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>
@endsection

