<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8" x-show="activeLink === 'stats'">
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Members</p>
                <p class="text-2xl font-bold text-blue-600" x-text="stats.totalMembers">0</p>
            </div>
            <i class="fas fa-users text-blue-600 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Savings</p>
                <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(stats.totalSavings)">0</p>
            </div>
            <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Active Loans</p>
                <p class="text-2xl font-bold text-yellow-600" x-text="stats.activeLoans">0</p>
            </div>
            <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Projects</p>
                <p class="text-2xl font-bold text-purple-600" x-text="stats.totalProjects">0</p>
            </div>
            <i class="fas fa-project-diagram text-purple-600 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pending Approvals</p>
                <p class="text-2xl font-bold text-red-600" x-text="stats.pendingApprovals">0</p>
            </div>
            <i class="fas fa-clock text-red-600 text-2xl"></i>
        </div>
    </div>
</div>
