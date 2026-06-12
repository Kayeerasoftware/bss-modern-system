<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'financial'" id="financial">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Financial Management</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Total Deposits</p>
            <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(financialSummary.totalDeposits)">0</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Total Withdrawals</p>
            <p class="text-2xl font-bold text-red-600" x-text="formatCurrency(financialSummary.totalWithdrawals)">0</p>
        </div>
    </div>
</div>
