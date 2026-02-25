<div class="bg-white rounded-xl shadow-xl p-6">
    <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center justify-between gap-2 mb-6 border-b pb-4">
        <div class="flex flex-wrap gap-2">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-chart-line mr-2"></i>Overview
            </button>
            <button @click="activeTab = 'dividends'" :class="activeTab === 'dividends' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-hand-holding-usd mr-2"></i>Dividends
            </button>
            <button @click="activeTab = 'portfolio'" :class="activeTab === 'portfolio' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-briefcase mr-2"></i>Portfolio
            </button>
            <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-project-diagram mr-2"></i>Projects
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
            <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Growth</h3>
            <div id="growthEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="growthYear"></span>'</p>
                </div>
            </div>
            <canvas id="growthChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Portfolio Distribution</h3>
            <div id="distributionEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="distributionYear"></span>'</p>
                </div>
            </div>
            <canvas id="distributionChart"></canvas>
        </div>
    </div>

    <!-- Dividends Tab -->
    <div x-show="activeTab === 'dividends'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Dividend History</h3>
            <div id="dividendEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-hand-holding-usd text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="dividendYear"></span>'</p>
                </div>
            </div>
            <canvas id="dividendChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Share Value Trend</h3>
            <div id="shareValueEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-coins text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="shareValueYear"></span>'</p>
                </div>
            </div>
            <canvas id="shareValueChart"></canvas>
        </div>
    </div>

    <!-- Portfolio Tab -->
    <div x-show="activeTab === 'portfolio'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Performance Metrics</h3>
            <div id="performanceEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="performanceYear"></span>'</p>
                </div>
            </div>
            <canvas id="performanceChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Breakdown</h3>
            <div id="investmentEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-dollar-sign text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="investmentYear"></span>'</p>
                </div>
            </div>
            <canvas id="investmentChart"></canvas>
        </div>
    </div>

    <!-- Projects Tab -->
    <div x-show="activeTab === 'projects'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Project ROI</h3>
            <div id="roiEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-percentage text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="roiYear"></span>'</p>
                </div>
            </div>
            <canvas id="roiChart"></canvas>
        </div>
        <div class="h-80 relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Allocation</h3>
            <div id="allocationEmpty" class="hidden absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-project-diagram text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-semibold">No data for year '<span id="allocationYear"></span>'</p>
                </div>
            </div>
            <canvas id="allocationChart"></canvas>
        </div>
    </div>
</div>

