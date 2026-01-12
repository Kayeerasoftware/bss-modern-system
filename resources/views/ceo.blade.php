<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CEO Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="ceoDashboard()">
    <!-- Header -->
    <nav class="bg-gradient-to-r from-gray-800 to-gray-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-crown text-2xl text-yellow-400"></i>
                    <div>
                        <h1 class="text-xl font-bold">CEO Executive Dashboard</h1>
                        <p class="text-gray-300 text-sm">Strategic Overview & Business Intelligence</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-sm text-gray-300">Total Assets</p>
                        <p class="font-semibold text-lg text-green-400">UGX 125.8M</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-300">YTD Growth</p>
                        <p class="font-semibold text-lg text-blue-400">+18.5%</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Executive Summary -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(executiveData.totalRevenue)">UGX 45.2M</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+22% YoY</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Net Profit</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(executiveData.netProfit)">UGX 12.8M</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-coins text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-blue-600">
                        <span>28.3% margin</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="executiveData.totalMembers">1,247</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-purple-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+8.5% growth</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Portfolio ROI</p>
                        <p class="text-2xl font-bold text-orange-600">16.7%</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-percentage text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>Above target</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Risk Score</p>
                        <p class="text-2xl font-bold text-red-600">2.3</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <span>Low risk</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Strategic Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue & Profit Trends -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Revenue & Profit Trends</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>Last 12 months</option>
                        <option>Last 24 months</option>
                        <option>Last 5 years</option>
                    </select>
                </div>
                <div style="height: 250px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Business Segments Performance -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Business Segments</h3>
                    <span class="text-sm text-gray-500">Revenue contribution</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="segmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Strategic Initiatives & Key Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Strategic Initiatives -->
            <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Strategic Initiatives</h3>
                    <button @click="showAddInitiative = true" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                        <i class="fas fa-plus mr-2"></i>New Initiative
                    </button>
                </div>
                <div class="space-y-4">
                    <template x-for="initiative in strategicInitiatives" :key="initiative.id">
                        <div class="p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium" x-text="initiative.title">Digital Transformation</h4>
                                    <p class="text-sm text-gray-600" x-text="initiative.description">Modernizing core systems</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs rounded-full" 
                                          :class="initiative.status === 'on-track' ? 'bg-green-100 text-green-800' : 
                                                 initiative.status === 'at-risk' ? 'bg-yellow-100 text-yellow-800' : 
                                                 'bg-red-100 text-red-800'"
                                          x-text="initiative.status">On Track</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Progress</span>
                                <span class="text-sm font-medium" x-text="initiative.progress + '%'">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                <div class="bg-gray-800 h-2 rounded-full" :style="`width: ${initiative.progress}%`"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span x-text="'Budget: ' + formatCurrency(initiative.budget)">Budget: UGX 15M</span>
                                <span x-text="'ROI: ' + initiative.expectedROI + '%'">ROI: 25%</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Key Performance Indicators -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Key Performance Indicators</h3>
                <div class="space-y-4">
                    <template x-for="kpi in keyMetrics" :key="kpi.name">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium" x-text="kpi.name">Customer Satisfaction</span>
                                <span class="text-sm font-bold" :class="kpi.trend === 'up' ? 'text-green-600' : kpi.trend === 'down' ? 'text-red-600' : 'text-gray-600'" x-text="kpi.value">94%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" :class="kpi.value >= kpi.target ? 'bg-green-500' : 'bg-yellow-500'" :style="`width: ${(kpi.value/100)*100}%`"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span x-text="'Target: ' + kpi.target + '%'">Target: 90%</span>
                                <i class="fas" :class="kpi.trend === 'up' ? 'fa-arrow-up text-green-500' : kpi.trend === 'down' ? 'fa-arrow-down text-red-500' : 'fa-minus text-gray-500'"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Market Analysis & Competitive Intelligence -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Market Analysis -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Market Analysis</h3>
                    <button class="text-gray-600 text-sm hover:underline">View Full Report</button>
                </div>
                <div class="space-y-4">
                    <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-green-800">Market Share Growth</h4>
                                <p class="text-sm text-green-600">Increased by 3.2% this quarter</p>
                            </div>
                            <div class="text-2xl font-bold text-green-600">15.8%</div>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-blue-800">Customer Acquisition</h4>
                                <p class="text-sm text-blue-600">New members this month</p>
                            </div>
                            <div class="text-2xl font-bold text-blue-600">127</div>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-purple-800">Brand Recognition</h4>
                                <p class="text-sm text-purple-600">Industry ranking</p>
                            </div>
                            <div class="text-2xl font-bold text-purple-600">#3</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Executive Alerts -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Executive Alerts</h3>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full" x-text="executiveAlerts.length + ' active'">3 active</span>
                </div>
                <div class="space-y-3">
                    <template x-for="alert in executiveAlerts" :key="alert.id">
                        <div class="p-3 rounded-lg border-l-4" :class="alert.priority === 'high' ? 'bg-red-50 border-red-500' : 
                                                                        alert.priority === 'medium' ? 'bg-yellow-50 border-yellow-500' : 
                                                                        'bg-blue-50 border-blue-500'">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-sm" x-text="alert.title">Loan Default Rate Increase</h4>
                                    <p class="text-xs text-gray-600 mt-1" x-text="alert.description">Default rate increased to 2.1%</p>
                                </div>
                                <div class="text-xs text-gray-500" x-text="alert.time">2h ago</div>
                            </div>
                            <div class="mt-2">
                                <button class="text-xs px-2 py-1 bg-gray-800 text-white rounded hover:bg-gray-700">
                                    Review
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Financial Health & Forecasting -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Financial Health & Forecasting</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">Quarterly</button>
                    <button class="px-3 py-1 bg-gray-800 text-white rounded text-sm">Annual</button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">AAA</div>
                    <div class="text-sm text-gray-600">Credit Rating</div>
                    <div class="text-xs text-green-600 mt-1">Stable outlook</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">2.8x</div>
                    <div class="text-sm text-gray-600">Debt-to-Equity</div>
                    <div class="text-xs text-blue-600 mt-1">Healthy ratio</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">18.5%</div>
                    <div class="text-sm text-gray-600">ROE</div>
                    <div class="text-xs text-purple-600 mt-1">Above industry</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">6.2M</div>
                    <div class="text-sm text-gray-600">Cash Reserves</div>
                    <div class="text-xs text-orange-600 mt-1">3 months runway</div>
                </div>
            </div>
        </div>

        <!-- Member Accounts Management -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Member Accounts</h3>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="memberSearch" @input="filterMembers()" placeholder="Search members..." class="px-4 py-2 border rounded-lg">
                    <button @click="showAddMemberModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Add Member
                    </button>
                    <button @click="loadAllData()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occupation</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="member in filteredMembers" :key="member.member_id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium" x-text="member.member_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.full_name"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="member.email"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.contact"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.location"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.occupation"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800" x-text="member.role"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <button @click="viewMemberDetails(member)" class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="editMember(member)" class="text-green-600 hover:text-green-800 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteMember(member.id)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <p class="text-sm text-gray-600" x-text="`Showing ${filteredMembers.length} of ${allMembers.length} members`"></p>
            </div>
        </div>

        <!-- Financial Operations Management -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Financial Operations</h3>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="financialSearch" @input="filterFinancials()" placeholder="Search members..." class="px-4 py-2 border rounded-lg">
                    <button @click="loadAllData()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Savings</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Loan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="member in filteredFinancials" :key="member.member_id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium" x-text="member.member_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.full_name"></td>
                                <td class="px-4 py-3 text-sm font-medium text-green-600" x-text="formatCurrencyUGX(member.savings)"></td>
                                <td class="px-4 py-3 text-sm font-medium text-red-600" x-text="formatCurrencyUGX(member.loan)"></td>
                                <td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="formatCurrencyUGX(member.balance)"></td>
                                <td class="px-4 py-3">
                                    <button @click="updateFinancials(member)" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        <i class="fas fa-edit mr-1"></i>Update
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Loan Applications Management -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">All Loan Applications</h3>
                <div class="flex items-center space-x-3">
                    <select x-model="loanStatusFilter" @change="filterLoans()" class="px-4 py-2 border rounded-lg">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <input type="text" x-model="loanSearch" @input="filterLoans()" placeholder="Search loans..." class="px-4 py-2 border rounded-lg">
                    <button @click="loadAllData()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Repayment Period</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Applied</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="filteredLoans.length === 0">
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No loan applications found.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="loan in filteredLoans" :key="loan.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium" x-text="loan.loan_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.member_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="getMemberName(loan.member_id)"></td>
                                <td class="px-4 py-3 text-sm font-bold text-purple-600" x-text="formatCurrencyUGX(loan.amount)"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.purpose"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.repayment_months + ' months'"></td>
                                <td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="formatCurrencyUGX(loan.monthly_payment || 0)"></td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full" 
                                          :class="{
                                              'bg-yellow-100 text-yellow-800': loan.status === 'pending',
                                              'bg-green-100 text-green-800': loan.status === 'approved',
                                              'bg-red-100 text-red-800': loan.status === 'rejected'
                                          }"
                                          x-text="loan.status.toUpperCase()"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="new Date(loan.created_at).toLocaleDateString()"></td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button x-show="loan.status === 'pending'" @click="approveLoan(loan.id)" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button x-show="loan.status === 'pending'" @click="rejectLoan(loan.id)" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button @click="editLoan(loan)" class="px-2 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="viewLoanDetails(loan)" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button @click="deleteLoan(loan.id)" class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <p class="text-sm text-gray-600" x-text="`Showing ${filteredLoans.length} of ${allLoans.length} loan applications`"></p>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Total Loan Amount: </span>
                    <span class="text-purple-600 font-bold" x-text="formatCurrencyUGX(filteredLoans.reduce((sum, l) => sum + (l.amount || 0), 0))"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Initiative Modal -->
    <div x-show="showAddInitiative" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">New Strategic Initiative</h3>
            <form @submit.prevent="addInitiative()">
                <div class="space-y-4">
                    <input type="text" x-model="newInitiative.title" placeholder="Initiative Title" class="w-full p-3 border rounded" required>
                    <textarea x-model="newInitiative.description" placeholder="Description" class="w-full p-3 border rounded" rows="3" required></textarea>
                    <input type="number" x-model="newInitiative.budget" placeholder="Budget (UGX)" class="w-full p-3 border rounded" required>
                    <input type="number" x-model="newInitiative.expectedROI" placeholder="Expected ROI (%)" class="w-full p-3 border rounded" required>
                    <select x-model="newInitiative.priority" class="w-full p-3 border rounded" required>
                        <option value="">Select Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddInitiative = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Create Initiative</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div x-show="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add New Member</h3>
            <form @submit.prevent="addMember()">
                <div class="space-y-4">
                    <input type="text" x-model="newMember.full_name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="newMember.email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.contact" placeholder="Contact" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.location" placeholder="Location" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.occupation" placeholder="Occupation" class="w-full p-3 border rounded" required>
                    <select x-model="newMember.role" class="w-full p-3 border rounded" required>
                        <option value="">Select Role</option>
                        <option value="client">Client</option>
                        <option value="shareholder">Shareholder</option>
                        <option value="cashier">Cashier</option>
                        <option value="td">TD</option>
                        <option value="ceo">CEO</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddMemberModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div x-show="showEditMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Edit Member</h3>
            <form @submit.prevent="updateMember()">
                <div class="space-y-4">
                    <input type="text" x-model="editingMember.full_name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="editingMember.email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="editingMember.contact" placeholder="Contact" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="editingMember.location" placeholder="Location" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="editingMember.occupation" placeholder="Occupation" class="w-full p-3 border rounded" required>
                    <select x-model="editingMember.role" class="w-full p-3 border rounded" required>
                        <option value="client">Client</option>
                        <option value="shareholder">Shareholder</option>
                        <option value="cashier">Cashier</option>
                        <option value="td">TD</option>
                        <option value="ceo">CEO</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditMemberModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Financials Modal -->
    <div x-show="showFinancialModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Update Financial Information</h3>
            <form @submit.prevent="saveFinancials()">
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Member: <span class="font-medium" x-text="financialMember.full_name"></span></p>
                        <p class="text-sm text-gray-600">ID: <span class="font-medium" x-text="financialMember.member_id"></span></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Savings Amount</label>
                        <input type="number" x-model="financialMember.savings" placeholder="Savings" class="w-full p-3 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Amount</label>
                        <input type="number" x-model="financialMember.loan" placeholder="Loan" class="w-full p-3 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Balance (Auto-calculated)</label>
                        <input type="number" x-model="financialMember.balance" placeholder="Balance" class="w-full p-3 border rounded bg-gray-100" readonly>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showFinancialModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Loan Modal -->
    <div x-show="showEditLoanModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Edit Loan Application</h3>
            <form @submit.prevent="updateLoan()">
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Loan ID: <span class="font-medium" x-text="editingLoan.loan_id"></span></p>
                        <p class="text-sm text-gray-600">Member: <span class="font-medium" x-text="editingLoan.member_id"></span></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" x-model="editingLoan.amount" placeholder="Amount" class="w-full p-3 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                        <textarea x-model="editingLoan.purpose" placeholder="Purpose" class="w-full p-3 border rounded" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Repayment Period (Months)</label>
                        <select x-model="editingLoan.repayment_months" class="w-full p-3 border rounded" required>
                            <option value="0.5">2 Weeks</option>
                            <option value="1">1 Month</option>
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="editingLoan.status" class="w-full p-3 border rounded" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditLoanModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Update Loan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeCEOCharts, 500);
        });
        
        function initializeCEOCharts() {
            const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Revenue',
                            data: [3200000, 3400000, 3600000, 3800000, 3900000, 4100000, 4200000, 4300000, 4400000, 4500000, 4600000, 4700000],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Profit',
                            data: [900000, 950000, 1000000, 1050000, 1100000, 1150000, 1200000, 1250000, 1300000, 1350000, 1400000, 1450000],
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return 'UGX ' + (value/1000000).toFixed(1) + 'M'; } }
                            }
                        }
                    }
                });
            }

            const segmentsCtx = document.getElementById('segmentsChart')?.getContext('2d');
            if (segmentsCtx) {
                new Chart(segmentsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Savings & Deposits', 'Loans & Credit', 'Investment Services', 'Insurance', 'Other Services'],
                        datasets: [{
                            data: [35, 30, 20, 10, 5],
                            backgroundColor: ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }
        function ceoDashboard() {
            return {
                showAddInitiative: false,
                newInitiative: {},
                allMembers: [],
                filteredMembers: [],
                memberSearch: '',
                filteredFinancials: [],
                financialSearch: '',
                allLoans: [],
                filteredLoans: [],
                loanSearch: '',
                loanStatusFilter: 'all',
                editingLoan: {},
                showEditLoanModal: false,
                newMember: {},
                showAddMemberModal: false,
                editingMember: {},
                showEditMemberModal: false,
                financialMember: {},
                showFinancialModal: false,
                executiveData: {
                    totalRevenue: 45200000,
                    netProfit: 12800000,
                    totalMembers: 1247
                },
                strategicInitiatives: [
                    {id: 1, title: 'Digital Transformation', description: 'Modernizing core systems', progress: 75, budget: 15000000, expectedROI: 25, status: 'on-track'},
                    {id: 2, title: 'Market Expansion', description: 'Enter new regional markets', progress: 45, budget: 8000000, expectedROI: 18, status: 'on-track'},
                    {id: 3, title: 'Product Innovation', description: 'Develop new financial products', progress: 30, budget: 12000000, expectedROI: 22, status: 'at-risk'}
                ],
                keyMetrics: [
                    {name: 'Customer Satisfaction', value: 94, target: 90, trend: 'up'},
                    {name: 'Employee Engagement', value: 87, target: 85, trend: 'up'},
                    {name: 'Operational Efficiency', value: 92, target: 88, trend: 'up'},
                    {name: 'Market Share', value: 16, target: 15, trend: 'up'}
                ],
                executiveAlerts: [
                    {id: 1, title: 'Loan Default Rate Increase', description: 'Default rate increased to 2.1%', priority: 'high', time: '2h ago'},
                    {id: 2, title: 'New Competitor Entry', description: 'Major bank entering our market', priority: 'medium', time: '4h ago'},
                    {id: 3, title: 'Regulatory Update', description: 'New compliance requirements', priority: 'medium', time: '1d ago'}
                ],
                
                init() {
                    this.loadCeoData();
                    this.loadAllData();
                },
                
                async loadAllData() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();
                        this.allMembers = data.members || [];
                        this.filteredMembers = this.allMembers;
                        this.filteredFinancials = this.allMembers;
                        this.allLoans = data.pending_loans || [];
                        this.filteredLoans = this.allLoans;
                    } catch (error) {
                        console.error('Error loading data:', error);
                    }
                },
                
                filterMembers() {
                    const search = this.memberSearch.toLowerCase();
                    this.filteredMembers = this.allMembers.filter(m => 
                        m.member_id?.toLowerCase().includes(search) ||
                        m.full_name?.toLowerCase().includes(search) ||
                        m.email?.toLowerCase().includes(search)
                    );
                },
                
                filterFinancials() {
                    const search = this.financialSearch.toLowerCase();
                    this.filteredFinancials = this.allMembers.filter(m => 
                        m.member_id?.toLowerCase().includes(search) ||
                        m.full_name?.toLowerCase().includes(search) ||
                        m.email?.toLowerCase().includes(search)
                    );
                },
                
                filterLoans() {
                    let loans = this.allLoans;
                    if (this.loanStatusFilter !== 'all') {
                        loans = loans.filter(l => l.status === this.loanStatusFilter);
                    }
                    if (this.loanSearch) {
                        const search = this.loanSearch.toLowerCase();
                        loans = loans.filter(l => 
                            l.loan_id?.toLowerCase().includes(search) ||
                            l.member_id?.toLowerCase().includes(search) ||
                            l.purpose?.toLowerCase().includes(search)
                        );
                    }
                    this.filteredLoans = loans;
                },
                
                getMemberName(memberId) {
                    const member = this.allMembers.find(m => m.member_id === memberId);
                    return member ? member.full_name : 'Unknown';
                },
                
                async approveLoan(loanId) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/loans/${loanId}/approve`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            await this.loadAllData();
                            alert('Loan approved successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to approve loan'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error approving loan');
                    }
                },
                
                async rejectLoan(loanId) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/loans/${loanId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            await this.loadAllData();
                            alert('Loan rejected successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reject loan'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error rejecting loan');
                    }
                },
                
                viewLoanDetails(loan) {
                    const memberName = this.getMemberName(loan.member_id);
                    alert(`Loan Details:\n\nLoan ID: ${loan.loan_id}\nMember: ${memberName} (${loan.member_id})\nAmount: ${this.formatCurrencyUGX(loan.amount)}\nPurpose: ${loan.purpose}\nRepayment: ${loan.repayment_months} months\nMonthly Payment: ${this.formatCurrencyUGX(loan.monthly_payment || 0)}\nStatus: ${loan.status}\nDate Applied: ${new Date(loan.created_at).toLocaleDateString()}`);
                },
                
                async deleteLoan(loanId) {
                    if (confirm('Delete this loan application? This action cannot be undone.')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/loans/${loanId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (response.ok) {
                                await this.loadAllData();
                                alert('Loan deleted successfully!');
                            }
                        } catch (error) {
                            alert('Error deleting loan');
                        }
                    }
                },
                
                editLoan(loan) {
                    this.editingLoan = {...loan};
                    this.showEditLoanModal = true;
                },
                
                async updateLoan() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/loans/${this.editingLoan.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                amount: parseFloat(this.editingLoan.amount),
                                purpose: this.editingLoan.purpose,
                                repayment_months: parseInt(this.editingLoan.repayment_months),
                                status: this.editingLoan.status
                            })
                        });
                        if (!response.ok) {
                            alert('Server error: ' + response.status);
                            return;
                        }
                        const data = await response.json();
                        if (data.success) {
                            this.showEditLoanModal = false;
                            this.editingLoan = {};
                            await this.loadAllData();
                            alert('Loan updated successfully!');
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },
                
                viewMemberDetails(member) {
                    alert(`Member: ${member.full_name}\nEmail: ${member.email}\nContact: ${member.contact}\nLocation: ${member.location}\nOccupation: ${member.occupation}\nRole: ${member.role}`);
                },
                
                addMember() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    fetch('/api/members', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(this.newMember)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showAddMemberModal = false;
                            this.newMember = {};
                            this.loadAllData();
                            alert('Member added successfully!');
                        }
                    })
                    .catch(error => alert('Error adding member'));
                },
                
                editMember(member) {
                    this.editingMember = {...member};
                    this.showEditMemberModal = true;
                },
                
                async updateMember() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/members/${this.editingMember.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                full_name: this.editingMember.full_name,
                                email: this.editingMember.email,
                                contact: this.editingMember.contact,
                                location: this.editingMember.location,
                                occupation: this.editingMember.occupation,
                                role: this.editingMember.role
                            })
                        });
                        if (!response.ok) {
                            alert('Server error: ' + response.status);
                            return;
                        }
                        const data = await response.json();
                        if (data.success) {
                            this.showEditMemberModal = false;
                            this.editingMember = {};
                            await this.loadAllData();
                            alert('Member updated successfully!');
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },
                
                async deleteMember(id) {
                    if (confirm('Delete this member? This will remove all associated data.')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/members/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (response.ok) {
                                await this.loadAllData();
                                alert('Member deleted successfully!');
                            }
                        } catch (error) {
                            alert('Error deleting member');
                        }
                    }
                },
                
                updateFinancials(member) {
                    this.financialMember = {...member};
                    this.showFinancialModal = true;
                },
                
                async saveFinancials() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/members/${this.financialMember.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                savings: parseFloat(this.financialMember.savings),
                                loan: parseFloat(this.financialMember.loan)
                            })
                        });
                        if (!response.ok) {
                            alert('Server error: ' + response.status);
                            return;
                        }
                        const data = await response.json();
                        if (data.success) {
                            this.showFinancialModal = false;
                            this.financialMember = {};
                            await this.loadAllData();
                            alert('Financial information updated successfully!');
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },
                
                async loadCeoData() {
                    try {
                        const response = await fetch('/api/ceo-data');
                        const data = await response.json();
                        
                        this.executiveData = data.executive_data;
                        this.strategicInitiatives = data.strategic_initiatives;
                        this.keyMetrics = data.key_metrics;
                        this.executiveAlerts = [
                            {id: 1, title: 'Loan Default Rate Increase', description: 'Default rate increased to 2.1%', priority: 'high', time: '2h ago'},
                            {id: 2, title: 'New Competitor Entry', description: 'Major bank entering our market', priority: 'medium', time: '4h ago'}
                        ];
                        
                        this.initCharts(data);
                    } catch (error) {
                        console.error('Error loading CEO data:', error);
                        this.initCharts();
                    }
                },
                
                formatCurrency(amount) {
                    return 'UGX ' + (amount/1000000).toFixed(1) + 'M';
                },
                
                formatCurrencyUGX(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount);
                },
                
                addInitiative() {
                    this.strategicInitiatives.push({
                        id: Date.now(),
                        ...this.newInitiative,
                        progress: 0,
                        status: 'planning'
                    });
                    this.showAddInitiative = false;
                    this.newInitiative = {};
                    alert('Strategic initiative created successfully!');
                },
                
                initCharts(data = null) {
                    // Clear existing charts
                    if (window.revenueChart) window.revenueChart.destroy();
                    if (window.segmentsChart) window.segmentsChart.destroy();
                    
                    // Revenue & Profit Chart
                    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                    
                    let revenueData = [3200000, 3400000, 3600000, 3800000, 3900000, 4100000, 4200000, 4300000, 4400000, 4500000, 4600000, 4700000];
                    let profitData = [900000, 950000, 1000000, 1050000, 1100000, 1150000, 1200000, 1250000, 1300000, 1350000, 1400000, 1450000];
                    
                    if (data && data.revenue_history) {
                        revenueData = data.revenue_history;
                        profitData = data.revenue_history.map(rev => rev * 0.28); // 28% margin
                    }
                    
                    window.revenueChart = new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: [{
                                label: 'Revenue',
                                data: revenueData,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Profit',
                                data: profitData,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'UGX ' + (value/1000000).toFixed(1) + 'M';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Business Segments Chart
                    const segmentsCtx = document.getElementById('segmentsChart').getContext('2d');
                    
                    let segmentData = [35, 30, 20, 10, 5];
                    if (data && data.business_segments) {
                        segmentData = [
                            data.business_segments.savings_deposits || 0,
                            data.business_segments.loans_credit || 0,
                            data.business_segments.investment_services || 0,
                            data.business_segments.insurance || 0,
                            data.business_segments.other_services || 0
                        ];
                    }
                    
                    window.segmentsChart = new Chart(segmentsCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Savings & Deposits', 'Loans & Credit', 'Investment Services', 'Insurance', 'Other Services'],
                            datasets: [{
                                data: segmentData,
                                backgroundColor: ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>