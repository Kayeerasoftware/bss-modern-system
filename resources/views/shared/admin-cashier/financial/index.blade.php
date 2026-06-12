@section('title', 'Financial Overview')<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-6" x-data="{ activeTab: {{ Js::from(request('tab', '')) }} }" x-effect="activeTab === '' && window.initAdminFinancialCharts && window.initAdminFinancialCharts()">
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
                <p class="text-gray-600 text-xs md:text-sm font-medium">System Financial Dashboard</p>
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

    <!-- System Snapshot -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Savings Balance</p>
            <p class="text-lg font-bold text-blue-600">UGX {{ number_format($totalAssets) }}</p>
            <p class="text-[10px] text-gray-400">Across all accounts</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Shares Value</p>
            <p class="text-lg font-bold text-teal-600">UGX {{ number_format($totalShares) }}</p>
            <p class="text-[10px] text-gray-400">Current share holdings</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Loan Balance</p>
            <p class="text-lg font-bold text-red-600">UGX {{ number_format($totalLiabilities) }}</p>
            <p class="text-[10px] text-gray-400">Outstanding loans</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Net Position</p>
            <p class="text-lg font-bold {{ $netPosition >= 0 ? 'text-green-600' : 'text-red-600' }}">UGX {{ number_format($netPosition) }}</p>
            <p class="text-[10px] text-gray-400">Assets minus liabilities</p>
        </div>
    </div>

    <!-- Cashflow & Fundraising -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Deposits</p>
            <p class="text-lg font-bold text-green-600">UGX {{ number_format($deposits) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Withdrawals</p>
            <p class="text-lg font-bold text-red-600">UGX {{ number_format($withdrawals) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Transfers</p>
            <p class="text-lg font-bold text-orange-600">UGX {{ number_format($transfers) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Fundraising</p>
            <p class="text-lg font-bold text-amber-600">UGX {{ number_format($fundraisingTotal) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-5 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Net Cashflow (Last 30 Days)</h3>
            <p class="text-2xl font-extrabold {{ $last30dNet >= 0 ? 'text-green-600' : 'text-red-600' }}">UGX {{ number_format($last30dNet) }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Based on completed transactions</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-5 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Dividends Snapshot</h3>
            <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Paid</span><span class="font-semibold text-green-600">UGX {{ number_format($totalDividendsPaid) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-gray-600 mt-1">
                <span>Pending</span><span class="font-semibold text-yellow-600">UGX {{ number_format($totalDividendsPending) }}</span>
            </div>
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

    @php
        $hasMonthlyData = (collect($monthlyRevenue ?? [])->sum() + collect($monthlyExpenses ?? [])->sum()) > 0;
        $hasRevenueData = (($deposits ?? 0) + ($loanPayments ?? 0) + ($totalInterestEarned ?? 0)) > 0;
    @endphp
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Monthly Trend</h3>
                    <p class="text-[11px] text-gray-500">Revenue vs Expenses</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] text-gray-500">
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span>Revenue</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Expenses</span>
                </div>
            </div>
            <div class="relative h-[240px]">
                <canvas id="monthlyChart" style="{{ $hasMonthlyData ? '' : 'display:none;' }}"></canvas>
                <div id="monthlyEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasMonthlyData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <p class="text-gray-600 text-sm font-semibold">No Data Available</p>
                        <p class="text-gray-400 text-[11px]">Start adding transactions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Revenue Sources</h3>
                    <p class="text-[11px] text-gray-500">Deposits, Loans, Interest</p>
                </div>
            </div>
            <div class="relative h-[240px]">
                <canvas id="revenueChart" style="{{ $hasRevenueData ? '' : 'display:none;' }}"></canvas>
                <div id="revenueEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasRevenueData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-14 h-14 rounded-full bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-pie text-white text-xl"></i>
                        </div>
                        <p class="text-gray-600 text-sm font-semibold">No Data Available</p>
                        <p class="text-gray-400 text-[11px]">Revenue will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $hasCategoryData = ($categoryTotals->count() ?? 0) > 0;
        $hasTypeData = ($typeTotals->count() ?? 0) > 0;
        $hasNetData = (collect($monthlyNet ?? [])->sum() !== 0);
    @endphp
    <!-- System Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Top Categories</h3>
            <div class="relative h-[220px]">
                <canvas id="categoryChart" style="{{ $hasCategoryData ? '' : 'display:none;' }}"></canvas>
                <div id="categoryEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasCategoryData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-chart-bar text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-semibold">No Data</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Transaction Mix</h3>
            <div class="relative h-[220px]">
                <canvas id="typeChart" style="{{ $hasTypeData ? '' : 'display:none;' }}"></canvas>
                <div id="typeEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasTypeData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-semibold">No Data</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Monthly Net Cashflow</h3>
            <div class="relative h-[220px]">
                <canvas id="netChart" style="{{ $hasNetData ? '' : 'display:none;' }}"></canvas>
                <div id="netEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasNetData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-chart-line text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-semibold">No Data</p>
                    </div>
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
            <i class="fas fa-exchange-alt mr-1"></i> Transactions ({{ number_format($transactionsCount ?? 0) }})
        </button>
        <button @click="activeTab = activeTab === 'dividends' ? '' : 'dividends'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'dividends' ? 'bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-md' : 'bg-white text-yellow-600 border border-yellow-200 hover:bg-yellow-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-coins mr-1"></i> Dividends ({{ number_format($dividendsCount ?? 0) }})
        </button>
        <button @click="activeTab = activeTab === 'shares' ? '' : 'shares'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'shares' ? 'bg-gradient-to-r from-teal-600 to-teal-700 text-white shadow-md' : 'bg-white text-teal-600 border border-teal-200 hover:bg-teal-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-certificate mr-1"></i> Shares ({{ number_format($sharesCount ?? 0) }})
        </button>
        <button @click="activeTab = activeTab === 'loans' ? '' : 'loans'; $el.scrollIntoView({behavior: 'smooth', block: 'start'})" :class="activeTab === 'loans' ? 'bg-gradient-to-r from-pink-600 to-pink-700 text-white shadow-md' : 'bg-white text-pink-600 border border-pink-200 hover:bg-pink-50'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
            <i class="fas fa-hand-holding-usd mr-1"></i> Loans ({{ number_format($loansCount ?? 0) }})
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

    <!-- Transactions Table -->
    <div x-show="activeTab === 'transactions'" x-cloak class="mb-6">
        @php
            $transactionsCount = $transactions->count();
            $transactionsTotal = $transactions->sum('amount');
            $transactionsDeposits = $transactions->filter(fn ($t) => ($t->transactionType->name ?? '') === 'deposit')->sum('amount');
            $transactionsWithdrawals = $transactions->filter(fn ($t) => ($t->transactionType->name ?? '') === 'withdrawal')->sum('amount');
            $transactionsFundraisingTotal = $transactionsFundraisingTotal ?? 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-3">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-indigo-100 text-[10px] font-medium mb-0.5">Transactions</p>
                <h3 class="text-lg font-bold">{{ number_format($transactionsCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-cyan-100 text-[10px] font-medium mb-0.5">Total Amount</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($transactionsTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Deposits</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($transactionsDeposits) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Withdrawals</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($transactionsWithdrawals) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-[10px] font-semibold text-center text-amber-100 mb-1">Fundraisings</p>
                <ul class="text-[10px] text-amber-100 space-y-0.5 max-h-20 overflow-y-auto">
                    @forelse($fundraisingCampaignTotals as $campaign)
                        <li class="flex justify-between gap-2">
                            <span class="truncate">{{ $campaign->title }}</span>
                            <span class="font-semibold">UGX {{ number_format($campaign->contributions_sum_amount ?? 0) }}</span>
                        </li>
                    @empty
                        <li>No fundraising campaigns</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-indigo-100 p-3 mb-3">
            <input type="hidden" name="tab" value="transactions">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-3">
                    <input type="text" name="transactions_search" value="{{ request('transactions_search') }}" placeholder="Search transactions or members..." class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="transactions_type" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">Type</option>
                        @foreach(['deposit','withdrawal','transfer','loan_payment'] as $type)
                            <option value="{{ $type }}" @selected(request('transactions_type') === $type)>{{ ucfirst(str_replace('_',' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="transactions_status" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">Status</option>
                        @foreach(['completed','pending','failed','cancelled','reversed'] as $status)
                            <option value="{{ $status }}" @selected(request('transactions_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="transactions_date_from" value="{{ request('transactions_date_from') }}" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="transactions_date_to" value="{{ request('transactions_date_to') }}" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="transactions_amount_min" value="{{ request('transactions_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="transactions_amount_max" value="{{ request('transactions_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'transactions']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-700">Transactions</h4>
                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions') }}" class="text-xs font-semibold text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($transactions as $transaction)
                            <tr class="border-b hover:bg-purple-50/40">
                                <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->transactionType->name ?? $transaction->type ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->transactionCategory->display_name ?? ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($transaction->amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($transaction->statusRelation->name ?? $transaction->status ?? 'completed') }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions.show', $transaction->id) }}" class="text-indigo-600 hover:underline font-semibold">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">No transactions available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Dividends Table -->
    <div x-show="activeTab === 'dividends'" x-cloak class="mb-6">
        @php
            $dividendsCount = $dividends->count();
            $dividendsTotal = $dividends->sum('net_amount');
            $dividendsPaid = $dividends->where('status', 'paid')->sum('net_amount');
            $dividendsPending = $dividends->where('status', 'pending')->sum('net_amount');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-yellow-100 text-[10px] font-medium mb-0.5">Dividends</p>
                <h3 class="text-lg font-bold">{{ number_format($dividendsCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-amber-100 text-[10px] font-medium mb-0.5">Total Amount</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($dividendsTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Paid</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($dividendsPaid) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Pending</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($dividendsPending) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-yellow-100 p-3 mb-3">
            <input type="hidden" name="tab" value="dividends">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-3">
                    <input type="text" name="dividends_search" value="{{ request('dividends_search') }}" placeholder="Search dividends or members..." class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="dividends_status" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                        <option value="">Status</option>
                        @foreach(['paid','pending'] as $status)
                            <option value="{{ $status }}" @selected(request('dividends_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="dividends_date_from" value="{{ request('dividends_date_from') }}" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="dividends_date_to" value="{{ request('dividends_date_to') }}" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="dividends_amount_min" value="{{ request('dividends_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="dividends_amount_max" value="{{ request('dividends_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-yellow-500 to-amber-500 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'dividends']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Dividends</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-yellow-500 to-yellow-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Year</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Quarter</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($dividends as $dividend)
                            <tr class="border-b hover:bg-yellow-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->dividend->year ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->dividend->quarter ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($dividend->net_amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($dividend->status ?? 'pending') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($dividend->paid_at ?? $dividend->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No dividends available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Shares Table -->
    <div x-show="activeTab === 'shares'" x-cloak class="mb-6">
        @php
            $sharesCount = $shares->count();
            $sharesTotal = $shares->sum('shares_count');
            $sharesValue = $shares->sum('current_value');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-teal-100 text-[10px] font-medium mb-0.5">Share Records</p>
                <h3 class="text-lg font-bold">{{ number_format($sharesCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-cyan-100 text-[10px] font-medium mb-0.5">Total Shares</p>
                <h3 class="text-lg font-bold">{{ number_format($sharesTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Total Value</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($sharesValue) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-blue-100 text-[10px] font-medium mb-0.5">Avg Value</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($sharesCount > 0 ? $sharesValue / $sharesCount : 0) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-teal-100 p-3 mb-3">
            <input type="hidden" name="tab" value="shares">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-4">
                    <input type="text" name="shares_search" value="{{ request('shares_search') }}" placeholder="Search shares or members..." class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="shares_date_from" value="{{ request('shares_date_from') }}" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="shares_date_to" value="{{ request('shares_date_to') }}" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="shares_value_min" value="{{ request('shares_value_min') }}" placeholder="Min value" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="shares_value_max" value="{{ request('shares_value_max') }}" placeholder="Max value" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'shares']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Shares</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-teal-500 to-teal-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Certificate</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Value</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($shares as $share)
                            <tr class="border-b hover:bg-teal-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $share->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $share->certificate_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ number_format($share->shares_count ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($share->current_value ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($share->purchase_date ?? $share->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No shares available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div x-show="activeTab === 'loans'" x-cloak class="mb-6">
        @php
            $loansCount = $loans->count();
            $loansPrincipal = $loans->sum('principal_amount');
            $loansBalance = $loans->sum('balance_due');
            $loansApproved = $loans->filter(fn ($l) => ($l->status ?? '') === 'approved')->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-pink-100 text-[10px] font-medium mb-0.5">Loans</p>
                <h3 class="text-lg font-bold">{{ number_format($loansCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-purple-100 text-[10px] font-medium mb-0.5">Principal</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($loansPrincipal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Balance</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($loansBalance) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-indigo-100 text-[10px] font-medium mb-0.5">Approved</p>
                <h3 class="text-lg font-bold">{{ number_format($loansApproved) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-pink-100 p-3 mb-3">
            <input type="hidden" name="tab" value="loans">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-4">
                    <input type="text" name="loans_search" value="{{ request('loans_search') }}" placeholder="Search loans or members..." class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="loans_status" value="{{ request('loans_status') }}" placeholder="Status" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="loans_date_from" value="{{ request('loans_date_from') }}" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="loans_date_to" value="{{ request('loans_date_to') }}" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="loans_amount_min" value="{{ request('loans_amount_min') }}" placeholder="Min principal" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="loans_amount_max" value="{{ request('loans_amount_max') }}" placeholder="Max principal" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-pink-600 to-red-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'loans']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Loans</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-pink-500 to-pink-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Loan #</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Principal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($loans as $loan)
                            <tr class="border-b hover:bg-pink-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->loan_number ?? $loan->loan_id ?? '#' . $loan->id }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($loan->principal_amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($loan->balance_due ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->status_label ?? ucfirst($loan->status ?? 'N/A') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($loan->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No loans available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Savings Transactions -->
    <div x-show="activeTab === 'savings'" x-cloak class="mb-6">
        @php
            $savingsCount = $savingsTransactions->count();
            $savingsTotal = $savingsTransactions->sum('amount');
            $savingsDeposits = $savingsTransactions->filter(fn ($t) => ($t->transactionType->name ?? '') === 'deposit')->sum('amount');
            $savingsWithdrawals = $savingsTransactions->filter(fn ($t) => ($t->transactionType->name ?? '') === 'withdrawal')->sum('amount');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Savings Txns</p>
                <h3 class="text-lg font-bold">{{ number_format($savingsCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-emerald-100 text-[10px] font-medium mb-0.5">Total Amount</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($savingsTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-blue-100 text-[10px] font-medium mb-0.5">Deposits</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($savingsDeposits) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Withdrawals</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($savingsWithdrawals) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-green-100 p-3 mb-3">
            <input type="hidden" name="tab" value="savings">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-3">
                    <input type="text" name="savings_search" value="{{ request('savings_search') }}" placeholder="Search savings..." class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="savings_date_from" value="{{ request('savings_date_from') }}" class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="savings_date_to" value="{{ request('savings_date_to') }}" class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="savings_amount_min" value="{{ request('savings_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="savings_amount_max" value="{{ request('savings_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div class="md:col-span-1">
                    <select name="savings_status" class="w-full px-3 py-2 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                        <option value="">Status</option>
                        @foreach(['completed','pending','failed','cancelled','reversed'] as $status)
                            <option value="{{ $status }}" @selected(request('savings_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'savings']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Savings Transactions</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-green-500 to-green-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($savingsTransactions as $transaction)
                            <tr class="border-b hover:bg-green-50/50">
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->transactionType->name ?? $transaction->type ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->transactionCategory->display_name ?? ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($transaction->amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($transaction->statusRelation->name ?? $transaction->status ?? 'completed') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No savings transactions available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expenses Transactions -->
    <div x-show="activeTab === 'expenses'" x-cloak class="mb-6">
        @php
            $expensesCount = $expenseTransactions->count();
            $expensesTotal = $expenseTransactions->sum('amount');
            $expensesAvg = $expensesCount > 0 ? $expensesTotal / $expensesCount : 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Expense Txns</p>
                <h3 class="text-lg font-bold">{{ number_format($expensesCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-rose-100 text-[10px] font-medium mb-0.5">Total Amount</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($expensesTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-orange-100 text-[10px] font-medium mb-0.5">Average</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($expensesAvg) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-lg p-2 text-white shadow-lg">
                <p class="text-gray-200 text-[10px] font-medium mb-0.5">Net Profit</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($netProfit ?? 0) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-red-100 p-3 mb-3">
            <input type="hidden" name="tab" value="expenses">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-3">
                    <input type="text" name="expenses_search" value="{{ request('expenses_search') }}" placeholder="Search expenses..." class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="expenses_date_from" value="{{ request('expenses_date_from') }}" class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="expenses_date_to" value="{{ request('expenses_date_to') }}" class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="expenses_amount_min" value="{{ request('expenses_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="expenses_amount_max" value="{{ request('expenses_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                </div>
                <div class="md:col-span-1">
                    <select name="expenses_status" class="w-full px-3 py-2 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                        <option value="">Status</option>
                        @foreach(['completed','pending','failed','cancelled','reversed'] as $status)
                            <option value="{{ $status }}" @selected(request('expenses_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'expenses']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Expense Transactions</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-red-500 to-red-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($expenseTransactions as $transaction)
                            <tr class="border-b hover:bg-red-50/50">
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->transactionType->name ?? $transaction->type ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->transactionCategory->display_name ?? ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($transaction->amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($transaction->statusRelation->name ?? $transaction->status ?? 'completed') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No expense transactions available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Transactions -->
    <div x-show="activeTab === 'revenue'" x-cloak class="mb-6">
        @php
            $revenueCount = $revenueTransactions->count();
            $revenueTotal = $revenueTransactions->sum('amount');
            $revenueAvg = $revenueCount > 0 ? $revenueTotal / $revenueCount : 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-emerald-100 text-[10px] font-medium mb-0.5">Revenue Txns</p>
                <h3 class="text-lg font-bold">{{ number_format($revenueCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Total Amount</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($revenueTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-blue-100 text-[10px] font-medium mb-0.5">Average</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($revenueAvg) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-purple-100 text-[10px] font-medium mb-0.5">Net Profit</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($netProfit ?? 0) }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 rounded-2xl shadow-lg border border-emerald-100 p-3 mb-3">
            <input type="hidden" name="tab" value="revenue">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-3">
                    <input type="text" name="revenue_search" value="{{ request('revenue_search') }}" placeholder="Search revenue..." class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="revenue_date_from" value="{{ request('revenue_date_from') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="revenue_date_to" value="{{ request('revenue_date_to') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="revenue_amount_min" value="{{ request('revenue_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="revenue_amount_max" value="{{ request('revenue_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-1">
                    <select name="revenue_status" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Status</option>
                        @foreach(['completed','pending','failed','cancelled','reversed'] as $status)
                            <option value="{{ $status }}" @selected(request('revenue_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'revenue']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Revenue Transactions</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-emerald-500 to-emerald-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($revenueTransactions as $transaction)
                            <tr class="border-b hover:bg-emerald-50/50">
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->transactionType->name ?? $transaction->type ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $transaction->transactionCategory->display_name ?? ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($transaction->amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($transaction->statusRelation->name ?? $transaction->status ?? 'completed') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No revenue transactions available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assets -->
    <div x-show="activeTab === 'assets'" x-cloak class="mb-6 space-y-4">
        @php
            $assetSavingsCount = $assetSavingsAccounts->count();
            $assetSavingsTotal = $assetSavingsAccounts->sum('current_balance');
            $assetSharesCount = $assetShares->count();
            $assetSharesTotal = $assetShares->sum('current_value');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-blue-100 text-[10px] font-medium mb-0.5">Savings Accounts</p>
                <h3 class="text-lg font-bold">{{ number_format($assetSavingsCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-indigo-100 text-[10px] font-medium mb-0.5">Savings Balance</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($assetSavingsTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-teal-100 text-[10px] font-medium mb-0.5">Share Holdings</p>
                <h3 class="text-lg font-bold">{{ number_format($assetSharesCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-green-100 text-[10px] font-medium mb-0.5">Shares Value</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($assetSharesTotal) }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Savings Accounts</h4>
            </div>
            <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 border-b border-blue-100 p-3">
                <input type="hidden" name="tab" value="assets">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-4">
                        <input type="text" name="assets_savings_search" value="{{ request('assets_savings_search') }}" placeholder="Search accounts or members..." class="w-full px-3 py-2 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <select name="assets_savings_status" class="w-full px-3 py-2 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Status</option>
                            @foreach(['active','dormant','frozen','closed'] as $status)
                                <option value="{{ $status }}" @selected(request('assets_savings_status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="assets_savings_min" value="{{ request('assets_savings_min') }}" placeholder="Min balance" class="w-full px-3 py-2 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="assets_savings_max" value="{{ request('assets_savings_max') }}" placeholder="Max balance" class="w-full px-3 py-2 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold">Filter</button>
                        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'assets']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Account #</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Account Name</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Current Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Available Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($assetSavingsAccounts as $account)
                            <tr class="border-b hover:bg-blue-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $account->member_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $account->account_number }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $account->account_name }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($account->current_balance ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($account->available_balance ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($account->status ?? 'active') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No savings accounts available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Share Holdings</h4>
            </div>
            <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 border-b border-teal-100 p-3">
                <input type="hidden" name="tab" value="assets">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-6">
                        <input type="text" name="assets_shares_search" value="{{ request('assets_shares_search') }}" placeholder="Search members or certificates..." class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="assets_shares_min" value="{{ request('assets_shares_min') }}" placeholder="Min value" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="assets_shares_max" value="{{ request('assets_shares_max') }}" placeholder="Max value" class="w-full px-3 py-2 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 bg-white">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg font-semibold">Filter</button>
                        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'assets']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-teal-500 to-teal-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Certificate</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Value</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($assetShares as $share)
                            <tr class="border-b hover:bg-teal-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $share->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $share->certificate_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ number_format($share->shares_count ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($share->current_value ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($share->purchase_date ?? $share->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No shares available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Liabilities -->
    <div x-show="activeTab === 'liabilities'" x-cloak class="mb-6 space-y-4">
        @php
            $liabilityLoanCount = $liabilityLoans->count();
            $liabilityLoanTotal = $liabilityLoans->sum('balance_due');
            $liabilityDividendCount = $liabilityDividends->count();
            $liabilityDividendTotal = $liabilityDividends->sum('net_amount');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-pink-100 text-[10px] font-medium mb-0.5">Loans</p>
                <h3 class="text-lg font-bold">{{ number_format($liabilityLoanCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-red-100 text-[10px] font-medium mb-0.5">Loan Balance</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($liabilityLoanTotal) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-yellow-100 text-[10px] font-medium mb-0.5">Pending Dividends</p>
                <h3 class="text-lg font-bold">{{ number_format($liabilityDividendCount) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 text-white shadow-lg">
                <p class="text-orange-100 text-[10px] font-medium mb-0.5">Dividends Total</p>
                <h3 class="text-lg font-bold">UGX {{ number_format($liabilityDividendTotal) }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Loans</h4>
            </div>
            <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 border-b border-pink-100 p-3">
                <input type="hidden" name="tab" value="liabilities">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-4">
                        <input type="text" name="liabilities_loans_search" value="{{ request('liabilities_loans_search') }}" placeholder="Search loans or members..." class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="text" name="liabilities_loans_status" value="{{ request('liabilities_loans_status') }}" placeholder="Status" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="liabilities_loans_min" value="{{ request('liabilities_loans_min') }}" placeholder="Min balance" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="liabilities_loans_max" value="{{ request('liabilities_loans_max') }}" placeholder="Max balance" class="w-full px-3 py-2 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-pink-600 to-red-600 text-white rounded-lg font-semibold">Filter</button>
                        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'liabilities']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-pink-500 to-pink-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Loan #</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Principal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($liabilityLoans as $loan)
                            <tr class="border-b hover:bg-pink-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->loan_number ?? $loan->loan_id ?? '#' . $loan->id }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($loan->principal_amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($loan->balance_due ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $loan->status_label ?? ucfirst($loan->status ?? 'N/A') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($loan->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No loans available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h4 class="text-sm font-bold text-gray-700">Pending Dividends</h4>
            </div>
            <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index') }}" class="bg-white/90 border-b border-yellow-100 p-3">
                <input type="hidden" name="tab" value="liabilities">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-4">
                        <input type="text" name="liabilities_dividends_search" value="{{ request('liabilities_dividends_search') }}" placeholder="Search members..." class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="liabilities_dividends_year" value="{{ request('liabilities_dividends_year') }}" placeholder="Year" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" name="liabilities_dividends_quarter" value="{{ request('liabilities_dividends_quarter') }}" placeholder="Quarter" class="w-full px-3 py-2 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg font-semibold">Filter</button>
                        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.index', ['tab' => 'liabilities']) }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-yellow-500 to-yellow-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Year</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Quarter</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($liabilityDividends as $dividend)
                            <tr class="border-b hover:bg-yellow-50/50">
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->member->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->dividend->year ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $dividend->dividend->quarter ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">UGX {{ number_format($dividend->net_amount ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($dividend->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No pending dividends available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function initAdminFinancialCharts(force = false) {
    if (!window.Chart) {
        const monthlyEmpty = document.getElementById('monthlyEmpty');
        const revenueEmpty = document.getElementById('revenueEmpty');
        const categoryEmpty = document.getElementById('categoryEmpty');
        const typeEmpty = document.getElementById('typeEmpty');
        const netEmpty = document.getElementById('netEmpty');
        if (monthlyEmpty) monthlyEmpty.style.display = '';
        if (revenueEmpty) revenueEmpty.style.display = '';
        if (categoryEmpty) categoryEmpty.style.display = '';
        if (typeEmpty) typeEmpty.style.display = '';
        if (netEmpty) netEmpty.style.display = '';
        return;
    }
    window.adminFinancialCharts = window.adminFinancialCharts || {};
    window.adminFinancialChartsRetry = window.adminFinancialChartsRetry || 0;
    const monthlyRevenue = @json(array_values($monthlyRevenue ?? []));
    const monthlyExpenses = @json(array_values($monthlyExpenses ?? []));
    const monthlyNet = @json(array_values($monthlyNet ?? []));
    const hasMonthlyData = monthlyRevenue.length > 0 && (monthlyRevenue.some(v => v > 0) || monthlyExpenses.some(v => v > 0));
    const fmtCurrency = (v) => 'UGX ' + (Number(v) || 0).toLocaleString();

    if (hasMonthlyData) {
        const monthlyEmptyEl = document.getElementById('monthlyEmpty');
        if (monthlyEmptyEl) monthlyEmptyEl.style.display = 'none';
        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas) {
            if ((monthlyCanvas.clientWidth === 0 || monthlyCanvas.clientHeight === 0) && window.adminFinancialChartsRetry < 3) {
                window.adminFinancialChartsRetry += 1;
                setTimeout(() => initAdminFinancialCharts(true), 300);
                return;
            }
            if (force && window.adminFinancialCharts.monthly) {
                window.adminFinancialCharts.monthly.destroy();
            }
            const monthlyCtx = monthlyCanvas.getContext('2d');
            try {
                window.adminFinancialCharts.monthly = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Revenue',
                            data: monthlyRevenue,
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.15)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }, {
                            label: 'Expenses',
                            data: monthlyExpenses,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.12)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                callbacks: {
                                    label: (ctx) => `${ctx.dataset.label}: ${fmtCurrency(ctx.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 10 }, callback: (v) => fmtCurrency(v) }
                            },
                            x: { ticks: { font: { size: 10 } } }
                        }
                    }
                });
                monthlyCanvas.style.display = '';
            } catch (err) {
                if (monthlyEmptyEl) monthlyEmptyEl.style.display = '';
                monthlyCanvas.style.display = 'none';
            }
        }
    } else {
        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas) monthlyCanvas.style.display = 'none';
    }

    const deposits = {{ $deposits ?? 0 }};
    const loanPayments = {{ $loanPayments ?? 0 }};
    const interestEarned = {{ $totalInterestEarned ?? 0 }};
    const totalRevenue = deposits + loanPayments + interestEarned;
    
    if (totalRevenue > 0) {
        const revenueEmptyEl = document.getElementById('revenueEmpty');
        if (revenueEmptyEl) revenueEmptyEl.style.display = 'none';
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) {
            if (force && window.adminFinancialCharts.revenue) {
                window.adminFinancialCharts.revenue.destroy();
            }
            const revenueCtx = revenueCanvas.getContext('2d');
            try {
                window.adminFinancialCharts.revenue = new Chart(revenueCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Deposits', 'Loan Payments', 'Interest'],
                        datasets: [{
                            data: [deposits, loanPayments, interestEarned],
                            backgroundColor: ['#22c55e', '#3b82f6', '#a855f7'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                callbacks: {
                                    label: (ctx) => `${ctx.label}: ${fmtCurrency(ctx.parsed)}`
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
                revenueCanvas.style.display = '';
            } catch (err) {
                if (revenueEmptyEl) revenueEmptyEl.style.display = '';
                revenueCanvas.style.display = 'none';
            }
        }
    } else {
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) revenueCanvas.style.display = 'none';
    }

    const categoryLabels = @json($categoryTotals->pluck('label') ?? []);
    const categoryTotals = @json($categoryTotals->pluck('total') ?? []);
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryLabels.length > 0 && categoryCanvas) {
        const emptyEl = document.getElementById('categoryEmpty');
        if (emptyEl) emptyEl.style.display = 'none';
        if (force && window.adminFinancialCharts.category) {
            window.adminFinancialCharts.category.destroy();
        }
        try {
            window.adminFinancialCharts.category = new Chart(categoryCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryTotals,
                        backgroundColor: '#6366f1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => fmtCurrency(ctx.parsed.y) } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (v) => fmtCurrency(v) } },
                        x: { ticks: { font: { size: 9 } } }
                    }
                }
            });
            categoryCanvas.style.display = '';
        } catch (err) {
            if (emptyEl) emptyEl.style.display = '';
            categoryCanvas.style.display = 'none';
        }
    }

    const typeLabels = @json($typeTotals->pluck('label') ?? []);
    const typeTotals = @json($typeTotals->pluck('total') ?? []);
    const typeCanvas = document.getElementById('typeChart');
    if (typeLabels.length > 0 && typeCanvas) {
        const emptyEl = document.getElementById('typeEmpty');
        if (emptyEl) emptyEl.style.display = 'none';
        if (force && window.adminFinancialCharts.type) {
            window.adminFinancialCharts.type.destroy();
        }
        try {
            window.adminFinancialCharts.type = new Chart(typeCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeTotals,
                        backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#ef4444','#a855f7','#14b8a6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${fmtCurrency(ctx.parsed)}` } }
                    },
                    cutout: '60%'
                }
            });
            typeCanvas.style.display = '';
        } catch (err) {
            if (emptyEl) emptyEl.style.display = '';
            typeCanvas.style.display = 'none';
        }
    }

    const netCanvas = document.getElementById('netChart');
    if (monthlyNet.length > 0 && monthlyNet.some(v => v !== 0) && netCanvas) {
        const emptyEl = document.getElementById('netEmpty');
        if (emptyEl) emptyEl.style.display = 'none';
        if (force && window.adminFinancialCharts.net) {
            window.adminFinancialCharts.net.destroy();
        }
        try {
            window.adminFinancialCharts.net = new Chart(netCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    datasets: [{
                        label: 'Net',
                        data: monthlyNet,
                        backgroundColor: monthlyNet.map(v => (v >= 0 ? '#22c55e' : '#ef4444'))
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => fmtCurrency(ctx.parsed.y) } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (v) => fmtCurrency(v) } },
                        x: { ticks: { font: { size: 9 } } }
                    }
                }
            });
            netCanvas.style.display = '';
        } catch (err) {
            if (emptyEl) emptyEl.style.display = '';
            netCanvas.style.display = 'none';
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { initAdminFinancialCharts(true); });
} else {
    initAdminFinancialCharts(true);
}
</script>
@endpush

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>