<!-- Add Member Modal -->
<div x-show="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Add New Member</h3>
        <form @submit.prevent="addMember()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member ID (Auto-generated)</label>
                    <input type="text" x-model="nextMemberId" class="w-full p-3 border rounded bg-gray-100" readonly>
                </div>
                <input type="text" x-model="memberForm.full_name" placeholder="Full Name" class="w-full p-3 border rounded" autocomplete="name" required>
                <input type="email" x-model="memberForm.email" placeholder="Email" class="w-full p-3 border rounded" autocomplete="email" required>
                <input type="text" x-model="memberForm.contact" placeholder="Contact" class="w-full p-3 border rounded" autocomplete="tel" required>
                <input type="text" x-model="memberForm.location" placeholder="Location" class="w-full p-3 border rounded" autocomplete="address-level2" required>
                <input type="text" x-model="memberForm.occupation" placeholder="Occupation" class="w-full p-3 border rounded" autocomplete="organization-title" required>
                <select x-model="memberForm.role" class="w-full p-3 border rounded" required>
                    <option value="">Select Role</option>
                    <option value="client">Client</option>
                    <option value="shareholder">Shareholder</option>
                    <option value="cashier">Cashier</option>
                    <option value="td">TD</option>
                    <option value="ceo">CEO</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="showAddMemberModal = false" class="px-4 py-2 text-gray-600 border rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add Member</button>
            </div>
        </form>
    </div>
</div>