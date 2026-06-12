<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'loans'" id="loans">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Loan Applications</h2>
    <h3 class="text-lg font-semibold mb-4">Loan Applications</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Loan ID</th>
                    <th class="text-left py-2">Member</th>
                    <th class="text-left py-2">Amount</th>
                    <th class="text-left py-2">Purpose</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-left py-2">Updated By</th>
                    <th class="text-left py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="loan in loans" :key="loan.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2" x-text="loan.loan_id"></td>
                        <td class="py-2" x-text="loan.member_id"></td>
                        <td class="py-2" x-text="formatCurrency(loan.amount)"></td>
                        <td class="py-2" x-text="loan.purpose"></td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded text-xs" :class="loan.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : loan.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="loan.status"></span>
                        </td>
                        <td class="py-2">
                            <span class="text-sm" x-text="loan.updated_by || '-'"></span>
                        </td>
                        <td class="py-2">
                            <button x-show="loan.status === 'pending'" @click="approveLoan(loan.id)" class="px-2 py-1 bg-green-600 text-white rounded text-xs mr-1">Approve</button>
                            <button x-show="loan.status === 'pending'" @click="rejectLoan(loan.id)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Reject</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
