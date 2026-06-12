<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'settings'" id="settings">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">System Settings</h2>
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-coins text-green-600 mr-2"></i>Financial Settings
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%)</label>
                <input type="number" x-model="settings.interest_rate" class="w-full p-3 border rounded-lg" step="0.1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loan Processing Fee (%)</label>
                <input type="number" x-model="settings.loan_fee" class="w-full p-3 border rounded-lg" step="0.1">
            </div>
        </div>
    </div>
    <div class="flex justify-between items-center pt-4 border-t">
        <button @click="resetSettings()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            <i class="fas fa-undo mr-2"></i>Reset
        </button>
        <button @click="updateSettings()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Save
        </button>
    </div>
</div>
