<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
    <!-- Total Shares -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-blue-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-chart-pie text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Shares</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['totalShares']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Share Value -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-green-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-coins text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Share Value</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['shareValue']/1000000, 1) }}M <span class="text-xs text-gray-500 font-medium">UGX</span></h3>
            </div>
        </div>
    </div>

    <!-- Total Dividends -->
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-purple-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-hand-holding-usd text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Dividends</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['totalDividends']/1000000, 1) }}M <span class="text-xs text-yellow-600 font-medium">{{ number_format($stats['pendingDividends']/1000, 1) }}K pending</span></h3>
            </div>
        </div>
    </div>

    <!-- Portfolio Value -->
    <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-orange-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-briefcase text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Portfolio Value</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['portfolioValue']/1000000, 1) }}M</h3>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
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

    <!-- Total Investments -->
    <div class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-pink-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-dollar-sign text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Investments</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['totalInvestments']/1000000, 1) }}M</h3>
            </div>
        </div>
    </div>

    <!-- Average ROI -->
    <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-teal-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-percentage text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Average ROI</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['avgROI'], 1) }}%</h3>
            </div>
        </div>
    </div>

    <!-- Performance Rate -->
    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-yellow-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-chart-line text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Performance</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['performanceRate'], 1) }}%</h3>
            </div>
        </div>
    </div>
</div>

