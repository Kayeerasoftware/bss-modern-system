<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3 mb-4 md:mb-6">
    <!-- Today's Deposits -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-green-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-arrow-down text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Deposits</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['todayDeposits']/1000000, 1) }}M <span class="text-xs text-gray-500 font-medium">UGX</span></h3>
            </div>
        </div>
    </div>

    <!-- Today's Withdrawals -->
    <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-red-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-red-500 to-red-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-arrow-up text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Withdrawals</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['todayWithdrawals']/1000000, 1) }}M <span class="text-xs text-gray-500 font-medium">UGX</span></h3>
            </div>
        </div>
    </div>

    <!-- Today's Net -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-blue-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-balance-scale text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Net</p>
                <h3 class="text-xl md:text-3xl font-bold {{ $stats['todayNet'] >= 0 ? 'text-green-600' : 'text-red-600' }} leading-tight">{{ number_format($stats['todayNet']/1000000, 1) }}M</h3>
            </div>
        </div>
    </div>

    <!-- Today's Transactions -->
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-purple-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-exchange-alt text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Transactions</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['todayTransactions'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Members -->
    <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-indigo-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-users text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Members</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['totalMembers'] }} <span class="text-xs text-gray-500 font-medium">{{ $stats['activeMembers'] }} active</span></h3>
            </div>
        </div>
    </div>

    <!-- Cash on Hand -->
    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-yellow-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-wallet text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Cash on Hand</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['cashOnHand']/1000000, 1) }}M</h3>
            </div>
        </div>
    </div>

    <!-- Pending Loans -->
    <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-orange-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-clock text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Pending Loans</p>
                <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['pendingLoans'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-teal-200">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-2 md:p-2.5 rounded-lg shadow">
                <i class="fas fa-bolt text-white text-base md:text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight mb-1">Quick Actions</p>
                <div class="flex gap-1">
                    <a href="{{ route('cashier.deposits.create') }}" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition">Deposit</a>
                    <a href="{{ route('cashier.withdrawals.create') }}" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition">Withdraw</a>
                </div>
            </div>
        </div>
    </div>
</div>
