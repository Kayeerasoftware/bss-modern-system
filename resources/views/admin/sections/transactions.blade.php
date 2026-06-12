<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'transactions'" id="transactions">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Transactions Management</h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Recent Transactions</h3>
        <button @click="showAddTransactionModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Add Transaction
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Transaction ID</th>
                    <th class="text-left py-2">Member</th>
                    <th class="text-left py-2">Type</th>
                    <th class="text-left py-2">Amount</th>
                    <th class="text-left py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="transaction in transactions" :key="transaction.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2" x-text="transaction.transaction_id"></td>
                        <td class="py-2" x-text="transaction.member_id"></td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded text-xs" :class="transaction.type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="transaction.type"></span>
                        </td>
                        <td class="py-2" x-text="formatCurrency(transaction.amount)"></td>
                        <td class="py-2" x-text="new Date(transaction.created_at).toLocaleDateString()"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
