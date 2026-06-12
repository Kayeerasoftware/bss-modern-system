<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'members'" id="members">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Members Management</h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Members Management</h3>
        <button @click="showAddMemberModal = true; fetchNextMemberId()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Add Member
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Member ID</th>
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Email</th>
                    <th class="text-left py-2">Role</th>
                    <th class="text-left py-2">Savings</th>
                    <th class="text-left py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="member in members" :key="member.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2" x-text="member.member_id"></td>
                        <td class="py-2" x-text="member.full_name"></td>
                        <td class="py-2" x-text="member.email"></td>
                        <td class="py-2"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs" x-text="member.role"></span></td>
                        <td class="py-2" x-text="formatCurrency(member.savings)"></td>
                        <td class="py-2">
                            <button @click="editMember(member)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                            <button @click="deleteMember(member.id)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
