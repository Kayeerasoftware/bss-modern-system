<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'reports'" id="reports">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Reports & Analytics</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="border rounded-lg p-4 hover:shadow-lg transition">
            <div class="flex items-center mb-3">
                <i class="fas fa-users text-blue-600 text-3xl mr-3"></i>
                <div>
                    <h4 class="font-semibold">Members Report</h4>
                    <p class="text-xs text-gray-600">Complete member list</p>
                </div>
            </div>
            <button @click="generateReport('members')" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                <i class="fas fa-download mr-2"></i>Generate
            </button>
        </div>
        <div class="border rounded-lg p-4 hover:shadow-lg transition">
            <div class="flex items-center mb-3">
                <i class="fas fa-chart-line text-green-600 text-3xl mr-3"></i>
                <div>
                    <h4 class="font-semibold">Financial Report</h4>
                    <p class="text-xs text-gray-600">Savings & transactions</p>
                </div>
            </div>
            <button @click="generateReport('financial')" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                <i class="fas fa-download mr-2"></i>Generate
            </button>
        </div>
    </div>
</div>
