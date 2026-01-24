<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cashier Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/nav.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="cashierDashboard()">
    @include('navs.cashier-topnav')
    @include('navs.cashier-sidenav')

    <!-- Main Content -->
    <div class="main-content ml-0 lg:ml-36 mt-12 transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-36'">
        <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Financial Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Daily Collections</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(dailyStats.collections)">UGX 2,450,000</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+15% from yesterday</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending Loans</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="pendingLoans.length">8</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-hourglass-half text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-blue-600">
                        <span x-text="formatCurrency(pendingLoansValue)">UGX 3,200,000</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Cash Balance</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="formatCurrency(dailyStats.cashBalance)">UGX 15,750,000</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>Balanced</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Transactions</p>
                        <p class="text-2xl font-bold text-orange-600" x-text="dailyStats.transactions">47</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-exchange-alt text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-orange-600">
                        <span>Today</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Daily Transaction Flow -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Daily Transaction Flow</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
                <div style="height: 250px;">
                    <canvas id="transactionFlowChart"></canvas>
                </div>
            </div>

            <!-- Transaction Types -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Transaction Distribution</h3>
                    <span class="text-sm text-gray-500">Today</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="transactionTypesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Loan Management & Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pending Loan Applications -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Loan Applications</h3>
                    <button class="text-green-600 text-sm hover:underline">View All</button>
                </div>
                <div class="space-y-3">
                    <template x-for="loan in pendingLoans.slice(0, 5)" :key="loan.id">
                        <div class="p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium" x-text="loan.memberName">John Doe</h4>
                                    <p class="text-sm text-gray-600" x-text="loan.purpose">Business expansion</p>
                                </div>
                                <span class="text-sm font-medium text-blue-600" x-text="formatCurrency(loan.amount)">UGX 500,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500" x-text="loan.appliedDate">Applied: 2024-01-15</span>
                                <div class="space-x-2">
                                    <button @click="approveLoan(loan.id)" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
                    <button class="text-green-600 text-sm hover:underline">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Time</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Member</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Type</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="transaction in recentTransactions.slice(0, 6)" :key="transaction.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 text-sm" x-text="transaction.time">14:30</td>
                                    <td class="py-2 text-sm" x-text="transaction.member">BSS001</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded-full"
                                              :class="transaction.type === 'deposit' ? 'bg-green-100 text-green-800' :
                                                     transaction.type === 'withdrawal' ? 'bg-red-100 text-red-800' :
                                                     'bg-blue-100 text-blue-800'"
                                              x-text="transaction.type">Deposit</span>
                                    </td>
                                    <td class="py-2 text-sm font-medium"
                                        :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'"
                                        x-text="formatCurrency(transaction.amount)">UGX 100,000</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Financial Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button @click="showProcessTransaction = true" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Process Transaction
                    </button>
                    <button class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Generate Report
                    </button>
                    <button class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 flex items-center justify-center">
                        <i class="fas fa-balance-scale mr-2"></i>
                        Balance Sheet
                    </button>
                    <button class="w-full bg-orange-600 text-white p-3 rounded-lg hover:bg-orange-700 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Member Lookup
                    </button>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Financial Summary</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total Deposits</p>
                                <p class="text-xl font-bold text-green-600" x-text="formatCurrency(financialSummary.totalDeposits)">UGX 12,500,000</p>
                            </div>
                            <i class="fas fa-arrow-down text-green-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total Withdrawals</p>
                                <p class="text-xl font-bold text-red-600" x-text="formatCurrency(financialSummary.totalWithdrawals)">UGX 3,250,000</p>
                            </div>
                            <i class="fas fa-arrow-up text-red-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Loans Disbursed</p>
                                <p class="text-xl font-bold text-blue-600" x-text="formatCurrency(financialSummary.loansDisbursed)">UGX 8,750,000</p>
                            </div>
                            <i class="fas fa-hand-holding-usd text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Net Cash Flow</p>
                                <p class="text-xl font-bold text-purple-600" x-text="formatCurrency(financialSummary.netCashFlow)">UGX 9,250,000</p>
                            </div>
                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CRUD Tables Section -->
        <!-- Table 1: All Transactions Management -->
        <div class="mt-6 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">All Transactions</h3>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="transactionSearch" @input="filterTransactions()" placeholder="Search transactions..." class="px-4 py-2 border rounded-lg">
                    <button @click="showProcessTransaction = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>New Transaction
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="transaction in filteredTransactions" :key="transaction.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm" x-text="transaction.transaction_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="transaction.member_id"></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :class="{
                                              'bg-green-100 text-green-800': transaction.type === 'deposit',
                                              'bg-red-100 text-red-800': transaction.type === 'withdrawal',
                                              'bg-blue-100 text-blue-800': transaction.type === 'transfer'
                                          }"
                                          x-text="transaction.type"></span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium" :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(transaction.amount)"></td>
                                <td class="px-4 py-3 text-sm" x-text="new Date(transaction.created_at).toLocaleDateString()"></td>
                                <td class="px-4 py-3">
                                    <button @click="viewTransaction(transaction)" class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="editTransaction(transaction)" class="text-green-600 hover:text-green-800 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteTransaction(transaction.id)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table 2: Loan Applications Management -->
        <div class="mt-6 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Loan Applications Management</h3>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="loanSearch" @input="filterLoans()" placeholder="Search loans..." class="px-4 py-2 border rounded-lg">
                    <select x-model="loanStatusFilter" @change="filterLoans()" class="px-4 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Months</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="loan in filteredLoans" :key="loan.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium" x-text="loan.loan_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.member_id"></td>
                                <td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="formatCurrency(loan.amount)"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.purpose"></td>
                                <td class="px-4 py-3 text-sm" x-text="loan.repayment_months"></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800': loan.status === 'pending',
                                              'bg-green-100 text-green-800': loan.status === 'approved',
                                              'bg-red-100 text-red-800': loan.status === 'rejected'
                                          }"
                                          x-text="loan.status"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <template x-if="loan.status === 'pending'">
                                        <div class="flex space-x-2">
                                            <button @click="approveLoan(loan.id)" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Approve
                                            </button>
                                            <button @click="rejectLoan(loan.id)" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                Reject
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="loan.status !== 'pending'">
                                        <div class="flex space-x-2">
                                            <button @click="viewLoan(loan)" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button @click="editLoan(loan)" class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click="deleteLoan(loan.id)" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table 3: Member Accounts Management -->
        <div class="mt-6 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Member Accounts</h3>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="memberSearch" @input="filterMembers()" placeholder="Search members..." class="px-4 py-2 border rounded-lg">
                    <button @click="loadAllData()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Savings</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="member in filteredMembers" :key="member.member_id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium" x-text="member.member_id"></td>
                                <td class="px-4 py-3 text-sm" x-text="member.full_name"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="member.email"></td>
                                <td class="px-4 py-3 text-sm font-medium text-green-600" x-text="formatCurrency(member.savings)"></td>
                                <td class="px-4 py-3 text-sm font-medium text-red-600" x-text="formatCurrency(member.loan)"></td>
                                <td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="formatCurrency(member.balance)"></td>
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
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Total Savings: </span>
                    <span class="text-green-600 font-bold" x-text="formatCurrency(filteredMembers.reduce((sum, m) => sum + (m.savings || 0), 0))"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Transaction Modal -->
    <div x-show="showProcessTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Process Transaction</h3>
            <form @submit.prevent="processTransaction()">
                <div class="space-y-4">
                    <select x-model="newTransaction.type" class="w-full p-3 border rounded" required>
                        <option value="">Select Transaction Type</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="transfer">Transfer</option>
                    </select>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                        <input type="text"
                               x-model="memberSearchModal"
                               @input="filterMembersModal()"
                               @focus="showMemberDropdown = true"
                               placeholder="Search member..."
                               class="w-full p-3 border rounded"
                               required>
                        <div x-show="showMemberDropdown && filteredMembersModal.length > 0"
                             class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="member in filteredMembersModal" :key="member.member_id">
                                <div @click="selectMember(member)"
                                     class="p-3 hover:bg-gray-100 cursor-pointer border-b">
                                    <div class="font-medium" x-text="member.member_id"></div>
                                    <div class="text-sm text-gray-600" x-text="member.full_name"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <input type="number" x-model="newTransaction.amount" placeholder="Amount" class="w-full p-3 border rounded" required>
                    <textarea x-model="newTransaction.description" placeholder="Description" class="w-full p-3 border rounded" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showProcessTransaction = false; memberSearchModal = ''; showMemberDropdown = false; newTransaction = {};" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Process</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div x-show="showEditTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Edit Transaction</h3>
            <form @submit.prevent="updateTransaction()">
                <div class="space-y-4">
                    <input type="text" x-model="editingTransaction.member_id" placeholder="Member ID" class="w-full p-3 border rounded" required>
                    <select x-model="editingTransaction.type" class="w-full p-3 border rounded" required>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="transfer">Transfer</option>
                    </select>
                    <input type="number" x-model="editingTransaction.amount" placeholder="Amount" class="w-full p-3 border rounded" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditTransactionModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Loan Modal -->
    <div x-show="showEditLoanModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Edit Loan</h3>
            <form @submit.prevent="updateLoan()">
                <div class="space-y-4">
                    <input type="text" x-model="editingLoan.member_id" placeholder="Member ID" class="w-full p-3 border rounded" required>
                    <input type="number" x-model="editingLoan.amount" placeholder="Amount" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="editingLoan.purpose" placeholder="Purpose" class="w-full p-3 border rounded" required>
                    <input type="number" x-model="editingLoan.repayment_months" placeholder="Repayment Months" class="w-full p-3 border rounded" required>
                    <select x-model="editingLoan.status" class="w-full p-3 border rounded" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditLoanModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
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
                    <input type="number" x-model="editingMember.savings" placeholder="Savings" class="w-full p-3 border rounded" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showEditMemberModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function cashierDashboard() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                showProfileDropdown: false,
                showLogoModal: false,
                showShareholderModal: false,
                showCalendarModal: false,
                showChatModal: false,
                showMemberChatModal: false,
                showLoanRequestModal: false,
                showOpportunitiesModal: false,
                showProfileViewModal: false,
                activeLink: 'overview',
                sidebarSearch: '',
                profilePicture: null,
                showProcessTransaction: false,
                newTransaction: {},
                memberSearchModal: '',
                filteredMembersModal: [],
                showMemberDropdown: false,
                dailyStats: {
                    collections: 2450000,
                    cashBalance: 15750000,
                    transactions: 47
                },
                pendingLoans: [],
                recentTransactions: [],
                financialSummary: {
                    totalDeposits: 12500000,
                    totalWithdrawals: 3250000,
                    loansDisbursed: 8750000,
                    netCashFlow: 9250000
                },
                allTransactions: [],
                filteredTransactions: [],
                transactionSearch: '',
                allLoans: [],
                filteredLoans: [],
                loanSearch: '',
                loanStatusFilter: '',
                allMembers: [],
                filteredMembers: [],
                memberSearch: '',
                selectedTransaction: null,
                showTransactionModal: false,
                editingTransaction: {},
                showEditTransactionModal: false,
                selectedLoan: null,
                showLoanModal: false,
                editingLoan: {},
                showEditLoanModal: false,
                selectedMember: null,
                showMemberModal: false,
                editingMember: {},
                showEditMemberModal: false,

                get pendingLoansValue() {
                    return this.pendingLoans.reduce((sum, loan) => sum + loan.amount, 0);
                },

                init() {
                    this.loadCashierData();
                    this.loadAllData();
                    this.filteredMembersModal = this.allMembers;
                },

                async loadAllData() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const data = await response.json();

                        console.log('Dashboard data:', data);

                        this.allTransactions = data.recent_transactions || [];
                        this.filteredTransactions = this.allTransactions;

                        this.allLoans = data.pending_loans || [];
                        this.filteredLoans = this.allLoans;

                        this.allMembers = data.members || [];
                        this.filteredMembers = this.allMembers;
                        this.filteredMembersModal = this.allMembers;

                        console.log('Transactions:', this.allTransactions.length);
                        console.log('Loans:', this.allLoans.length);
                        console.log('Members:', this.allMembers.length);
                    } catch (error) {
                        console.error('Error loading data:', error);
                    }
                },

                filterTransactions() {
                    const search = this.transactionSearch.toLowerCase();
                    this.filteredTransactions = this.allTransactions.filter(t =>
                        t.transaction_id?.toLowerCase().includes(search) ||
                        t.member_id?.toLowerCase().includes(search) ||
                        t.type?.toLowerCase().includes(search)
                    );
                },

                filterLoans() {
                    const search = this.loanSearch.toLowerCase();
                    this.filteredLoans = this.allLoans.filter(loan => {
                        const matchesSearch = loan.loan_id?.toLowerCase().includes(search) ||
                                            loan.member_id?.toLowerCase().includes(search) ||
                                            loan.purpose?.toLowerCase().includes(search);
                        const matchesStatus = !this.loanStatusFilter || loan.status === this.loanStatusFilter;
                        return matchesSearch && matchesStatus;
                    });
                },

                filterMembers() {
                    const search = this.memberSearch.toLowerCase();
                    this.filteredMembers = this.allMembers.filter(m =>
                        m.member_id?.toLowerCase().includes(search) ||
                        m.full_name?.toLowerCase().includes(search) ||
                        m.email?.toLowerCase().includes(search)
                    );
                },

                filterMembersModal() {
                    const search = this.memberSearchModal.toLowerCase();
                    this.filteredMembersModal = this.allMembers.filter(m =>
                        m.member_id?.toLowerCase().includes(search) ||
                        m.full_name?.toLowerCase().includes(search) ||
                        m.email?.toLowerCase().includes(search)
                    );
                    this.showMemberDropdown = search.length > 0;
                },

                selectMember(member) {
                    this.newTransaction.memberId = member.member_id;
                    this.memberSearchModal = member.member_id + ' - ' + member.full_name;
                    this.showMemberDropdown = false;
                },

                viewTransaction(transaction) {
                    this.selectedTransaction = transaction;
                    this.showTransactionModal = true;
                },

                editTransaction(transaction) {
                    this.editingTransaction = {...transaction};
                    this.showEditTransactionModal = true;
                },

                async updateTransaction() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/api/transactions/${this.editingTransaction.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                member_id: this.editingTransaction.member_id,
                                type: this.editingTransaction.type,
                                amount: parseFloat(this.editingTransaction.amount),
                                description: this.editingTransaction.description || ''
                            })
                        });
                        if (!response.ok) {
                            alert('Server error: ' + response.status);
                            return;
                        }
                        const data = await response.json();
                        if (data.success) {
                            this.showEditTransactionModal = false;
                            this.editingTransaction = {};
                            await this.loadAllData();
                            await this.loadCashierData();
                            alert('Transaction updated successfully!');
                        }
                    } catch (error) {
                        alert('Error updating transaction');
                    }
                },

                async deleteTransaction(id) {
                    if (confirm('Delete this transaction?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/transactions/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (response.ok) {
                                await this.loadAllData();
                                await this.loadCashierData();
                                alert('Transaction deleted successfully!');
                            }
                        } catch (error) {
                            alert('Error deleting transaction');
                        }
                    }
                },

                viewLoan(loan) {
                    this.selectedLoan = loan;
                    this.showLoanModal = true;
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
                                member_id: this.editingLoan.member_id,
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
                            await this.loadCashierData();
                            alert('Loan updated successfully!');
                        }
                    } catch (error) {
                        alert('Error updating loan');
                    }
                },

                async deleteLoan(id) {
                    if (confirm('Delete this loan?')) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const response = await fetch(`/api/loans/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (response.ok) {
                                await this.loadAllData();
                                await this.loadCashierData();
                                alert('Loan deleted successfully!');
                            }
                        } catch (error) {
                            alert('Error deleting loan');
                        }
                    }
                },

                rejectLoan(loanId) {
                    if (confirm('Reject this loan application?')) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        fetch(`/api/loans/${loanId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.loadAllData();
                                this.loadCashierData();
                                alert('Loan rejected successfully!');
                            }
                        })
                        .catch(error => alert('Error rejecting loan'));
                    }
                },

                viewMemberDetails(member) {
                    this.selectedMember = member;
                    this.showMemberModal = true;
                },

                editMember(member) {
                    this.editingMember = {...member};
                    this.showEditMemberModal = true;
                },

                async updateMember() {
                    try {
                        if (!this.editingMember.id) {
                            alert('Error: No member ID');
                            return;
                        }

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
                                occupation: this.editingMember.occupation
                            })
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            console.error('Server response:', text);
                            alert('Server error: ' + response.status);
                            return;
                        }

                        const data = await response.json();
                        if (data.success) {
                            this.showEditMemberModal = false;
                            this.editingMember = {};
                            await this.loadAllData();
                            alert('Member updated successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Update failed'));
                        }
                    } catch (error) {
                        console.error('Full error:', error);
                        alert('Error: ' + error.message);
                    }
                },

                async deleteMember(id) {
                    if (confirm('Delete this member? This will remove all associated data.')) {
                        try {
                            await fetch(`/api/members/${id}`, { method: 'DELETE' });
                            this.loadAllData();
                            alert('Member deleted successfully!');
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error deleting member');
                        }
                    }
                },

                async loadCashierData() {
                    try {
                        const response = await fetch('/api/cashier-data');
                        const data = await response.json();

                        this.dailyStats = {
                            collections: data.daily_collections,
                            cashBalance: data.cash_balance,
                            transactions: data.daily_transactions
                        };

                        this.pendingLoans = data.pending_loans.map(loan => ({
                            id: loan.id,
                            memberName: loan.member ? loan.member.full_name : loan.member_id,
                            purpose: loan.purpose,
                            amount: loan.amount,
                            appliedDate: new Date(loan.created_at).toLocaleDateString()
                        }));

                        this.financialSummary = data.financial_summary;
                        this.initCharts(data);
                    } catch (error) {
                        console.error('Error loading cashier data:', error);
                        this.initCharts();
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount);
                },

                approveLoan(loanId) {
                    if (confirm('Approve this loan application?')) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        fetch(`/api/loans/${loanId}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.loadAllData();
                                this.loadCashierData();
                                alert('Loan approved successfully!');
                            } else {
                                alert('Error: ' + (data.message || 'Failed to approve'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error approving loan');
                        });
                    }
                },

                processTransaction() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    console.log('Processing transaction:', this.newTransaction);
                    fetch('/api/transactions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            member_id: this.newTransaction.memberId,
                            type: this.newTransaction.type,
                            amount: parseFloat(this.newTransaction.amount),
                            description: this.newTransaction.description
                        })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            this.showProcessTransaction = false;
                            this.newTransaction = {};
                            this.memberSearchModal = '';
                            this.showMemberDropdown = false;
                            this.loadAllData();
                            this.loadCashierData();
                            alert('Transaction processed successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Failed to process'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error processing transaction');
                    });
                },

                initCharts(data = null) {
                    // Clear existing charts
                    if (window.flowChart) window.flowChart.destroy();
                    if (window.typesChart) window.typesChart.destroy();

                    // Transaction Flow Chart - Use real hourly data
                    const flowCtx = document.getElementById('transactionFlowChart').getContext('2d');

                    let hourlyData = {
                        deposits: [0, 0, 0, 0, 0, 0, 0, 0],
                        withdrawals: [0, 0, 0, 0, 0, 0, 0, 0]
                    };

                    // If we have real data, use it
                    if (data && data.hourly_transactions) {
                        hourlyData = data.hourly_transactions;
                    } else {
                        // Generate realistic hourly data based on transaction patterns
                        const totalDeposits = this.recentTransactions.filter(t => t.type === 'deposit').length;
                        const totalWithdrawals = this.recentTransactions.filter(t => t.type === 'withdrawal').length;

                        // Distribute transactions across business hours (9AM-4PM)
                        const businessHours = 8;
                        const avgDepositsPerHour = Math.max(1, Math.ceil(totalDeposits / businessHours));
                        const avgWithdrawalsPerHour = Math.max(1, Math.ceil(totalWithdrawals / businessHours));

                        // Create realistic hourly distribution
                        hourlyData.deposits = [
                            Math.max(0, avgDepositsPerHour - 2), // 9AM - slow start
                            Math.max(1, avgDepositsPerHour - 1), // 10AM
                            avgDepositsPerHour + 2, // 11AM - peak
                            avgDepositsPerHour + 3, // 12PM - lunch peak
                            avgDepositsPerHour, // 1PM
                            avgDepositsPerHour + 1, // 2PM
                            avgDepositsPerHour - 1, // 3PM
                            Math.max(0, avgDepositsPerHour - 2) // 4PM - slow end
                        ];

                        hourlyData.withdrawals = [
                            Math.max(0, avgWithdrawalsPerHour - 1),
                            avgWithdrawalsPerHour,
                            avgWithdrawalsPerHour + 1,
                            avgWithdrawalsPerHour + 2, // lunch peak
                            avgWithdrawalsPerHour,
                            avgWithdrawalsPerHour + 1,
                            avgWithdrawalsPerHour,
                            Math.max(0, avgWithdrawalsPerHour - 1)
                        ];
                    }

                    window.flowChart = new Chart(flowCtx, {
                        type: 'line',
                        data: {
                            labels: ['9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM'],
                            datasets: [{
                                label: 'Deposits',
                                data: hourlyData.deposits,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Withdrawals',
                                data: hourlyData.withdrawals,
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
                                        stepSize: 1
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + ' transactions';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Transaction Types Chart
                    const typesCtx = document.getElementById('transactionTypesChart').getContext('2d');

                    let typeData = [0, 0, 0, 0];
                    let typeLabels = ['Deposits', 'Withdrawals', 'Transfers', 'Loan Payments'];

                    if (data && data.transaction_types) {
                        typeData = [
                            data.transaction_types.deposits || 0,
                            data.transaction_types.withdrawals || 0,
                            data.transaction_types.transfers || 0,
                            data.transaction_types.loan_payments || 0
                        ];
                    } else {
                        // Calculate from recent transactions
                        const deposits = this.recentTransactions.filter(t => t.type === 'deposit').length;
                        const withdrawals = this.recentTransactions.filter(t => t.type === 'withdrawal').length;
                        const transfers = this.recentTransactions.filter(t => t.type === 'transfer').length;
                        const loanPayments = this.recentTransactions.filter(t => t.type === 'loan_payment').length;

                        typeData = [deposits, withdrawals, transfers, loanPayments];
                    }

                    window.typesChart = new Chart(typesCtx, {
                        type: 'doughnut',
                        data: {
                            labels: typeLabels,
                            datasets: [{
                                data: typeData,
                                backgroundColor: ['#10B981', '#EF4444', '#3B82F6', '#8B5CF6'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                        }
                                    }
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
