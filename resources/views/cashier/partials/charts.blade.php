<div class="bg-white rounded-xl shadow-xl p-6 mb-4 md:mb-6">
    <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center justify-between gap-2 mb-6 border-b pb-4">
        <div class="flex flex-wrap gap-2">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-chart-line mr-2"></i>Overview
            </button>
            <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-exchange-alt mr-2"></i>Transactions
            </button>
            <button @click="activeTab = 'cashflow'" :class="activeTab === 'cashflow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-chart-area mr-2"></i>Cash Flow
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
            <h3 class="text-xl font-bold text-gray-800 mb-4">Transaction Trends</h3>
            <div id="trendsEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="trendsYear"></span>'</p>
                </div>
            </div>
            <canvas id="trendsChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Transaction Distribution</h3>
            <div id="distributionEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="distributionYear"></span>'</p>
                </div>
            </div>
            <canvas id="distributionChart"></canvas>
        </div>
    </div>

    <!-- Transactions Tab -->
    <div x-show="activeTab === 'transactions'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Deposits vs Withdrawals</h3>
            <div id="depositsEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-exchange-alt text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="depositsYear"></span>'</p>
                </div>
            </div>
            <canvas id="depositsChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Transaction Volume</h3>
            <div id="volumeEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="volumeYear"></span>'</p>
                </div>
            </div>
            <canvas id="volumeChart"></canvas>
        </div>
    </div>

    <!-- Cash Flow Tab -->
    <div x-show="activeTab === 'cashflow'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Net Cash Flow</h3>
            <div id="netCashEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-area text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="netCashYear"></span>'</p>
                </div>
            </div>
            <canvas id="netCashChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Cash Flow Analysis</h3>
            <div id="analysisEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-analytics text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="analysisYear"></span>'</p>
                </div>
            </div>
            <canvas id="analysisChart"></canvas>
        </div>
    </div>
</div>
