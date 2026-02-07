<!-- COMPREHENSIVE FUNDRAISING MANAGEMENT SECTION -->
<!-- Replace the existing fundraising section in admin-dashboard.blade.php with this complete version -->

<!-- Fundraising Management -->
<div class="bg-white rounded-2xl shadow-xl p-6 mb-8" x-show="activeLink === 'fundraising'" id="fundraising">
    <div class="text-center mb-6">
        <h2 class="text-4xl font-extrabold bg-gradient-to-r from-rose-600 via-pink-600 to-fuchsia-600 bg-clip-text text-transparent mb-2">Fundraising Management</h2>
        <p class="text-gray-600">Complete campaign management with contributions and expenses tracking</p>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
        <div class="bg-gradient-to-br from-rose-50 to-rose-100 p-4 rounded-lg border border-rose-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-hand-holding-heart text-rose-500 text-2xl"></i>
                <span class="px-2 py-1 bg-rose-200 text-rose-800 rounded-full text-xs font-bold" x-text="fundraisings?.length || 0">0</span>
            </div>
            <p class="text-xs text-rose-700 font-medium">Campaigns</p>
            <p class="text-lg font-bold text-rose-600" x-text="formatCurrency(fundraisings?.reduce((sum, f) => sum + parseFloat(f.target_amount || 0), 0) || 0)"></p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-bold" x-text="contributions?.length || 0">0</span>
            </div>
            <p class="text-xs text-green-700 font-medium">Total Raised</p>
            <p class="text-lg font-bold text-green-600" x-text="formatCurrency(fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0)"></p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-bold" x-text="contributions?.length || 0">0</span>
            </div>
            <p class="text-xs text-blue-700 font-medium">Contributors</p>
            <p class="text-lg font-bold text-blue-600" x-text="formatCurrency(contributions?.reduce((sum, c) => sum + parseFloat(c.amount || 0), 0) || 0)"></p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-receipt text-orange-500 text-2xl"></i>
                <span class="px-2 py-1 bg-orange-200 text-orange-800 rounded-full text-xs font-bold" x-text="expenses?.length || 0">0</span>
            </div>
            <p class="text-xs text-orange-700 font-medium">Total Expenses</p>
            <p class="text-lg font-bold text-orange-600" x-text="formatCurrency(expenses?.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0) || 0)"></p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                <span class="px-2 py-1 bg-purple-200 text-purple-800 rounded-full text-xs font-bold" x-text="Math.round((fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) / fundraisings?.reduce((sum, f) => sum + parseFloat(f.target_amount || 0), 0) * 100) || 0) + '%'">0%</span>
            </div>
            <p class="text-xs text-purple-700 font-medium">Progress</p>
            <p class="text-lg font-bold text-purple-600" x-text="formatCurrency((fundraisings?.reduce((sum, f) => sum + parseFloat(f.target_amount || 0), 0) || 0) - (fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0))"></p>
            <p class="text-xs text-purple-600">Remaining</p>
        </div>
        <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-4 rounded-lg border border-teal-200">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-wallet text-teal-500 text-2xl"></i>
                <span class="px-2 py-1 bg-teal-200 text-teal-800 rounded-full text-xs font-bold">NET</span>
            </div>
            <p class="text-xs text-teal-700 font-medium">Net Amount</p>
            <p class="text-lg font-bold text-teal-600" x-text="formatCurrency((fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0) - (expenses?.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0) || 0))"></p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 mb-6 border-b">
        <button @click="fundraisingTab = 'campaigns'" :class="fundraisingTab === 'campaigns' ? 'border-b-2 border-rose-600 text-rose-600' : 'text-gray-600'" class="px-4 py-2 font-semibold">
            <i class="fas fa-hand-holding-heart mr-2"></i>Campaigns
        </button>
        <button @click="fundraisingTab = 'contributions'" :class="fundraisingTab === 'contributions' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-600'" class="px-4 py-2 font-semibold">
            <i class="fas fa-donate mr-2"></i>Contributions
        </button>
        <button @click="fundraisingTab = 'expenses'" :class="fundraisingTab === 'expenses' ? 'border-b-2 border-orange-600 text-orange-600' : 'text-gray-600'" class="px-4 py-2 font-semibold">
            <i class="fas fa-receipt mr-2"></i>Expenses
        </button>
        <button @click="fundraisingTab = 'analytics'" :class="fundraisingTab === 'analytics' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600'" class="px-4 py-2 font-semibold">
            <i class="fas fa-chart-bar mr-2"></i>Analytics
        </button>
    </div>

    <!-- Campaigns Tab -->
    <div x-show="fundraisingTab === 'campaigns'">
        <div class="flex gap-2 mb-4">
            <button @click="showAddFundraisingModal = true" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> New Campaign
            </button>
            <button @click="exportFundraisingReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>
        
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-rose-500 to-pink-500 text-white">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm">Campaign ID</th>
                        <th class="text-left py-3 px-4 text-sm">Title</th>
                        <th class="text-left py-3 px-4 text-sm">Target</th>
                        <th class="text-left py-3 px-4 text-sm">Raised</th>
                        <th class="text-left py-3 px-4 text-sm">Expenses</th>
                        <th class="text-left py-3 px-4 text-sm">Net</th>
                        <th class="text-left py-3 px-4 text-sm">Progress</th>
                        <th class="text-left py-3 px-4 text-sm">Status</th>
                        <th class="text-left py-3 px-4 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <template x-for="(fundraising, index) in fundraisings" :key="fundraising.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-sm text-rose-600" x-text="fundraising.campaign_id"></td>
                            <td class="py-3 px-4">
                                <div class="font-semibold" x-text="fundraising.title"></div>
                                <div class="text-xs text-gray-500" x-text="fundraising.description?.substring(0, 40) + '...'"></div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-gray-800" x-text="formatCurrency(fundraising.target_amount)"></td>
                            <td class="py-3 px-4 font-semibold text-green-600" x-text="formatCurrency(fundraising.raised_amount)"></td>
                            <td class="py-3 px-4 font-semibold text-orange-600" x-text="formatCurrency(expenses?.filter(e => e.fundraising_id == fundraising.id).reduce((sum, e) => sum + parseFloat(e.amount), 0) || 0)"></td>
                            <td class="py-3 px-4 font-semibold text-teal-600" x-text="formatCurrency(fundraising.raised_amount - (expenses?.filter(e => e.fundraising_id == fundraising.id).reduce((sum, e) => sum + parseFloat(e.amount), 0) || 0))"></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-500" :style="`width: ${Math.min((fundraising.raised_amount / fundraising.target_amount) * 100, 100)}%`"></div>
                                    </div>
                                    <span class="text-xs font-semibold" x-text="Math.round((fundraising.raised_amount / fundraising.target_amount) * 100) + '%'"></span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="{
                                    'bg-green-100 text-green-800': fundraising.status === 'active',
                                    'bg-blue-100 text-blue-800': fundraising.status === 'completed',
                                    'bg-gray-100 text-gray-800': fundraising.status === 'cancelled'
                                }" x-text="fundraising.status"></span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-1">
                                    <button @click="viewFundraising(fundraising)" class="p-1.5 bg-purple-100 text-purple-600 rounded hover:bg-purple-200" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button @click="editFundraising(fundraising)" class="p-1.5 bg-blue-100 text-blue-600 rounded hover:bg-blue-200" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button @click="deleteFundraising(fundraising.id)" class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contributions Tab -->
    <div x-show="fundraisingTab === 'contributions'">
        <div class="flex gap-2 mb-4">
            <button @click="showContributionModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Record Contribution
            </button>
        </div>
        
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-green-500 to-emerald-500 text-white">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm">ID</th>
                        <th class="text-left py-3 px-4 text-sm">Campaign</th>
                        <th class="text-left py-3 px-4 text-sm">Contributor</th>
                        <th class="text-left py-3 px-4 text-sm">Amount</th>
                        <th class="text-left py-3 px-4 text-sm">Method</th>
                        <th class="text-left py-3 px-4 text-sm">Date</th>
                        <th class="text-left py-3 px-4 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <template x-for="contribution in contributions" :key="contribution.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-sm text-green-600" x-text="contribution.contribution_id"></td>
                            <td class="py-3 px-4 text-sm" x-text="fundraisings?.find(f => f.id == contribution.fundraising_id)?.title || 'N/A'"></td>
                            <td class="py-3 px-4 text-sm" x-text="contribution.contributor_name || 'Anonymous'"></td>
                            <td class="py-3 px-4 font-semibold text-green-600" x-text="formatCurrency(contribution.amount)"></td>
                            <td class="py-3 px-4 text-sm" x-text="contribution.payment_method"></td>
                            <td class="py-3 px-4 text-xs" x-text="new Date(contribution.created_at).toLocaleDateString()"></td>
                            <td class="py-3 px-4">
                                <button @click="deleteContribution(contribution.id)" class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expenses Tab -->
    <div x-show="fundraisingTab === 'expenses'">
        <div class="flex gap-2 mb-4">
            <button @click="showExpenseModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Record Expense
            </button>
        </div>
        
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-orange-500 to-amber-500 text-white">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm">ID</th>
                        <th class="text-left py-3 px-4 text-sm">Campaign</th>
                        <th class="text-left py-3 px-4 text-sm">Description</th>
                        <th class="text-left py-3 px-4 text-sm">Amount</th>
                        <th class="text-left py-3 px-4 text-sm">Category</th>
                        <th class="text-left py-3 px-4 text-sm">Date</th>
                        <th class="text-left py-3 px-4 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <template x-for="expense in expenses" :key="expense.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-sm text-orange-600" x-text="expense.expense_id"></td>
                            <td class="py-3 px-4 text-sm" x-text="fundraisings?.find(f => f.id == expense.fundraising_id)?.title || 'N/A'"></td>
                            <td class="py-3 px-4 text-sm" x-text="expense.description"></td>
                            <td class="py-3 px-4 font-semibold text-orange-600" x-text="formatCurrency(expense.amount)"></td>
                            <td class="py-3 px-4 text-sm" x-text="expense.category"></td>
                            <td class="py-3 px-4 text-xs" x-text="new Date(expense.expense_date).toLocaleDateString()"></td>
                            <td class="py-3 px-4">
                                <div class="flex gap-1">
                                    <button @click="editExpense(expense)" class="p-1.5 bg-blue-100 text-blue-600 rounded hover:bg-blue-200" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button @click="deleteExpense(expense.id)" class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analytics Tab -->
    <div x-show="fundraisingTab === 'analytics'">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Campaign Performance -->
            <div class="border rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-purple-600"></i>
                    Campaign Performance
                </h3>
                <template x-for="fundraising in fundraisings?.slice(0, 5)" :key="fundraising.id">
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span x-text="fundraising.title"></span>
                            <span class="font-semibold" x-text="Math.round((fundraising.raised_amount / fundraising.target_amount) * 100) + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500" :style="`width: ${Math.min((fundraising.raised_amount / fundraising.target_amount) * 100, 100)}%`"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Financial Summary -->
            <div class="border rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet text-teal-600"></i>
                    Financial Summary
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between p-3 bg-green-50 rounded">
                        <span class="text-sm">Total Raised</span>
                        <span class="font-bold text-green-600" x-text="formatCurrency(fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0)"></span>
                    </div>
                    <div class="flex justify-between p-3 bg-orange-50 rounded">
                        <span class="text-sm">Total Expenses</span>
                        <span class="font-bold text-orange-600" x-text="formatCurrency(expenses?.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0) || 0)"></span>
                    </div>
                    <div class="flex justify-between p-3 bg-teal-50 rounded">
                        <span class="text-sm">Net Amount</span>
                        <span class="font-bold text-teal-600" x-text="formatCurrency((fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0) - (expenses?.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0) || 0))"></span>
                    </div>
                    <div class="flex justify-between p-3 bg-purple-50 rounded">
                        <span class="text-sm">Remaining to Target</span>
                        <span class="font-bold text-purple-600" x-text="formatCurrency((fundraisings?.reduce((sum, f) => sum + parseFloat(f.target_amount || 0), 0) || 0) - (fundraisings?.reduce((sum, f) => sum + parseFloat(f.raised_amount || 0), 0) || 0))"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


            