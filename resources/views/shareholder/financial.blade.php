@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6" x-data="{ activeTab: '' }">
    <div class="mb-4 md:mb-6">
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

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Financial data...</span>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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

        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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

        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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
    <!-- Debug: {{ json_encode($monthlyRevenue) }} | {{ json_encode($monthlyExpenses) }} | Deposits: {{ $deposits }} | Loans: {{ $loanPayments }} | Interest: {{ $totalInterestEarned }} -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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

        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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

        <div class="bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
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
    <div id="recordsSection" class="mb-4 bg-gradient-to-r from-purple-50 via-pink-50 to-purple-50 rounded-xl p-4 border-l-4 border-purple-600 shadow-md">
        <h3 class="text-lg md:text-xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1 flex items-center gap-3">
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 p-2 rounded-lg shadow-lg">
                <i class="fas fa-table text-white text-base"></i>
            </div>
            Comprehensive Financial Records & Analytics
        </h3>
        <p class="text-xs text-gray-600 ml-11">Select a category below to view detailed financial data and insights</p>
    </div>
    <div class="flex flex-wrap gap-2 mb-3">
        <button @click="activeTab = activeTab === 'transactions' ? '' : 'transactions'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'transactions' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md' : 'bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-exchange-alt mr-1"></i> Transactions ({{ $transactions->total() }})
        </button>
        <button @click="activeTab = activeTab === 'dividends' ? '' : 'dividends'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'dividends' ? 'bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-md' : 'bg-white text-yellow-600 border border-yellow-200 hover:bg-yellow-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-coins mr-1"></i> Dividends ({{ $dividends->total() }})
        </button>
        <button @click="activeTab = activeTab === 'shares' ? '' : 'shares'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'shares' ? 'bg-gradient-to-r from-teal-600 to-teal-700 text-white shadow-md' : 'bg-white text-teal-600 border border-teal-200 hover:bg-teal-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-certificate mr-1"></i> Shares ({{ $shares->total() }})
        </button>
        <button @click="activeTab = activeTab === 'loans' ? '' : 'loans'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'loans' ? 'bg-gradient-to-r from-pink-600 to-pink-700 text-white shadow-md' : 'bg-white text-pink-600 border border-pink-200 hover:bg-pink-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-hand-holding-usd mr-1"></i> Loans ({{ $loans->total() }})
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
    <div x-show="activeTab === ''" class="mb-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Column 1</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Column 2</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Column 3</th>
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

    <!-- Transactions Table -->
    <div x-show="activeTab === 'transactions'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="type" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Types</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Date</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Amount</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($transactions as $transaction)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                            <td class="px-4 py-3 border-r border-gray-200">
                                <p class="text-xs text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm">UGX {{ number_format($transaction->amount) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $transaction->status ?? 'Completed' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No transactions found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $transactions->links() }}</div>
        </div>
    </div>

    <!-- Dividends Table -->
    <div x-show="activeTab === 'dividends'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search dividends..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Period</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Amount</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Payment Date</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($dividends as $dividend)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="text-xs font-semibold">Q{{ $dividend->quarter ?? 1 }} {{ $dividend->year ?? date('Y') }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm text-green-600">UGX {{ number_format($dividend->amount) }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="text-xs">{{ $dividend->payment_date ? \Carbon\Carbon::parse($dividend->payment_date)->format('M d, Y') : 'Pending' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $dividend->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($dividend->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No dividends found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $dividends->links() }}</div>
        </div>
    </div>

    <!-- Shares Table -->
    <div x-show="activeTab === 'shares'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shares..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="value_high" {{ request('sort') == 'value_high' ? 'selected' : '' }}>Value (High-Low)</option>
                                <option value="value_low" {{ request('sort') == 'value_low' ? 'selected' : '' }}>Value (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="value_min" value="{{ request('value_min') }}" placeholder="Min Value" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Certificate</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Shares Owned</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Share Value</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($shares as $share)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="text-xs font-semibold text-purple-600">{{ $share->certificate_number ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm">{{ number_format($share->shares_owned) }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm text-blue-600">UGX {{ number_format($share->share_value) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs">{{ \Carbon\Carbon::parse($share->purchase_date)->format('M d, Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No shares found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $shares->links() }}</div>
        </div>
    </div>

    <!-- Loans Table -->
    <div x-show="activeTab === 'loans'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search loans..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Loan ID</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Amount</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Interest</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Monthly Payment</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($loans as $loan)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="text-xs font-semibold text-purple-600">{{ $loan->loan_id }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm">UGX {{ number_format($loan->amount) }}</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="text-xs text-orange-600">{{ $loan->interest_rate }}% (UGX {{ number_format($loan->interest) }})</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="font-bold text-sm text-blue-600">UGX {{ number_format($loan->monthly_payment) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $loan->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No loans found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $loans->links() }}</div>
        </div>
    </div>

    <!-- Savings Table -->
    <div x-show="activeTab === 'savings'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search savings..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="type" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Types</option>
                                <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="balance_high" {{ request('sort') == 'balance_high' ? 'selected' : '' }}>Balance (High-Low)</option>
                                <option value="balance_low" {{ request('sort') == 'balance_low' ? 'selected' : '' }}>Balance (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="balance_min" value="{{ request('balance_min') }}" placeholder="Min Balance" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-green-600 via-emerald-600 to-green-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Date</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Account Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Balance</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Interest Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="bg-white hover:bg-green-50 border-l-4 border-green-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs">{{ now()->format('M d, Y') }}</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-green-50 text-green-700">Regular Savings</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-green-600">UGX {{ number_format($totalAssets) }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs font-semibold text-blue-600">{{ number_format($avgInterestRate ?? 5.0, 1) }}%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div x-show="activeTab === 'expenses'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search expenses..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="category" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Categories</option>
                                <option value="operating" {{ request('category') == 'operating' ? 'selected' : '' }}>Operating</option>
                                <option value="admin" {{ request('category') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-red-600 via-rose-600 to-red-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Date</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Category</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Description</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="bg-white hover:bg-red-50 border-l-4 border-red-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs">{{ now()->format('M d, Y') }}</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700">Operating Costs</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs">Total Expenses</span></td>
                            <td class="px-4 py-3"><span class="font-bold text-sm text-red-600">UGX {{ number_format($totalExpenses) }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div x-show="activeTab === 'revenue'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search revenue..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="source" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Sources</option>
                                <option value="interest" {{ request('source') == 'interest' ? 'selected' : '' }}>Interest</option>
                                <option value="fees" {{ request('source') == 'fees' ? 'selected' : '' }}>Fees</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Source</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Period</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Amount</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Growth</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="bg-white hover:bg-emerald-50 border-l-4 border-emerald-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700">Total Revenue</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs">{{ now()->format('Y') }}</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-emerald-600">UGX {{ number_format($totalRevenue) }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs font-semibold text-green-600">+{{ number_format($profitMargin, 1) }}%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div x-show="activeTab === 'assets'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search assets..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="category" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Categories</option>
                                <option value="liquid" {{ request('category') == 'liquid' ? 'selected' : '' }}>Liquid Assets</option>
                                <option value="investment" {{ request('category') == 'investment' ? 'selected' : '' }}>Investments</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="value_high" {{ request('sort') == 'value_high' ? 'selected' : '' }}>Value (High-Low)</option>
                                <option value="value_low" {{ request('sort') == 'value_low' ? 'selected' : '' }}>Value (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="value_min" value="{{ request('value_min') }}" placeholder="Min Value" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Asset Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Category</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Value</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="bg-white hover:bg-blue-50 border-l-4 border-blue-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs font-semibold">Savings Account</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700">Liquid Assets</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-blue-600">UGX {{ number_format($totalAssets) }}</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span></td>
                        </tr>
                        <tr class="bg-purple-50 hover:bg-blue-50 border-l-4 border-blue-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs font-semibold">Share Holdings</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700">Investments</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-blue-600">UGX {{ number_format($totalShares) }}</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Liabilities Table -->
    <div x-show="activeTab === 'liabilities'" x-collapse class="mb-4">
        <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
            <form method="GET" x-data="{ showAdvanced: false }" class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search liabilities..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="category" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Categories</option>
                                <option value="longterm" {{ request('category') == 'longterm' ? 'selected' : '' }}>Long-term</option>
                                <option value="current" {{ request('category') == 'current' ? 'selected' : '' }}>Current</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.financial') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-orange-600 via-amber-600 to-orange-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Liability Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Category</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase border-r border-white/20">Amount</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Due Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="bg-white hover:bg-orange-50 border-l-4 border-orange-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs font-semibold">Outstanding Loans</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700">Long-term Debt</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-orange-600">UGX {{ number_format($totalLiabilities) }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs">Ongoing</span></td>
                        </tr>
                        <tr class="bg-purple-50 hover:bg-orange-50 border-l-4 border-orange-500">
                            <td class="px-4 py-3 border-r border-gray-200"><span class="text-xs font-semibold">Pending Dividends</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="px-2 py-1 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700">Current Liability</span></td>
                            <td class="px-4 py-3 border-r border-gray-200"><span class="font-bold text-sm text-orange-600">UGX {{ number_format($totalDividendsPending) }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs">Quarterly</span></td>
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
